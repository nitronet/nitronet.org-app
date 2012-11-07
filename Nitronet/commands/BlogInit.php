<?php
namespace Nitronet\commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class BlogInit extends BaseCommand
{
    protected function configure()
    {
        $this->setName('blog:init')
            ->setDescription('Initialize & Configure the blog');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cfg = $this->getServices()->get('blogCfg');
        $workdir = $cfg->get('blog.workdir', getcwd() .'/workdir');

        if (is_dir($workdir)) {
            throw new \RuntimeException("Blog already initialized");
        }

        $repodir = $workdir;
        $repoUrl = $cfg->get('blog.repository');
        if (!$repoUrl) {
            throw new \RuntimeException("Missing blog.repository configuration.");
        }
        
        $output->writeln(sprintf("Clonning repository %s into workdir ...", $repoUrl));

        $cmd = sprintf("git clone %s %s", $repoUrl, $repodir);
        $proc = new \Symfony\Component\Process\Process($cmd);
        $proc->setTimeout(null);
        $proc->run(function ($type, $buffer) use ($output) {
            $output->write('<comment>'.$buffer .'</comment>');
        });

        if(!$proc->isSuccessful()) {
           throw new \RuntimeException(
                'Git process failed.'
            );
        }

        $output->writeln("<info>Blog is ready.</info>");
    }
}
