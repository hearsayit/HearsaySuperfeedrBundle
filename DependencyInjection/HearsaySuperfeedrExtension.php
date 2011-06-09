<?php

/*
 * Copyright (c) 2011 Hearsay News Products, Inc.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
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
            $container->setAlias('hearsay_superfeedr.subscriber', 'hearsay_superfeedr.live_subscriber');
        }
    }
    
}
