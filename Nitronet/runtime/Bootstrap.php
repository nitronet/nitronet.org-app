<?php

namespace Nitronet\runtime;

use Fwk\Core\Application;
use Fwk\Db\Connection;

class Bootstrap
{
    public function registerBlogConfig(Application $app)
    {
        $config = new \Fwk\Core\Object();
        foreach ($app->rawGetAll() as $key => $value) {
            if (strpos($key, 'blog.') === false) {
                continue;
            }
            
            $config->set($key, $value);
        }
        
        $app->getServices()->set('blogCfg', $config);
    }
    
    public function registerDatabase(Application $app)
    {
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