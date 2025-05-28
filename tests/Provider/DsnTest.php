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

namespace IQ2i\AiMakerBundle\Tests\Provider;

use IQ2i\AiMakerBundle\Exception\InvalidArgumentException;
use IQ2i\AiMakerBundle\Provider\Dsn;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DsnTest extends TestCase
{
    #[DataProvider('constructProvider')]
    public function testConstruct(string $dsnString, string $scheme, string $host, ?string $user = null, ?string $password = null, ?int $port = null, array $options = [], ?string $path = null)
    {
        $dsn = new Dsn($dsnString);
        $this->assertSame($dsnString, $dsn->getOriginalDsn());

        $this->assertSame($scheme, $dsn->getScheme());
        $this->assertSame($host, $dsn->getHost());
        $this->assertSame($user, $dsn->getUser());
        $this->assertSame($password, $dsn->getPassword());
        $this->assertSame($port, $dsn->getPort());
        $this->assertSame($path, $dsn->getPath());
        $this->assertSame($options, $dsn->getOptions());
    }

    public static function constructProvider(): iterable
    {
        yield 'simple dsn' => [
            'scheme://localhost',
            'scheme',
            'localhost',
        ];

        yield 'simple dsn including @ sign, but no user/password/token' => [
            'scheme://@localhost',
            'scheme',
            'localhost',
        ];

        yield 'simple dsn including : sign and @ sign, but no user/password/token' => [
            'scheme://:@localhost',
            'scheme',
            'localhost',
        ];

        yield 'simple dsn including user, : sign and @ sign, but no password' => [
            'scheme://user1:@localhost',
            'scheme',
            'localhost',
            'user1',
        ];

        yield 'simple dsn including : sign, password, and @ sign, but no user' => [
            'scheme://:pass@localhost',
            'scheme',
            'localhost',
            null,
            'pass',
        ];

        yield 'dsn with user and pass' => [
            'scheme://u$er:pa$s@localhost',
            'scheme',
            'localhost',
            'u$er',
            'pa$s',
        ];

        yield 'dsn with user and pass and custom port' => [
            'scheme://u$er:pa$s@localhost:8000',
            'scheme',
            'localhost',
            'u$er',
            'pa$s',
            8000,
        ];

        yield 'dsn with user and pass, custom port and custom path' => [
            'scheme://u$er:pa$s@localhost:8000/channel',
            'scheme',
            'localhost',
            'u$er',
            'pa$s',
            8000,
            [],
            '/channel',
        ];

        yield 'dsn with user and pass, custom port, custom path and custom option' => [
            'scheme://u$er:pa$s@localhost:8000/channel?from=FROM',
            'scheme',
            'localhost',
            'u$er',
            'pa$s',
            8000,
            [
                'from' => 'FROM',
            ],
            '/channel',
        ];

        yield 'dsn with user and pass, custom port, custom path and custom options' => [
            'scheme://u$er:pa$s@localhost:8000/channel?from=FROM&to=TO',
            'scheme',
            'localhost',
            'u$er',
            'pa$s',
            8000,
            [
                'from' => 'FROM',
                'to' => 'TO',
            ],
            '/channel',
        ];

        yield 'dsn with user and pass, custom port, custom path and custom options and custom options keep the same order' => [
            'scheme://u$er:pa$s@localhost:8000/channel?to=TO&from=FROM',
            'scheme',
            'localhost',
            'u$er',
            'pa$s',
            8000,
            [
                'to' => 'TO',
                'from' => 'FROM',
            ],
            '/channel',
        ];
    }

    #[DataProvider('invalidDsnProvider')]
    public function testInvalidDsn(string $dsnString, string $exceptionMessage)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);

        new Dsn($dsnString);
    }

    public static function invalidDsnProvider(): iterable
    {
        yield [
            'some://',
            'The AI provider DSN is invalid.',
        ];

        yield [
            '//mistral',
            'The AI provider DSN must contain a scheme.',
        ];

        yield [
            'file:///some/path',
            'The AI provider DSN must contain a host (use "default" by default).',
        ];
    }
}
