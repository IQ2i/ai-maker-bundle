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

use IQ2i\AiMakerBundle\Bridge\Mistral\MistralProvider;
use IQ2i\AiMakerBundle\Message\MessageBag;
use IQ2i\AiMakerBundle\Provider\ProviderInterface;
use IQ2i\AiMakerBundle\Test\AbstractProviderTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MistralProviderTest extends AbstractProviderTestCase
{
    public static function createProvider(HttpClientInterface $client, string $endpoint): ProviderInterface
    {
        return new MistralProvider($client, $endpoint);
    }

    public static function toStringProvider(): iterable
    {
        yield [
            self::createProvider((new MockHttpClient())->withOptions([
                'base_uri' => 'https://api.mistral.ai/api/v1/',
                'auth_bearer' => 'API_TOKEN',
            ]), 'api.mistral.ai'),
            'mistral://api.mistral.ai',
        ];
    }

    public function testAskComplete(): void
    {
        $response = [
            function (string $method, string $url, array $options = []): ResponseInterface {
                $this->assertSame('POST', $method);
                $this->assertSame('https://api.mistral.ai/api/v1/chat/completions', $url);
                $this->assertSame('Authorization: Bearer API_TOKEN', $options['normalized_headers']['authorization'][0]);

                return new JsonMockResponse([
                    'choices' => [
                        [
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Hello world',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ]);
            },
        ];

        $provider = self::createProvider((new MockHttpClient($response))->withOptions([
            'base_uri' => 'https://api.mistral.ai/api/v1/',
            'auth_bearer' => 'API_TOKEN',
        ]), 'api.mistral.ai');

        $messageBag = new MessageBag();
        $messageBag->addUserMessage('Hello world');

        $provider->ask($messageBag);
    }
}
