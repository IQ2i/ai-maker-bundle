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

use IQ2i\AiMakerBundle\Message\SystemMessage;
use PHPUnit\Framework\TestCase;

class SystemMessageTest extends TestCase
{
    public function testCreateNewMessage(): void
    {
        $message = new SystemMessage('foo');

        self::assertSame('system', $message->getRole());
        self::assertSame('foo', $message->getContent());
    }

    public function testJsonSerialization(): void
    {
        $message = new SystemMessage('foo');

        self::assertSame(['role' => 'system', 'content' => 'foo'], $message->jsonSerialize());
    }
}
