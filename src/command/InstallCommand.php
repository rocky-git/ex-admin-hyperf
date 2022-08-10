<?php

declare(strict_types=1);

namespace ExAdmin\hyperf\command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @Command
 */
#[Command]
class InstallCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('admin:install');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Install the admin package');
        $this->addOption('force','f',InputOption::VALUE_NONE,'Force overwrite file');
        $this->addOption('versions', null, InputOption::VALUE_REQUIRED, 'version number');
    }

    public function handle()
    {
        $filesystem = new Filesystem;
        $filesystem->mirror(dirname(__DIR__,3) . '/ex-admin-ui/resources',BASE_PATH.'/public/ex-admin',null,['override'=>$this->input->getOption('force')]);
        $path = plugin()->download('hyperf',$this->input->getOption('versions'));
        if ($path === false) {
            $this->output->warning('下载插件失败');
            return 0;
        }
       
        $result = plugin()->install($path,$this->input->getOption('force'));
        if ($result !== true) {
            $this->output->warning($result);
            return 0;
        }
        unlink($path);
        plugin()->buildIde();
        plugin()->hyperf->install();
        $file = BASE_PATH.'/composer.json';
        $content = file_get_contents($file);
        if(strpos($content,'"plugin\\\\"') === false){
            $content = str_replace('"App\\\\": "app/"','"App\\\\": "app/",'.PHP_EOL."\t\t\t".'"plugin\\\\": "plugin/"',$content);
            file_put_contents($file,$content);
        }
        $this->call('plugin:composer',['name'=>'hyperf']);
        $this->call('vendor:publish',['package'=>'hyperf/filesystem']);
        $file = BASE_PATH.'/config/autoload/file.php';
        $content = file_get_contents($file);
        $content = str_replace("__DIR__ . '/../../runtime'","BASE_PATH . '/public/storage',".PHP_EOL."\t\t\t'url'=>'http://127.0.0.1:9501/storage'",$content);
        file_put_contents($file,$content);
        $this->call('vendor:publish',['package'=>'hyperf/async-queue']);
        $this->output->success('install success');
    }
}
