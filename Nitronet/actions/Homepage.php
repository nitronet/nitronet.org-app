<?php
namespace Nitronet\actions;


use Fwk\Core\Action\Result;
use Nitronet\runtime\AppController;

class Homepage extends AppController
{
    protected $articles;

    public function show()
    {
        $this->articles = $this->getServices()->get('builder')->getArticles();

        return Result::SUCCESS;
    }

    public function getArticles() {
        return $this->articles;
    }
}