<?php
namespace Nitronet\actions;


use Symfony\Component\HttpFoundation\Response;
use Nitronet\models\Publication;

class RssFeed extends Homepage
{
    public function show()
    {
        parent::show();
        
        $response = new Response(
            $this->articlesToFeed($this->getArticles())->saveXML(), 
            200, 
            array(
                'Content-Type' => 'application/xml'
            )
        );
        
        return $response;
    }

    /**
     *
     * @param array $articles
     * 
     * @return \DOMDocument 
     */
    public function articlesToFeed(array $articles)
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $burl = $this->getServices()->get('blogCfg')->get('blog.link');
        // create root element
        $root = $dom->createElement("rss");
        $root->setAttribute('version', '2.0');
        $dom->appendChild($root);
        
        $channel = $dom->createElement("channel");
        $root->appendChild($channel);
        
        $title = $dom->createElement("title");
        $titleTxt = $dom->createTextNode($this->getServices()->get('blogCfg')->get('blog.name'));
        $title->appendChild($titleTxt);
        $channel->appendChild($title);

        $desc = $dom->createElement("description");
        $descTxt = $dom->createTextNode($this->getServices()->get('blogCfg')->get('blog.description'));
        $desc->appendChild($descTxt);
        $channel->appendChild($desc);
        
        $link = $dom->createElement("link");
        $linkTxt = $dom->createTextNode($this->getServices()->get('blogCfg')->get('blog.link'));
        $link->appendChild($linkTxt);
        $channel->appendChild($link);
        
        foreach ($articles as $article) {
            $item = $dom->createElement("item");
            $title = $dom->createElement("title");
            $titleTxt = $dom->createTextNode($article->getTitle());
            $title->appendChild($titleTxt);
            $item->appendChild($title);
            
            $desc = $dom->createElement("description");
            $descTxt = $dom->createTextNode($article->getExcerp());
            $desc->appendChild($descTxt);
            $item->appendChild($desc);
            
            $link = $dom->createElement("link");
            $linkTxt = $dom->createTextNode(rtrim($burl, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . "article". DIRECTORY_SEPARATOR . $article->getSlug());
            $link->appendChild($linkTxt);
            $item->appendChild($link);
            
            $pDate = $dom->createElement("pubDate");
            $pDateTxt = $dom->createTextNode($article->dateFormat(Publication::DATEFIELD_CREATED_AT, \DateTime::RSS));
            $pDate->appendChild($pDateTxt);
            $item->appendChild($pDate);
            
            $channel->appendChild($item);
        }
        // create text node
        $text = $dom->createTextNode("pepperoni");
        $item->appendChild($text);
        
        return $dom;
    }
}