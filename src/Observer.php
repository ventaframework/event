<?php declare(strict_types = 1);

namespace Venta\Event;

use Venta\Contracts\Event\EventContract;
use Venta\Contracts\Event\ObserverContract;

/**
 * Class Observer
 *
 * @package Venta\Event
 */
abstract class Observer implements ObserverContract
{
    /**
     * {@inheritdoc}
     */
    abstract public function handle(EventContract $event);

    /**
     * Making object callable
     *
     * @param EventContract $event
     */
    final public function __invoke(EventContract $event)
    {
        $this->handle($event);
    }
}