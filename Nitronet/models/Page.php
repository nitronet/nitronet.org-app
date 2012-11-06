<?php
namespace Nitronet\models;

class Page
{
    protected $slug;
    
    protected $title;
    
    protected $contents;
    
    protected $created_at;
    
    protected $updated_at;
    
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
}