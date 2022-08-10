<?php

declare(strict_types=1);

namespace ExAdmin\hyperf\command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * @Command
 */
#[Command]
class PluginComposerCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('plugin:composer');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Install the admin package');
        $this->addArgument('name',InputArgument::OPTIONAL,'plugin name');
    }

    public function handle()
    {
        $name = $this->input->getArgument('name');
        $plugs = plugin()->getPlug($name);
        if(!is_array($plugs)){
            $plugs = [$plugs];
        }
        $package = [];
        foreach ($plugs as $plug){
            $requires = $plug['require'] ??[];
            foreach ($requires as $require=>$version){
                $package[] = $require;
                $package[] = $version;
            }
        }
        if(count($package) == 0){
            $this->output->write('Nothing to install, update or remove');
            return 0;
        }
        $path  = dirname(__DIR__,5);
        $cmd = array_merge(['composer','require'],$package);
        $process = new Process($cmd,$path);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });
    }
}
