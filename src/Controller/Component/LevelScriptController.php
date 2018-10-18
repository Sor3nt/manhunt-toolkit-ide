<?php

namespace App\Controller\Component;

use App\Service\Compiler\Compiler;
use App\Service\Compiler\Tokenizer;
use App\Service\Element\LevelScript;
use App\Service\Element\LevelScriptInspector;
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
     */
    public function get( LevelScript $levelScript, LevelScriptInspector $levelScriptInspector, $level, $script )
    {

        $source = $levelScript->getScript( $level, $script );
        $preparedsource = Compiler::prepare($source);

        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->run($preparedsource);

        $inspection = $levelScriptInspector->inspect($tokens);

        return [
            'level' => $level,
            'script' => $script,
            'inspection' => $inspection,
            'levelscript' => $source
        ];
    }

}