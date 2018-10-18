<?php

namespace App\Service;

class Resource
{

    private $content = '';
    private $type = '';
    private $relativeFile = '';

    public function __construct( $content, $type, $relativeFile)
    {
        $this->content = $content;
        $this->type = $type;
        $this->relativeFile = $relativeFile;
    }

    public function getContent(){
        return $this->content;
    }

}