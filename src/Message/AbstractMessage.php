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

namespace IQ2i\AiMakerBundle\Message;

abstract class AbstractMessage implements MessageInterface
{
    public function __construct(
        protected string $content,
    ) {
    }

    abstract public function getRole(): string;

    public function getContent(): string
    {
        return $this->content;
    }

    public function jsonSerialize(): array
    {
        return [
            'role' => $this->getRole(),
            'content' => $this->getContent(),
        ];
    }
}
