<?php

namespace Nitronet\runtime;

use Fwk\Core\Application;
use Fwk\Db\Connection;

class Bootstrap
{
    public function registerBlogConfig(Application $app)
    {
        $app->getServices()->set('blogCfg', function() use ($app) {
            $config = new \Fwk\Core\Object();
            foreach ($app->rawGetAll() as $key => $value) {
                if (strpos($key, 'blog.') === false) {
                    continue;
                }

                $config->set($key, $value);
            }
            
            return $config;
        });
    }
    
    public function registerDatabase(Application $app)
    {
    }

    public function registerGitService(Application $app)
    {
         $app->getServices()->set('git', function() use ($app) {
            $git = new GitService($app->get('blog.workdir') .'/repo');
            
            return $git;
        });
    }
    /**
     *
     * @param Application $app
     * 
     * @return void 
     */
    public function registerCurlService(Application $app)
    {
        if (php_sapi_name() != "cli") {
            return;
        }
        
        $app->getServices()->set('curl', function() use ($app) {
            return new CurlService(
                $app->get("curl.proxyhost", null),
                $app->get("curl.proxyuser", null),
                $app->get("curl.proxypass", null)
            );
        });
    }
}