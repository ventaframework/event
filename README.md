# Venta eCommerce Framework Event package
Event package includes three classes to use - Dispatcher, Observer, Event.

## Table of contents
- [Installation](#installation)
- [Basic usage](#basic-usage)
- [Dispatcher](#dispatcher)
- [Observer](#observer)
- [Event](#event)

## Installation

```sh
composer require venta/event
```

## Basic usage
Create new instance of dispatcher with `new \Venta\Event\Dispatcher;` in order to observe and fire events.

```php
    // Require autoload
    require __DIR__ . '/vendor/autoload.php';

    // Create new dispatcher instance
    $dispatcher = new \Venta\Event\Dispatcher;
    
    // Add an observer
    $dispatcher->observe('event.name', function($event) {
        // do stuff here...
    });
    
    // Fire an event
    $dispatcher->fire('event.name');
```

## Dispatcher
Dispatcher is used to store observers and fire events.

```php
    // Create dispatcher instance
    $dispatcher = new \Venta\Event\Dispatcher;
```

As an event listener, dispatcher supports any callable. Also, you can specify priority for observer. There are two basic ways to define observer: \Closure and instance of \Venta\Event\Observer class.

```php
    // Add observer with closure
    $dispatcher->observe('event.name', function($event) {
        // observe...
    }, 0);
    
    // Or, add \Venta\Event\Observer class instance.
    // Can be more usefull to store complex logic
    $dispatcher->observe('event.name', new class extends \Venta\Event\Observer {
        public function handle($event) 
        {
            // handle...
        }
    });
```

In order to dispatch an event use `dispatch()` function. After dispatching, event boject will be returned.
```php
    // Simple event dispatch
    $event = $dispatcher->dispatch('event.name');
    
    // Passing some parameters in
    $event = $dispatcher->dispatch('event.name', ['foo' => 'bar']);
```

There are several helper functions defined:
```php
    $observers = $dispatcher->getObservers('event.name'); // Returns array with all event observers
    $observerExists = $dispatcher->hasObservers('event.name'); // Defines, if event has any observers
```

## Observer
Observer can be any callable. If you want to store a bit more complex logic on event handling, you can extend `\Venta\Event\Observer` class and pass it as an observer of an event. This class requries you to define `handle()` function, which will be called on event dispatching.

```php
    use Venta\Event\Observer;
    use Venta\Contracts\Event\EventContract;

    /**
     * Class EventObserver
     */
    class EventObserver extends Observer
    {
        /**
         * {@inheritdoc}
         */
        public function handle(EventContract $event)
        {
            // handle...
        }
    }
```

## Event
Event object get passed to any observer function. It contains some helper functions to use, and also stores event data. You can call `stop()` function on event object in observer, which will stop event propagation. You can call `wasStopped()` function on returned from `dispatch()` function event object, in order to know if event was stopped.

```php
    use Venta\Event\Observer;
    use Venta\Contracts\Event\EventContract;

    /**
     * Class EventObserver
     */
    class EventObserver extends Observer
    {
        /**
         * {@inheritdoc}
         */
        public function handle(EventContract $event)
        {
            $event->getName(); // returns event name
            $event->getData('foo', 'default'); // Event data getter
            $event->getData(); // returns array with all event data
            $event->stop(); // stop event propagation
            $event->wasStopped(); // defines, if event was stopped
        }
    }
```