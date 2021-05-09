<?php

namespace Flasher\Pnotify\Symfony\Tests\DependencyInjection;

use Flasher\Pnotify\Symfony\DependencyInjection\FlasherPnotifyExtension;
use Flasher\Pnotify\Symfony\FlasherPnotifySymfonyBundle;
use Flasher\Prime\Tests\TestCase;
use Flasher\Symfony\DependencyInjection\FlasherExtension;
use Flasher\Symfony\FlasherSymfonyBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FlasherPnotifyExtensionTest extends TestCase
{
    public function testContainerContainFlasherServices()
    {
        $container = $this->getContainer();

        $this->assertTrue($container->has('flasher.pnotify'));
    }

    public function testCreateInstanceOfPnotifyAdapter()
    {
        $container = $this->getContainer();

        $flasher = $container->getDefinition('flasher');
        $calls = $flasher->getMethodCalls();

        $this->assertCount(2, $calls);

        $this->assertSame('addFactory', $calls[0][0]);
        $this->assertSame('template', $calls[0][1][0]);
        $this->assertSame('flasher.notification_factory', (string) $calls[0][1][1]);

        $this->assertSame('addFactory', $calls[1][0]);
        $this->assertSame('pnotify', $calls[1][1][0]);
        $this->assertSame('flasher.pnotify', (string) $calls[1][1][1]);
    }

    private function getRawContainer()
    {
        $container = new ContainerBuilder();

        $container->registerExtension(new FlasherExtension());
        $flasherBundle = new FlasherSymfonyBundle();
        $flasherBundle->build($container);

        $container->registerExtension(new FlasherPnotifyExtension());
        $adapterBundle = new FlasherPnotifySymfonyBundle();
        $adapterBundle->build($container);

        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->getCompilerPassConfig()->setAfterRemovingPasses(array());

        return $container;
    }

    private function getContainer()
    {
        $container = $this->getRawContainer();
        $container->loadFromExtension('flasher', array());
        $container->loadFromExtension('flasher_pnotify', array());
        $container->compile();

        return $container;
    }
}
