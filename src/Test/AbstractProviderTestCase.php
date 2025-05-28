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

use IQ2i\AiMakerBundle\Provider\ProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractProviderTestCase extends TestCase
{
    abstract public static function createProvider(HttpClientInterface $client, string $endpoint): ProviderInterface;
}
