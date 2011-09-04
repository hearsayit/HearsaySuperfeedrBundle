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

namespace Hearsay\SuperfeedrBundle\Tests\Command;

use Hearsay\SuperfeedrBundle\Command\SubscribeCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Unit tests for the subscribe command.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscribeCommandTest extends WebTestCase
{
    public function testSubscribed()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $application = new Application($kernel);
        $application->add(new SubscribeCommand());
        
        // Make sure we're not testing in a live setting
        $container = $kernel->getContainer();
        if (!($container->getParameter('hearsay_superfeedr.subscription_adapter.type') === 'test')) {
            $this->markTestSkipped('Cannot test Superfeedr susbcriptions in a live setting');
        }
        
        $command = $application->find('superfeedr:subscribe');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => 'superfeedr:subscribe', 'url' => 'http://www.google.com'));
        
        
    }
    
    public function testSubscribedWithDigest()
    {
        
    }
}
