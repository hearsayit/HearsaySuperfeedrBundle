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

namespace Hearsay\SuperfeedrBundle\Listening;

/**
 * Interface for classes which can receive Superfeedr notifications.
 * @author Kevin Montag <kevin@hearsay.it>
 */
interface ListenerInterface
{

    /**
     * Listen indefinitely for incoming messages.
     * @throws Hearsay\SuperfeedrBundle\Exception\TimeoutException If the
     * connection appears to be lost.
     */
    public function listen();
    
    /**
     * Add a notification handler to be invoked when an XMPP message is
     * received.
     * @param NotificationHandlerInterface $handler The handler.
     */
    public function addNotificationHandler(NotificationHandlerInterface $handler);
}
