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

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to listen for Superfeedr update notifications.  Runs indefinitely;
 * most likely useful in conjunction with e.g. deamontools.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class ListenCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
                ->setName('superfeedr:listen')
                ->addOption('die', 'd', InputOption::VALUE_NONE, 'Die immediately upon failure, rather than just throwing an exception.')
                ->setDescription('Listen for notifications from Superfeedr.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $receiver = $this->container->get('hearsay_superfeedr.listener');
        $output->writeln('Listening for messages...');
        try {
            $receiver->listen();
        } catch (\Exception $exception) {
            if ($exception instanceof \Hearsay\SuperfeedrBundle\Exception\TimeoutException) {
                $this->container->get('monolog.logger.superfeedr')->warn('Superfeedr listener timed out.');
            } else {
                $this->container->get('monolog.logger.superfeedr')->err('Caught ' . \get_class($exception) . ' while listening: ' . $exception->getMessage());
            }
            if ($input->getOption('die')) {
                die($exception->getMessage() . "\n");
            } else {
                throw $exception;
            }
        }
        $output->writeln('Finished listening.');
    }

}
