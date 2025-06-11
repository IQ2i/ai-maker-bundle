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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use IQ2i\AiMakerBundle\Bridge\Mistral\MistralProviderFactory;
use IQ2i\AiMakerBundle\Maker\MakeTest;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('iq2i_ai_maker.provider_factory.mistral', MistralProviderFactory::class)
            ->tag('iq2i_ai_maker.provider_factory')
            ->args([
                service('http_client'),
            ])

        ->set(MakeTest::class)
            ->tag('maker.command')
    ;
};
