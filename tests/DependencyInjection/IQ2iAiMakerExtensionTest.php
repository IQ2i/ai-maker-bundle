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

namespace IQ2i\AiMakerBundle\Tests\DependencyInjection;

use IQ2i\AiMakerBundle\DependencyInjection\Compiler\ProviderPass;
use IQ2i\AiMakerBundle\DependencyInjection\IQ2iAiMakerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class IQ2iAiMakerExtensionTest extends TestCase
{
    public function testLoad(): void
    {
        $extension = new IQ2iAiMakerExtension();
        $container = new ContainerBuilder();

        $extension->load([
            'ai_maker' => [
                'provider' => 'mistral',
                'dsn' => 'mistral://API_KEY@default',
            ],
        ], $container);
        (new ProviderPass())->process($container);

        $this->assertTrue($container->hasDefinition('iq2i_ai_maker.provider.mistral'));

        foreach (\array_keys($container->findTaggedServiceIds('maker.command')) as $id) {
            $arguments = $container->getDefinition($id)->getArguments();
            $this->assertCount(1, $arguments);

            /** @var Reference $providerArgument */
            $providerArgument = \current($arguments);
            $this->assertInstanceOf(Reference::class, $providerArgument);
            $this->assertEquals('iq2i_ai_maker.provider.mistral', $providerArgument);
        }
    }
}
