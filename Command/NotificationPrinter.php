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

use Hearsay\SuperfeedrBundle\Listening\NotificationHandlerInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Helper service to print notifications as they arrive.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class NotificationPrinter implements NotificationHandlerInterface
{
    /**
     * @var OutputInterface
     */
    protected $output = null;
    
    /**
     * Standard constructor.
     * @param OutputInterface $output Output to display notifications.
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }
    
    /**
     * {@inheritdoc}
     */
    public function handleNotification($payload)
    {        
        $xml = simplexml_load_string($payload);
        $url = (string) ($xml->status->attributes()->feed);
        
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln(sprintf('Received notification from <comment>"%s"</comment>.', $url));
        }
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln('<comment>Payload was:</comment>');
            $this->output->writeln($payload);
            $this->output->writeln('<comment>------------------</comment>');
        }
    }
}
