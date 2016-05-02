<?php declare(strict_types = 1);

namespace Venta\Event\Traits;

use Venta\Contracts\Event\DispatcherContract;

/**
 * Class EventDispatcherAwareTrait
 *
 * @package Venta\Event
 */
trait EventDispatcherAwareTrait
{
    /**
     * Dispatcher holder
     *
     * @var DispatcherContract
     */
    protected $_eventDispatcher;

    /**
     * {@inheritdoc}
     */
    public function setEventsDispatcher(DispatcherContract $dispatcher)
    {
        $this->_eventDispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventsDispatcher(): DispatcherContract
    {
        return $this->_eventDispatcher;
    }
}