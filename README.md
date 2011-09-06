Introduction
============

This bundle enables integration with the [Superfeedr](http://www.superfeedr.com)
XMPP API.  It provides commands for subscribing/unsubscribing to resources and
receiving content updates over XMPP.  It uses Nathan Fritz's XMPPHP to handle
communication with Superfeedr.

Installation
============

  1. Install the bundle:

        $ git submodule add git://github.com/hearsayit/HearsaySuperfeedrBundle vendor/bundles/Hearsay/SuperfeedrBundle

  2. Install XMPPHP.  The bundle is tested against cweiske's fork:
      
        $ git submodule add git://github.com/cweiske/xmpphp.git vendor/xmpphp

  3. Add the Hearsay namespace and the XMPPHP prefix to your autoloader:

        // app/autoload.php
        $loader->registerNamespaces(array(
            // ...
            'Hearsay' => __DIR__.'/../vendor/bundles',
            // ...
        ));

  4. Add the bundle to your kernel:
        
        // app/AppKernel.php
        public function registerBundles()
        {
            return array(
                // ...
                new HearsaySuperfeedrBundle(),
                // ...
            );
        }

  5. Configure the bundle:
        
        # app/config/config.yml
        hearsay_superfeedr:
            username:           superfeedr_username
            password:           superfeedr_password

Usage
=====

Typical usage is through the command line:

        $ app/console superfeedr:subscribe http://superfeedr.com/dummy.xml
        $ app/console superfeedr:unsubscribe http://superfeedr.com/dummy.xml
        $ app/console superfeedr:listen

To debug full payloads or suppress all output, the --verbose or --quiet options
(respectively) can be passed to ``superfeedr:listen``.

Interaction services are also available in the DIC:

        /* @var $subscriber Hearsay\SuperfeedrBundle\Subscription\SubscriberInterface */
        $subscriber = $container->get('hearsay_superfeedr.subscriber');
        $subscriber->subscribeTo('http://superfeedr.com/dummy.xml');

        /* @var $listener Hearsay\SuperfeedrBundle\Listening\ListenerInterface */
        $listener = $container->get('hearsay_superfeedr.listener');
        $listener->listen();

Testing
=======

In your testing environment, you may wish to disable live subscription:
        
        # app/config/config_test.yml        
        hearsay_superfeedr:
            subscription_adapter: test

Information about executed subscribe and unsubscribe requests is available in
the profiler:
        
        class SubscribeControllerTest extends WebTestCase
        {
            public function testSubscribe()
            {
                $client = $this->createClient();
                $crawler = $client->request('GET', '/admin/susbscribe?url=http://superfeedr.com/dummy.xml');

                if ($profile = $client->getProfile()) {
                    $this->assertEquals(1, $profile->getCollector('superfeedr')->getSubscribeAttemptCount());
                }
            }
        }

Additional configuration
========================

If you want to be more aggressive about avoiding network hangups while listening
for notifications, you can specify a timeout:

        # app/config/config.yml
        hearsay_superfeedr:
            # ...
            listener_timeout: 300 # (seconds)

The listener then throws a ``Hearsay\SuperfeedrBundle\Exception\TimeoutException``
if no notifications are received within the timeout period.  Note that this will
result in timeouts under normal circumstances when notifications are simply
sparse.  Used in combination with a service supervisor like 
[daemontools](http://cr.yp.to/daemontools.html)' ``supervise``, however, this 
can be a good way to ensure that your production listener doesn't hang up
indefinitely.
