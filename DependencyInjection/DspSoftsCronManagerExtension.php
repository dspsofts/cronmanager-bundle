<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 01/11/15 15:57
 */

namespace DspSofts\CronManagerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DspSoftsCronManagerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = array();
        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }
        $container->setParameter('dsp_softs_cron_manager.logs_dir', $config['logs_dir']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('util.xml');
    }
}
