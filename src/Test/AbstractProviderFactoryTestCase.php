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

namespace IQ2i\AiMakerBundle\Test;

use IQ2i\AiMakerBundle\Exception\IncompleteDsnException;
use IQ2i\AiMakerBundle\Exception\UnsupportedSchemeException;
use IQ2i\AiMakerBundle\Provider\Dsn;
use IQ2i\AiMakerBundle\Provider\ProviderFactoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

abstract class AbstractProviderFactoryTestCase extends TestCase
{
    abstract public function createFactory(): ProviderFactoryInterface;

    abstract public static function supportsProvider(): iterable;

    abstract public static function createProvider(): iterable;

    abstract public static function unsupportedSchemeProvider(): iterable;

    abstract public static function incompleteDsnProvider(): iterable;

    #[DataProvider('supportsProvider')]
    public function testSupports(bool $expected, string $dsn)
    {
        $factory = $this->createFactory();

        $this->assertSame($expected, $factory->supports(new Dsn($dsn)));
    }

    #[DataProvider('createProvider')]
    public function testCreate(string $expected, string $dsn)
    {
        $factory = $this->createFactory();
        $provider = $factory->create(new Dsn($dsn));

        $this->assertSame($expected, (string) $provider);
    }

    #[DataProvider('unsupportedSchemeProvider')]
    public function testUnsupportedSchemeException(string $dsn, ?string $message = null)
    {
        $factory = $this->createFactory();

        $dsn = new Dsn($dsn);

        $this->expectException(UnsupportedSchemeException::class);
        if (null !== $message) {
            $this->expectExceptionMessage($message);
        }

        $factory->create($dsn);
    }

    #[DataProvider('incompleteDsnProvider')]
    public function testIncompleteDsnException(string $dsn, ?string $message = null)
    {
        $factory = $this->createFactory();

        $dsn = new Dsn($dsn);

        $this->expectException(IncompleteDsnException::class);
        if (null !== $message) {
            $this->expectExceptionMessage($message);
        }

        $factory->create($dsn);
    }
}
