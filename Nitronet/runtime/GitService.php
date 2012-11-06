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
        if (!isset($this->treeHash)) {
            $cmd = sprintf('%s show --format="%s"',
                self::GIT_BIN,
                "%T"
            );
            
        }
    }
    
    
    protected function loadInfos()
    {
       if (!isset($this->treeHash)) {
            $cmd = sprintf('%s show --format="%s" --no-ext-diff',
                self::GIT_BIN,
                "treeHash:%T;commit:%H:author:%an;date:%ar"
            );
            
            $proc = $this->processFactory($cmd);
            $proc->run();
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
        $proc = new Process($cmd);
        $proc->setWorkingDirectory($this->repositoryDir);
        
        return $proc;
    }
}
