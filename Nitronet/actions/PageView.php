<?php
namespace Nitronet\actions;


use Fwk\Core\Action\Result;
use Nitronet\runtime\AppController;

class PageView extends AppController
{
    protected $pageSlug;

    protected $page;

    public function show()
    {
        $pages = $this->getServices()->get('builder')->getPages();

        if (!isset($pages[$this->pageSlug])) {
            return Result::ERROR;
        }

        $this->page = $pages[$this->pageSlug];

        return Result::SUCCESS;
    }

    public function getPageSlug()
    {
        return $this->pageSlug;
    }

    public function setPageSlug($pageSlug)
    {
        $this->pageSlug = $pageSlug;
    }

    public function getPage()
    {
        return $this->page;
    }
}