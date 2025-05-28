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

namespace IQ2i\AiMakerBundle\Bridge\Mistral;

use IQ2i\AiMakerBundle\Exception\UnsupportedSchemeException;
use IQ2i\AiMakerBundle\Provider\AbstractProviderFactory;
use IQ2i\AiMakerBundle\Provider\Dsn;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MistralProviderFactory extends AbstractProviderFactory
{
    private const string HOST = 'api.mistral.ai';

    public function __construct(
        private readonly HttpClientInterface $client,
    ) {
    }

    public function create(Dsn $dsn): MistralProvider
    {
        if ('mistral' !== $dsn->getScheme()) {
            throw new UnsupportedSchemeException($dsn, 'mistral', $this->getSupportedSchemes());
        }

        $endpoint = \preg_replace('/(^|\.)default$/', '\1'.self::HOST, $dsn->getHost());
        $endpoint .= $dsn->getPort() ? ':'.$dsn->getPort() : '';

        $client = $this->client->withOptions([
            'base_uri' => 'https://'.$endpoint.'/api/v1/',
            'auth_bearer' => $this->getUser($dsn),
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        return new MistralProvider($client, $endpoint);
    }

    protected function getSupportedSchemes(): array
    {
        return ['mistral'];
    }
}
