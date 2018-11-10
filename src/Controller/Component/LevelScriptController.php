<?php

namespace App\Controller\Component;

use App\Service\Element\LevelScript;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/component/levelscript/{level}")
 */
class LevelScriptController
{

    /**
     * @Route("/", name="component_levelscript_listing")
     * @Template()
     *
     * @param LevelScript $levelScript
     * @param $level
     * @return array
     */
    public function listing( LevelScript $levelScript, $level )
    {


        return [
            'level' => $level,
            'scriptList' => $levelScript->getScriptList( $level )
        ];
    }

    /**
     * @Route("/{script}", name="component_levelscript_get")
     * @Template()
     *
     * @param LevelScript $levelScript
     * @param $level
     * @param $script
     * @return array
     */
    public function get( LevelScript $levelScript, $level, $script )
    {

        $source = $levelScript->getScript( $level, $script );
//        $preparedsource = Compiler::prepare($source);
//
//        $tokenizer = new Tokenizer();
//        $tokens = $tokenizer->run($preparedsource);
//
        return [
            'level' => $level,
            'script' => $script,
            'levelscript' => $source
        ];
    }

}