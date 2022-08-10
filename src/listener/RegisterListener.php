<?php

namespace ExAdmin\hyperf\listener;

use ExAdmin\ui\support\Container;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeMainServerStart;
use Hyperf\Framework\Event\BootApplication;


class RegisterListener implements ListenerInterface
{
    
    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [
            BeforeMainServerStart::class,
        ];
    }

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event)
    {
        Container::getInstance()->plugin->register();
    }
}