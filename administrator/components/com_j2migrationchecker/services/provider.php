<?php
/**
 * @copyright Copyright (C) 2024-26 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

defined('_JEXEC') or die();

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use J2Commerce\Component\J2migrationchecker\Administrator\Extension\J2migrationcheckerComponent;

/**
 * The service provider
 */
return new class() implements ServiceProviderInterface {

    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     */
    public function register(Container $container)
    {
        $componentNamespace = '\\J2Commerce\\Component\\J2migrationchecker';

        $container->registerServiceProvider(new MVCFactory($componentNamespace));
        $container->registerServiceProvider(new ComponentDispatcherFactory($componentNamespace));

        $container->set(ComponentInterface::class, function (Container $container) {
            $component = new J2migrationcheckerComponent($container->get(ComponentDispatcherFactoryInterface::class));

            $component->setRegistry($container->get(Registry::class));
            $component->setMVCFactory($container->get(MVCFactoryInterface::class));

            return $component;
        });
    }
};
