<?php
namespace Nitronet\actions;


use Fwk\Core\Action\Result;
use Nitronet\runtime\AppController;

class ArticleView extends AppController
{
    protected $articleSlug;

    protected $article;

    public function show()
    {
        $articles = $this->getServices()->get('builder')->getArticles();

        if (!isset($articles[$this->articleSlug])) {
            return Result::ERROR;
        }

        $this->article = $articles[$this->articleSlug];

        return Result::SUCCESS;
    }

    public function getArticleSlug() {
        return $this->articleSlug;
    }

    public function setArticleSlug($articleSlug) {
        $this->articleSlug = $articleSlug;
    }

    public function getArticle() {
        return $this->article;
    }
}