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
    public function __construct($respositoryDir)
    {
        if (!is_dir($respositoryDir)) {
            throw new \RuntimeException(
                sprintrf(
                    "Invalid directory %s",
                    $respositoryDir
                )
            );
        }
        
        $this->repositoryDir = $respositoryDir;
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
       if (!isset($this->treeHash)) {
            $cmd = sprintf('%s show --format="%s" --no-ext-diff',
                self::GIT_BIN,
                "treeHash:%T;commitHash:%H;author:%an;authorEmail:%ae;dateRelative:%ar;date:%aD;committerEmail:%ce;committer:%cn"
            );
            
            $proc = $this->processFactory($cmd);
            $proc->run();
            
            if (!$proc->isSuccessful()) {
                throw new \RuntimeException("git-show process failed.");
            }
            
            $out = $proc->getOutput();
            $line = substr($out, 0, strpos($out, "\n"));
            
            $final = array();
            $tmp = explode(";", $line);
            foreach($tmp as $token) {
                list($key, $value) = explode(":", $token);
                $this->{$key} = $value;
            }
            
            var_dump($this);
        } 
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
}
