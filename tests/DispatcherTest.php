<?php

/**
 * Class DispatcherTest
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function canAddListener()
    {
        $dispatcher = new \Venta\Event\Dispatcher;

        $dispatcher->observe('test', function () {});

        $this->assertTrue($dispatcher->hasObservers('test'));
        $this->assertCount(1, $dispatcher->getObservers('test'));
    }

    /**
     * @test
     */
    public function canFireEvent()
    {
        $dispatcher = new \Venta\Event\Dispatcher;
        $data = new class {};

        $dispatcher->observe('test', function (\Venta\Contracts\Event\EventContract $event) {
            $event->getData('dataClass')->foo = 'baz';
        }, 2);

        $dispatcher->observe('test', function (\Venta\Contracts\Event\EventContract $event) {
            $event->getData('dataClass')->foo = 'bar';
            $event->getData('dataClass')->bar = 'foo';
        }, 10);

        $dispatcher->observe('test', new class extends \Venta\Event\Observer {
            public function handle(\Venta\Contracts\Event\EventContract $event)
            {
                $event->getData('dataClass')->foo = '123';
            }
        });

        $event = $dispatcher->dispatch('test', ['dataClass' => $data]);

        $this->assertEquals('123', $event->getData('dataClass')->foo);
        $this->assertEquals('foo', $event->getData('dataClass')->bar);
        $this->assertEquals('test', $event->getName());
    }

    /**
     * @test
     */
    public function canStopEvents()
    {
        $dispatcher = new \Venta\Event\Dispatcher;

        $dispatcher->observe('test', function(\Venta\Contracts\Event\EventContract $event){
            $event->setData('string', 'one');
        });

        $dispatcher->observe('test', function(\Venta\Contracts\Event\EventContract $event){
            $event->stop();
        });

        $dispatcher->observe('test', function(\Venta\Contracts\Event\EventContract $event){
            $event->setData('string', 'two');
        });

        $event = $dispatcher->dispatch('test', ['string' => null]);

        $this->assertEquals('one', $event->getData('string'));
        $this->assertEquals(['string' => 'one'], $event->getData());
    }

    /**
     * @test
     */
    public function canUseTrait()
    {
        $dispatcher = new \Venta\Event\Dispatcher;
        $instance = new class {
            use \Venta\Event\Traits\EventDispatcherAwareTrait;
        };

        $this->assertInstanceOf(\Venta\Contracts\Event\DispatcherContract::class, $instance->getEventsDispatcher());

        $instance->setEventsDispatcher($dispatcher);
        $this->assertSame($dispatcher, $instance->getEventsDispatcher());
    }

    /**
     * @test
     */
    public function canMakePerStepCallback()
    {
        $dispatcher = new \Venta\Event\Dispatcher;

        $dispatcher->observe('test', function($event) {
            $event->setData('integer', $event->getData('integer') + 1);
        });

        $dispatcher->observe('test', function($event) {
            $event->setData('integer', $event->getData('integer') + 5);
        });

        $event = $dispatcher->dispatch('test', ['integer' => 10], function($event) {
            $event->setData('integer', $event->getData('integer') + 10);
        });

        $this->assertEquals(36, $event->getData('integer'));
    }
}