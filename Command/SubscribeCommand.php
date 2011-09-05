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

namespace Hearsay\SuperfeedrBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to subscribe to a feed.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscribeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
                ->setName('superfeedr:subscribe')
                ->setDescription('Subscribe to a resource on Superfeedr.')
                ->addArgument('url', InputArgument::REQUIRED, 'The resource URL to subscribe to.')
                ->addOption('digest', 'd', InputOption::VALUE_NONE, 'Subscribe for digest notifications on this feed.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $digest = $input->getOption('digest');

        // Just hand this off to the subscriber service
        $success = $this->getContainer()->get('hearsay_superfeedr.subscriber')->subscribeTo($url, $digest);
        
        if ($success) {
            $output->writeln(sprintf('Successfully subscribed to <info>"%s"</info>.', $url));
            return 0;
        } else {
            $output->writeln(sprintf('<error>There was a problem subscribing to "%s".</error>', $url));
            return 1;
        }
    }
}
