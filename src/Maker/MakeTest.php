<?php

declare(strict_types=1);

/*
 * This file is part of the AI Maker Bundle.
 *
 * (c) Loïc Sapone <loic@sapone.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\AiMakerBundle\Maker;

use IQ2i\AiMakerBundle\Message\MessageBag;
use IQ2i\AiMakerBundle\Provider\ProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class MakeTest extends AbstractMaker
{
    public function __construct(
        private readonly ProviderInterface $provider,
    ) {
    }

    public static function getCommandName(): string
    {
        return 'make:ai:test';
    }

    public static function getCommandDescription(): string
    {
        return 'Generate a new test from specified class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('path', InputArgument::REQUIRED, 'Path to a PHP class file')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Override already existing tests')
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): int
    {
        $path = (string) $input->getArgument('path');
        if (!\is_file($path) || !\str_ends_with($path, '.php')) {
            $io->error('No PHP file found.');

            return Command::FAILURE;
        }

        $io->info('Processing '.$path);

        $classCode = \file_get_contents($path);
        $dependencies = $this->extractDependencies($classCode);

        $prompt = "Here is a PHP class :\n".$classCode."\n\n"
            .'Can you generate a PHPUnit test file for this class? '
            .'Use mocks for the following dependencies if necessary:'.\implode(', ', $dependencies).'. '
            .'The test must follow good practice (clear assertions, arrange/act/assert).';

        $messageBag = new MessageBag();
        $messageBag->addSystemMessage('You are a PHP, PHPUnit and Symfony expert. You help write clean unit tests. Your reply must contain only the code requested');
        $messageBag->addUserMessage($prompt);

        $response = $this->provider->ask($messageBag);
        $choices = $response->getContent();
        $generatedTest = $choices[0]['message']['content'] ?? null;

        if (!$generatedTest) {
            $io->error('No generated test for '.$path);

            return Command::FAILURE;
        }

        $cleanTest = $this->sanitizeGeneratedCode($generatedTest);

        if ($input->getOption('preview')) {
            $io->setDecorated(false);
            $io->writeln($cleanTest);

            return Command::SUCCESS;
        }

        [$testDirectory, $testFilePath, $namespace] = $this->determineTestPathAndNamespace($path);

        if (!\is_dir($testDirectory)) {
            \mkdir($testDirectory, 0o775, true);
        }

        if (\file_exists($testFilePath) && !$input->getOption('force')) {
            $io->error(\sprintf('File %s already exists, use --force option to override it.', $testFilePath));

            return Command::FAILURE;
        }

        $cleanTestWithNamespace = $this->injectNamespace($cleanTest, $namespace);

        \file_put_contents($testFilePath, $cleanTestWithNamespace);

        $this->writeSuccessMessage($io);

        return Command::SUCCESS;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $dependencies->addClassDependency(
            TestCase::class,
            'phpunit/phpunit',
            true,
            true
        );
    }

    private function extractDependencies(string $phpCode): array
    {
        $dependencies = [];

        if (\preg_match('/function\s+__construct\s*\(([^)]*)\)/', $phpCode, $matches)) {
            $parameters = \explode(',', $matches[1]);
            foreach ($parameters as $param) {
                if (\preg_match('/\?\s*([\w\\\\]+)\s+\$/', $param, $paramMatch) || \preg_match('/([\w\\\\]+)\s+\$/', $param, $paramMatch)) {
                    $dependencies[] = $paramMatch[1];
                }
            }
        }

        return $dependencies;
    }

    private function sanitizeGeneratedCode(string $code): string
    {
        return \trim((string) \preg_replace('/```(?:php)?|```/', '', $code));
    }

    private function determineTestPathAndNamespace(string $sourcePath): array
    {
        $relativePath = \str_replace('src/', '', $sourcePath);
        $relativePath = \preg_replace('/\.php$/', '', $relativePath);

        $testDirectory = 'tests/'.\dirname((string) $relativePath).'/';
        $className = \basename((string) $relativePath);
        $testFilePath = $testDirectory.$className.'Test.php';

        $namespace = 'Tests\\'.\trim(\str_replace('/', '\\', \dirname((string) $relativePath)), '\\');

        return [$testDirectory, $testFilePath, $namespace];
    }

    private function injectNamespace(string $testContent, string $namespace): string
    {
        $namespaceLine = 'Tests\\' === $namespace || 'Tests' === $namespace ? "namespace Tests;\n" : "namespace {$namespace};\n";

        if (\preg_match('/namespace\s+[^;]+;/', $testContent)) {
            return \preg_replace('/namespace\s+[^;]+;/', $namespaceLine, $testContent, 1);
        }

        if (\preg_match('/<\?php\s*/', $testContent, $matches)) {
            return \preg_replace('/(<\?php\s*)/', "$1\n{$namespaceLine}\n", $testContent, 1);
        }

        return "<?php\n\n{$namespaceLine}\n".$testContent;
    }
}
