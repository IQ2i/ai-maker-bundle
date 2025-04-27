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

use IQ2i\AiMakerBundle\Message\MessageBagInterface;
use IQ2i\AiMakerBundle\Response\ResponseInterface;

interface ProviderInterface extends \Stringable
{
    public function ask(MessageBagInterface $messageBag): ?ResponseInterface;
}
