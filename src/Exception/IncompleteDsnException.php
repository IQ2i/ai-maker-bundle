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

class IncompleteDsnException extends InvalidArgumentException
{
    public function __construct(
        string $message,
        private readonly ?string $dsn = null,
        ?\Throwable $previous = null,
    ) {
        if ($dsn) {
            $message = \sprintf('Invalid "%s" AI provider DSN: %s', $dsn, $message);
        }

        parent::__construct($message, 0, $previous);
    }

    public function getOriginalDsn(): string
    {
        return $this->dsn;
    }
}
