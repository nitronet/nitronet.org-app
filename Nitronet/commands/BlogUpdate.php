<?php
namespace Nitronet\commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class BlogUpdate extends BaseCommand
{
    const REPOSITORY_DIR    = 'repo';

    protected function configure()
    {
        $this->setName('blog:update')
            ->setDescription('Updates the blog')
            ->addOption(
                "force",
                "f",
                InputOption::VALUE_NONE,
                "Forces parsing the repository"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cfg = $this->getServices()->get('blogCfg');
        $workdir = $cfg->get('blog.workdir');
        $force = $input->getOption("force");

        if (!is_dir($workdir)) {
            throw new \RuntimeException("Working directory doesn't exist. Please run blog:init instead.");
        }

        $repodir = $workdir . DIRECTORY_SEPARATOR . self::REPOSITORY_DIR;
        if (!is_dir($repodir)) {
            throw new \RuntimeException("Repository directory doesn't exist. Please run blog:init instead.");
        }

        $git = $this->getServices()->get('git');
        $currentVersion = $git->getTreeHash();

        $output->writeln("Pulling last changes...");
        $git->pull();
        $git->refresh();

        $newVersion = $git->getTreeHash();

        if ($newVersion === $currentVersion && !$force) {
            $output->writeln("<info>Everything is up to date.</info>");
        } else {
            $output->writeln("<info>Blog is now up to date.</info>");
        }
    }
}
