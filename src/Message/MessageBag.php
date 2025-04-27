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

class MessageBag implements MessageBagInterface
{
    /** @var MessageInterface[] */
    private array $messages = [];

    public function addSystemMessage(string $content): static
    {
        $this->messages[] = new SystemMessage($content);

        return $this;
    }

    public function addUserMessage(string $content): static
    {
        $this->messages[] = new UserMessage($content);

        return $this;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function count(): int
    {
        return \count($this->messages);
    }

    public function jsonSerialize(): array
    {
        return $this->messages;
    }
}
