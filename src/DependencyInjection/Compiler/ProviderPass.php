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

namespace IQ2i\AiMakerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $providers = $container->findTaggedServiceIds('iq2i_ai_maker.provider');
        $provider = \current(\array_keys($providers));

        foreach (array_keys($container->findTaggedServiceIds('maker.command')) as $id) {
            $container->getDefinition($id)
                ->setArgument(0, new Reference($provider));
        }
    }
}
