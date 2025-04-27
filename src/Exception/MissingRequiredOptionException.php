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

class MissingRequiredOptionException extends IncompleteDsnException
{
    public function __construct(string $option, ?string $dsn = null, ?\Throwable $previous = null)
    {
        $message = \sprintf('The option "%s" is required but missing.', $option);

        parent::__construct($message, $dsn, $previous);
    }
}
