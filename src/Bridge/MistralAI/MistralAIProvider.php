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

namespace IQ2i\AiMakerBundle\Bridge\MistralAI;

use IQ2i\AiMakerBundle\Exception\ProviderException;
use IQ2i\AiMakerBundle\Exception\RuntimeException;
use IQ2i\AiMakerBundle\Message\MessageBagInterface;
use IQ2i\AiMakerBundle\Provider\ProviderInterface;
use IQ2i\AiMakerBundle\Response\Choice;
use IQ2i\AiMakerBundle\Response\ChoiceResponse;
use IQ2i\AiMakerBundle\Response\ResponseInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class MistralAIProvider implements ProviderInterface
{
    public function __construct(
        private HttpClientInterface $client,
        private string $endpoint,
    ) {
    }

    public function ask(MessageBagInterface $messageBag): ?ResponseInterface
    {
        $response = $this->client->request('POST', 'chat/completions', [
            'json' => [
                'model' => 'mistral-small-latest',
                'temperature' => 0.2,
                'messages' => $messageBag->getMessages(),
            ],
        ]);

        try {
            $data = $response->toArray();
        } catch (ClientExceptionInterface $clientException) {
            $data = $response->toArray(throw: false);

            if (isset($data['error']['code']) && 'content_filter' === $data['error']['code']) {
                throw new ProviderException($data['error']['message'], $response, previous: $clientException);
            }

            throw $clientException;
        }

        if (!isset($data['choices'])) {
            throw new RuntimeException('Response does not contain choices');
        }

        /** @var Choice[] $choices */
        $choices = \array_map($this->convertChoice(...), $data['choices']);

        return new ChoiceResponse($choices);
    }

    public function __toString(): string
    {
        return \sprintf('mistralai://%s', $this->endpoint);
    }

    private function convertChoice(array $choice): Choice
    {
        if (\in_array($choice['finish_reason'], ['stop', 'length'], true)) {
            return new Choice($choice['message']['content']);
        }

        throw new RuntimeException(\sprintf('Unsupported finish reason "%s".', $choice['finish_reason']));
    }
}
