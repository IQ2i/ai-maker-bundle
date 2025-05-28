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

namespace IQ2i\AiMakerBundle\Tests\Bridge\Mistral;

use IQ2i\AiMakerBundle\Bridge\Mistral\MistralProviderFactory;
use IQ2i\AiMakerBundle\Message\MessageBag;
use IQ2i\AiMakerBundle\Provider\Dsn;
use IQ2i\AiMakerBundle\Provider\ProviderFactoryInterface;
use IQ2i\AiMakerBundle\Test\AbstractProviderFactoryTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

class MistralProviderFactoryTest extends AbstractProviderFactoryTestCase
{
    public function createFactory(): ProviderFactoryInterface
    {
        return new MistralProviderFactory(new MockHttpClient());
    }

    public static function supportsProvider(): iterable
    {
        yield [true, 'mistral://API_KEY@default'];
        yield [false, 'somethingElse://API_KEY@default'];
    }

    public static function createProvider(): iterable
    {
        yield [
            'mistral://api.mistral.ai',
            'mistral://API_KEY@default',
        ];
    }

    public static function unsupportedSchemeProvider(): iterable
    {
        yield ['somethingElse://API_KEY@default'];
    }

    public static function incompleteDsnProvider(): iterable
    {
        yield ['mistral://default'];
    }

    public function testBaseUri()
    {
        $response = new JsonMockResponse(['choices' => []]);
        $httpClient = new MockHttpClient([$response]);
        $factory = new MistralProviderFactory($httpClient);
        $provider = $factory->create(new Dsn('mistral://API_KEY@default'));

        $messageBag = new MessageBag();
        $messageBag->addUserMessage('Hello, how are you?');

        $provider->ask($messageBag);

        $this->assertMatchesRegularExpression('/https:\/\/api.mistral.ai\/api\/v1\/chat\/completions\/*/', $response->getRequestUrl());
    }
}
