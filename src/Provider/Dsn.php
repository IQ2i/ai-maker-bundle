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

namespace IQ2i\AiMakerBundle\Provider;

use IQ2i\AiMakerBundle\Exception\InvalidArgumentException;
use IQ2i\AiMakerBundle\Exception\MissingRequiredOptionException;

final class Dsn
{
    private readonly string $scheme;

    private readonly string $host;

    private readonly ?string $user;

    private readonly ?string $password;

    private readonly ?int $port;

    private readonly ?string $path;

    private array $options = [];

    public function __construct(#[\SensitiveParameter] private readonly string $originalDsn)
    {
        if (false === $params = \parse_url($this->originalDsn)) {
            throw new InvalidArgumentException('The AI provider DSN is invalid.');
        }

        if (!isset($params['scheme'])) {
            throw new InvalidArgumentException('The AI provider DSN must contain a scheme.');
        }

        $this->scheme = $params['scheme'];

        if (!isset($params['host'])) {
            throw new InvalidArgumentException('The AI provider DSN must contain a host (use "default" by default).');
        }

        $this->host = $params['host'];

        $this->user = '' !== ($params['user'] ?? '') ? \rawurldecode($params['user']) : null;
        $this->password = '' !== ($params['pass'] ?? '') ? \rawurldecode($params['pass']) : null;
        $this->port = $params['port'] ?? null;
        $this->path = $params['path'] ?? null;
        \parse_str($params['query'] ?? '', $this->options);
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getPort(?int $default = null): ?int
    {
        return $this->port ?? $default;
    }

    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    public function getRequiredOption(string $key): mixed
    {
        if (!\array_key_exists($key, $this->options) || '' === \trim((string) $this->options[$key])) {
            throw new MissingRequiredOptionException($key);
        }

        return $this->options[$key];
    }

    public function getBooleanOption(string $key, bool $default = false): bool
    {
        return \filter_var($this->getOption($key, $default), \FILTER_VALIDATE_BOOLEAN);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getOriginalDsn(): string
    {
        return $this->originalDsn;
    }
}
