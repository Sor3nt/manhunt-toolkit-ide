<?php

namespace App\Controller\Component;


use App\Service\Element\Ini\Game\InitScripts\Levels;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/Component/levels")
 */
class LevelsController
{

    /**
     * @Route("/", name="component_levels_dropdown")
     * @Template()
     */
    public function dropdown( Levels $level )
    {

        return [
            'levels' => $level->getLevels()
        ];
    }

}