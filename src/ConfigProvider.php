<?php
namespace ExAdmin\hyperf;

use ExAdmin\hyperf\command\InstallCommand;
use ExAdmin\hyperf\command\PluginComposerCommand;
use ExAdmin\hyperf\listener\ConfigSetListener;
use ExAdmin\hyperf\listener\RegisterListener;
use ExAdmin\hyperf\middleware\CoreMiddleware;
use ExAdmin\ui\support\Container;

class ConfigProvider
{
    public function __invoke(): array
    {
        
        return [
            'listeners' => [
                ConfigSetListener::class,
                RegisterListener::class
            ],
            'dependencies'=>[
                \Hyperf\HttpServer\CoreMiddleware::class => CoreMiddleware::class,
            ],
            'commands'=>[
                InstallCommand::class,
                PluginComposerCommand::class
            ],
            // 合并到  config/autoload/annotations.php 文件
            'annotations' => [
                'scan' => [
                    'paths' => [
                        dirname(__DIR__,4).DIRECTORY_SEPARATOR.'plugin',
                    ],
                    'ignore_annotations' => [
                        'auth',
                    ],
                ],
            ],
        ];
    }
}