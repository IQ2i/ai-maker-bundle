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

namespace IQ2i\AiMakerBundle\Response;

readonly class ChoiceResponse implements ResponseInterface
{
    /**
     * @param Choice[] $choices
     */
    public function __construct(
        private array $choices,
    ) {
    }

    public function getContent(): array
    {
        return $this->choices;
    }
}
