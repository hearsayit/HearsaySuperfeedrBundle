<?php

/*
 * The Hearsay Superfeedr bundle for Symfony2.
 * Copyright (C) 2011 Hearsay News Products, Inc.
 *
 * This program is free software; you can redistribute it and/or modify it 
 * under the terms of the GNU General Public License as published by the Free 
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more 
 * details.
 *
 * You should have received a copy of the GNU General Public License along with 
 * this program; if not, write to the Free Software Foundation, Inc., 51 
 * Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. 
 */

namespace Hearsay\SuperfeedrBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Setup for the Hearsay Superfeedr bundle.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class HearsaySuperfeedrExtension extends Extension
{
    /**
     * {@inhertidoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . "/../Resources/config"));
        $loader->load('superfeedr.xml');
        
        $processor = new Processor();

        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $config);
        
        $container->setParameter('hearsay_superfeedr.username', $config['username']);
        $container->setParameter('hearsay_superfeedr.password', $config['password']);
        $container->setParameter('hearsay_superfeedr.jaxl_log_path', $config['log_path']);
        $container->setParameter('hearsay_superfeedr.jaxl_log_level', $config['log_level']);
        
        if ($config['test']) {
            $container->setAlias('hearsay_superfeedr.subscriber', 'hearsay_superfeedr.test_subscriber');
        } else {
            $container->setAlias('hearsay_superfeedr.subscriber', 'hearsay_superfeedr.superfeedr');
        }
    }
    
}
