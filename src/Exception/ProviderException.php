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

namespace IQ2i\AiMakerBundle\Exception;

use Symfony\Contracts\HttpClient\ResponseInterface;

class ProviderException extends RuntimeException implements ProviderExceptionInterface
{
    private string $debug = '';

    public function __construct(
        string $message,
        private readonly ResponseInterface $response,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $this->debug .= $response->getInfo('debug') ?? '';

        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getDebug(): string
    {
        return $this->debug;
    }
}
