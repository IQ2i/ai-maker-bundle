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

namespace IQ2i\AiMakerBundle\Tests\Message;

use IQ2i\AiMakerBundle\Message\MessageBag;
use PHPUnit\Framework\TestCase;

class MessageBagTest extends TestCase
{
    public function testAddMessage(): void
    {
        $messageBag = new MessageBag();
        $messageBag->addSystemMessage('This is a system message');
        $messageBag->addUserMessage('This is a user message');

        self::assertCount(2, $messageBag);
    }

    public function testJsonSerialization(): void
    {
        $messageBag = new MessageBag();
        $messageBag->addSystemMessage('This is a system message');
        $messageBag->addUserMessage('This is a user message');

        $json = \json_encode($messageBag);

        self::assertJson($json);
        self::assertJsonStringEqualsJsonString(\json_encode([
            ['role' => 'system', 'content' => 'This is a system message'],
            ['role' => 'user', 'content' => 'This is a user message'],
        ]), $json);
    }
}
