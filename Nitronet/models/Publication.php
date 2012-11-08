<?php
namespace Nitronet\models;

use dflydev\markdown\MarkdownExtraParser;

class Publication
{
    const DATEFIELD_CREATED_AT = "created_at";
    const DATEFIELD_UPDATED_AT = "updated_at";
    
    protected $slug;

    protected $title;

    protected $contents;

    protected $created_at;

    protected $updated_at;

    protected $dateRelativeCreated;

    protected $dateRelativeUpdated;

    protected $author;

    protected $revision;
    
    protected $excerpLength = 300;

    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getContents() {
        return $this->contents;
    }

    public function getFormattedContent()
    {
        $parser = new MarkdownExtraParser();
        return $parser->transform($this->contents);
    }

    public function setContents($contents) {
        $this->contents = $contents;
    }

    public function getCreated_at() {
        return $this->created_at;
    }

    public function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

    public function getUpdated_at() {
        return $this->updated_at;
    }

    public function setUpdated_at($updated_at) {
        $this->updated_at = $updated_at;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function getRevision() {
        return $this->revision;
    }

    public function setRevision($revision) {
        $this->revision = $revision;
    }

    public function getDateRelativeCreated() {
        return $this->dateRelativeCreated;
    }

    public function setDateRelativeCreated($dateRelativeCreated) {
        $this->dateRelativeCreated = $dateRelativeCreated;
    }

    public function getDateRelativeUpdated() {
        return $this->dateRelativeUpdated;
    }

    public function setDateRelativeUpdated($dateRelativeUpdated) {
        $this->dateRelativeUpdated = $dateRelativeUpdated;
    }

    public function getExcerp()
    {
        $contents = $this->getFormattedContent();
        $contents = strip_tags($contents);
        
        if (strlen($contents) > $this->excerpLength) {
            $contents = substr($contents, 0, $this->excerpLength);
            $contents .= '...';
        }
        
        if (false === strpos($contents, '.') 
            || strpos($contents, '.', strlen($contents))) {
            return $contents;
        }
        
        $sentences = explode('. ', $contents);
        array_pop($sentences);
        
        return trim(implode('. ', $sentences)) .'.';
    }
    
    public function getExcerpLength() {
        return $this->excerpLength;
    }

    public function setExcerpLength($excerpLength) {
        $this->excerpLength = $excerpLength;
    }

    public function dateFormat($dateField, $format = "U")
    {
        $value = $this->{$dateField};
        $datetime = \DateTime::createFromFormat(\DateTime::RFC2822, $value);
        
        return $datetime->format($format);
    }

}