<?php

namespace App\Service\Element\Ini\Game\InitScripts;

use App\Service\Parser\Ini;
use App\Service\Resources;

/**
 * Class Level
 * @package App\Service
 *
 * Handler for the file: /global/initscripts/resource23.glg
 * Original name: LEVELS.txt
 */

class Levels
{
    private $file = '/global/initscripts/resource23.glg';

    /** @var \App\Service\Resource  */
    private $content;

    /**
     * Public ini fields
     */
    public $levels = [];
    public $enableLevelOrder = 1;


    public function __construct( Resources $resources )
    {

        $this->content = $resources->load($this->file);
        $this->parseContent();
    }

    private function parseContent(){

        $attributes = Ini::attributes($this->content->getContent());

        foreach ($attributes as $attribute) {
            $attributeName = current(array_keys($attribute));
            $value = $attribute[$attributeName];

            switch ($attributeName){

                case 'ENABLE_LEVEL_ORDER':
                    $this->enableLevelOrder = (int) $value;
                    break;

                case 'LEVEL':
                    $this->levels[] = $value;
                    break;

                default:
                    throw new \Exception(sprintf('Unknown attribute %s', $attributeName));

            }
        }
    }

    public function getLevels()
    {
        return $this->levels;
    }
}