<?php

namespace App\Controller;

use App\Service\Compiler\FunctionMap\Manhunt;
use App\Service\Compiler\FunctionMap\Manhunt2;
use App\Service\Compiler\FunctionMap\ManhuntDefault;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{

    /**
     * @Route("/")
     * @Template()
     */
    public function index( )
    {


        $manhuntFunctionNames = [];
        foreach (array_merge(ManhuntDefault::$functions, Manhunt2::$functions) as $lowerName => $manhuntFunction) {

            if (isset($manhuntFunction['name'])){
                $manhuntFunctionNames[] = $manhuntFunction['name'];
            }else{
                $manhuntFunctionNames[] = $lowerName;
            }

        }

        $manhuntConstants = [];
        foreach (Manhunt2::$constants as $name => $const) {
            $manhuntConstants[] = $name;
        }

        $manhuntEvents = [];
        foreach (ManhuntDefault::$functionEventDefinition as $name => $offset) {
            $manhuntEvents[$name] = $offset;
        }

        return [
            'manhuntFunctionNames' => \json_encode($manhuntFunctionNames),
            'manhuntConstants' => \json_encode($manhuntConstants),
            'manhuntEvents' => \json_encode($manhuntEvents)
        ];
    }
}