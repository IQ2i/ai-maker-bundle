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

namespace IQ2i\AiMakerBundle\DependencyInjection;

use IQ2i\AiMakerBundle\Provider\ProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class IQ2iAiMakerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.php');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $providerName = $config['provider'];

        $providerDefinition = (new Definition(ProviderInterface::class))
            ->addTag('iq2i_ai_maker.provider')
            ->setFactory([new Reference('iq2i_ai_maker.provider_factory.'.$providerName), 'create'])
            ->setArguments([$config['dsn']]);
        $container->setDefinition('iq2i_ai_maker.provider.'.$providerName, $providerDefinition);
    }
}
