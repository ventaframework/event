<?php declare(strict_types = 1);

namespace Venta\Event;

use Ds\Map;
use Ds\PriorityQueue;
use Venta\Contracts\Event\EventContract;

/**
 * Class Event
 *
 * @package Venta\Event
 */
class Event implements EventContract
{
    /**
     * Event name holder
     *
     * @var string
     */
    protected $_name;

    /**
     * Observers holder
     *
     * @var PriorityQueue
     */
    protected $_observers;

    /**
     * Data holder
     *
     * @var Map
     */
    protected $_data;

    /**
     * Flag, defines if event was stopped
     *
     * @var bool
     */
    protected $_stopped = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name, PriorityQueue $observers = null)
    {
        $this->_name = $name;
        $this->_observers = $observers ?: new PriorityQueue;
        $this->_data = new Map;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $this->_stopped = true;
    }

    /**
     * {@inheritdoc}
     */
    public function wasStopped(): bool
    {
        return $this->_stopped === true;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(string $key, $value)
    {
        $this->_data->put($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getData(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->_data->copy()->toArray();
        }

        return $this->_data->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function addObserver(callable $observer, int $priority = 0)
    {
        $this->_observers->push($observer, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function hasObservers(): bool
    {
        return $this->_observers->count() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getObservers(): array
    {
        return $this->_observers->copy()->toArray();
    }
}