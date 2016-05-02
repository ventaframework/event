<?php declare(strict_types = 1);

namespace Venta\Event;

use Ds\Map;
use Venta\Contracts\Event\DispatcherContract;
use Venta\Contracts\Event\EventContract;

/**
 * Class Dispatcher
 *
 * @package Venta\Event
 */
class Dispatcher implements DispatcherContract
{
    /**
     * Events holder
     *
     * @var Map
     */
    protected $_events;

    /**
     * Construct function
     */
    public function __construct()
    {
        $this->_events = new Map;
    }

    /**
     * {@inheritdoc}
     */
    public function observe(string $name, callable $listener, int $priority = 0)
    {
        $this->_getEvent($name)->addObserver($listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(string $name, array $arguments = [], callable $stepCallback = null): EventContract
    {
        $event = $this->_prepareEventForDispatch($name, $arguments);

        foreach ($event->getObservers() as $observer) {
            $observer($event);

            if ($stepCallback !== null) {
                $stepCallback($event);
            }

            if ($event->wasStopped()) {
                break;
            }
        }

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function getObservers(string $name): array
    {
        return $this->_getEvent($name)->getObservers();
    }

    /**
     * {@inheritdoc}
     */
    public function hasObservers(string $name): bool
    {
        return $this->_getEvent($name)->hasObservers() > 0;
    }

    /**
     * Returns event by its name
     *
     * @param string $name
     * @return EventContract
     */
    protected function _getEvent(string $name): EventContract
    {
        if (!$this->_events->hasKey($name)) {
            $this->_events->put($name, new Event($name));
        }

        return $this->_events->get($name);
    }

    /**
     * Prepares event for dispatching
     *
     * @param string $name
     * @param array $arguments
     * @return EventContract
     */
    protected function _prepareEventForDispatch(string $name, array $arguments): EventContract
    {
        foreach ($arguments as $key => $argument) {
            $this->_getEvent($name)->setData($key, $argument);
        }

        return $this->_getEvent($name);
    }
}