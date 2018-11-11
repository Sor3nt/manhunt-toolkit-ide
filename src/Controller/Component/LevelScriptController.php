<?php

namespace App\Controller\Component;

use App\Service\Element\LevelScript;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
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
    /**
     * @Route("/save/{script}", name="component_levelscript_save")
     *
     * @param Request $request
     * @param LevelScript $levelScript
     * @param $level
     * @param $script
     * @return array
     */
    public function save(Request $request, LevelScript $levelScript, $level, $script )
    {

        $srce = $request->get('srce');

        $parentScript = false;
        if ($level != $script) $parentScript = $level;

        $mhsc = $levelScript->compile($srce, $parentScript, 'mh2');
        unset($mhsc['extra']);

        $mhsc['NAME'] = $script;
//        $mhsc['SRCE'] = $srce;
        if ($level == $script) $mhsc['NAME'] = 'levelscript';

        $levelMls = $levelScript->replaceScript($level, $script, $mhsc);
//        $levelMls = $levelScript->replaceScript($level, "gibts nicht", []);

        $mls = $levelScript->pack($levelMls->getContent());

        file_put_contents('/Users/matthias/mh2/levels/A01_Escape_Asylum/test.mls', $mls);
        var_dump("OK");
        exit;


//        $source = $levelScript->getScript( $level, $script );
//        var_dump($request->get('srce'));
        exit;

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