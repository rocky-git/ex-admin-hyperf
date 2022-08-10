<?php
namespace ExAdmin\hyperf;

use ExAdmin\hyperf\command\InstallCommand;
use ExAdmin\hyperf\command\PluginComposerCommand;
use ExAdmin\hyperf\listener\ConfigSetListener;
use ExAdmin\hyperf\listener\RegisterListener;
use ExAdmin\hyperf\middleware\CoreMiddleware;
use ExAdmin\ui\support\Container;
use Symfony\Component\Filesystem\Filesystem;

class ConfigProvider
{
    protected function updateVersion(){
        $path = BASE_PATH.'/public/ex-admin';
        $file = $path.'/version';
        $update = false;
        if(!is_file($file)){
            $update = true;
        }
        if(!$update && file_get_contents($file) != ex_admin_version()){
            $update = true;
        }
        if($update){
            $filesystem = new Filesystem();
            $filesystem->mirror(dirname(__DIR__,2) . '/ex-admin-ui/resources',$path,null,['override'=>true]);
            file_put_contents($file,ex_admin_version());
        }
    }
    public function __invoke(): array
    {
        $this->updateVersion();
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