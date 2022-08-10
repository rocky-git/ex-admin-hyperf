<?php

namespace ExAdmin\hyperf\listener;

use ExAdmin\hyperf\ExAdminExceptionHandler;
use ExAdmin\hyperf\middleware\RequestMiddleware;
use ExAdmin\ui\support\Container;
use Hyperf\AsyncQueue\Process\ConsumerProcess;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Contract\ListenerInterface;

use Hyperf\Framework\Event\BootApplication;


class ConfigSetListener implements ListenerInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;


    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event)
    {
        //设置静态资源配置
        
        $this->setConfig('server.settings',[
            'document_root' => BASE_PATH . '/public',
            'enable_static_handler' => true,
        ]);
        //设置异常response
        $this->setConfig('exceptions.handler.http',ExAdminExceptionHandler::class);
        
        //添加中间件
        $this->setConfig('middlewares.http',RequestMiddleware::class);
        //添加中间件
        $this->setConfig('processes',ConsumerProcess::class);
    }
    protected function setConfig($key,$value){
        $config = $this->config->get($key,[]);
        if(is_array($value)){
            $config = array_merge($config,$value);
        }else{
            array_unshift($config,$value);
        }
        $this->config->set($key,$config);
    }
}