<?php

namespace Nitronet\runtime;

use Fwk\Core\Application;

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

    public function registerGitService(Application $app)
    {
        $git = new GitService($app->get('blog.workdir') .'/repo');
        $builder = new BuilderService($git);

        $app->getServices()->set('git', $git);
        $app->getServices()->set('builder', $builder);
    }
}