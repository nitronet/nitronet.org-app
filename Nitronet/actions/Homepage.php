<?php
namespace Nitronet\actions;


use Fwk\Core\Action\Result;
use Nitronet\runtime\AppController;
use Nitronet\models\Publication;

class Homepage extends AppController
{
    protected $articles;

    public function show()
    {
        $articles = $this->getServices()->get('builder')->getArticles();

        // sort articles by creation date
        $sorted = array();
        foreach ($articles as $article) {
            $ts = $article->dateFormat(Publication::DATEFIELD_CREATED_AT);
            $sorted[$ts] = $article;
        }
        
        $this->articles = $sorted;
        
        ksort($this->articles);
        $this->articles = array_reverse($this->articles);
        
        return Result::SUCCESS;
    }

    /**
     * @return array 
     */
    public function getArticles()
    {
        return $this->articles;
    }
}