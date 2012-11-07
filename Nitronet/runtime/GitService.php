<?php
namespace Nitronet\runtime;

use Symfony\Component\Process\Process;

class GitService
{
    const GIT_BIN = 'git';

    /**
     * @var string
     */
    protected $repositoryDir;

    protected $treeHash;

    protected $commitHash;

    protected $committer;

    protected $committerEmail;

    protected $author;

    protected $dateRelative;

    protected $date;

    protected $authorEmail;

    /**
     *
     * @param string $respositoryDir
     *
     * @return void
     */
    public function __construct($repositoryDir)
    {
        $this->repositoryDir = $repositoryDir;
    }

    /**
     *
     */
    public function getTreeHash()
    {
        $this->loadInfos();

        return $this->treeHash;
    }

    public function getRepositoryDir()
    {
        return $this->repositoryDir;
    }

    public function getCommitHash()
    {
        $this->loadInfos();

        return $this->commitHash;
    }

    public function getAuthor()
    {
        $this->loadInfos();

        return $this->author;
    }

    public function getDateRelative()
    {
        $this->loadInfos();

        return $this->dateRelative;
    }

    public function getDate()
    {
        $this->loadInfos();

        return $this->date;
    }

    public function getAuthorEmail()
    {
        $this->loadInfos();

        return $this->authorEmail;
    }

    public function getCommitter()
    {
        $this->loadInfos();

        return $this->committer;
    }

    public function getCommitterEmail()
    {
        $this->loadInfos();

        return $this->committerEmail;
    }

    protected function loadInfos()
    {
       if (!is_dir($this->repositoryDir)) {
           throw new \RuntimeException(
               sprintf(
                   "Invalid directory %s",
                   $this->repositoryDir
               )
           );
       }

       if (!isset($this->treeHash)) {
            $cmd = sprintf('%s show --format="%s" --no-ext-diff',
                self::GIT_BIN,
                "treeHash-|-%T;commitHash-|-%H;author-|-%an;authorEmail-|-%ae;dateRelative-|-%ar;date-|-%aD;committerEmail-|-%ce;committer-|-%cn"
            );

            $proc = $this->processFactory($cmd);
            $proc->run();

            if (!$proc->isSuccessful()) {
                throw new \RuntimeException("git-show process failed.");
            }

            $out = $proc->getOutput();
            $line = substr($out, 0, strpos($out, "\n"));

            $tmp = explode(";", $line);
            foreach($tmp as $token) {
                list($key, $value) = explode("-|-", $token);
                $this->{$key} = $value;
            }
        }
    }

    /**
     *
     * @return boolean
     * @throws \RuntimeException
     */
    public function pull()
    {
        $cmd = sprintf('%s pull --force --quiet',
            self::GIT_BIN
        );

        $proc = $this->processFactory($cmd);
        $proc->run();

        if (!$proc->isSuccessful()) {
            throw new \RuntimeException("git-pull process failed.");
        }

        return true;
    }

    /**
     * @return void
     */
    public function refresh()
    {
        unset($this->treeHash);
        $this->loadInfos();
    }

    /**
     *
     * @param string $command
     *
     * @return Process
     */
    protected function processFactory($command)
    {
        $proc = new Process($command);
        $proc->setWorkingDirectory($this->repositoryDir);

        return $proc;
    }

    /**
     * Returns commits informations for a given file as an array of stdClass
     *
     * @param string $filePath Relative path to the file (base is repositoryDir)
     *
     * @return array
     * @throws \RuntimeException
     */
    public function getFileCommits($filePath)
    {
        $file = $this->repositoryDir . DIRECTORY_SEPARATOR . $filePath;
        if(!is_file($file)) {
            throw new \RuntimeException(sprintf("File does not exist: %s", $file));
        }

        $cmd = sprintf('%s log --format="%s" --follow -- %s',
            self::GIT_BIN,
            "commit-|-%H;message-|-%f;author-|-%an;authorEmail-|-%ae;dateCreated-|-%aD;dateCreatedRelative-|-%ar;committerEmail-|-%ce;committer-|-%cn%n",
            $file
        );

        $proc = $this->processFactory($cmd);
        $proc->run();

        if (!$proc->isSuccessful()) {
            throw new \RuntimeException("git-log process failed.");
        }

        $out = $proc->getOutput();
        $lines = explode("\n", $out);

        $final = array();
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            $infos = new \stdClass();
            $tmp = explode(";", $line);

            foreach ($tmp as $token) {
                list($key, $value) = explode("-|-", $token);
                if(empty($key)) {
                    continue;
                }

                $infos->{$key} = $value;
            }

            $final[$infos->commit] = $infos;
        }

        return $final;
    }
}