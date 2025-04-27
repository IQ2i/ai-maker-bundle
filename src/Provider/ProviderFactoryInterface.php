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

use IQ2i\AiMakerBundle\Exception\IncompleteDsnException;
use IQ2i\AiMakerBundle\Exception\MissingRequiredOptionException;
use IQ2i\AiMakerBundle\Exception\UnsupportedSchemeException;

interface ProviderFactoryInterface
{
    /**
     * @throws UnsupportedSchemeException
     * @throws IncompleteDsnException
     * @throws MissingRequiredOptionException
     */
    public function create(Dsn $dsn): ProviderInterface;

    public function supports(Dsn $dsn): bool;
}
