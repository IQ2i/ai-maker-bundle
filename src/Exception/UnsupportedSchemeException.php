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

use IQ2i\AiMakerBundle\Provider\Dsn;

class UnsupportedSchemeException extends LogicException
{
    public function __construct(Dsn $dsn, ?string $name = null, array $supported = [])
    {
        $message = \sprintf('The "%s" scheme is not supported', $dsn->getScheme());
        if ($name && $supported) {
            $message .= \sprintf('; supported schemes for translation provider "%s" are: "%s"', $name, \implode('", "', $supported));
        }

        parent::__construct($message.'.');
    }
}
