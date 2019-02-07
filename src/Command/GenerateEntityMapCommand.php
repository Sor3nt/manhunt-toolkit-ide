<?php

namespace App\Command;

use App\MHT;
use App\Service\Archive\Dff;
use App\Service\Archive\Glg;
use App\Service\Archive\Mls;
use App\Service\Archive\ZLib;
use App\Service\Compiler\Compiler;
use App\Service\Resources;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class GenerateEntityMapCommand extends Command
{

    protected static $defaultName = 'gen:map';

    protected function configure()
    {


    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resources = new Resources();



        $glgResource = $resources->load('tests/Resources/Archive/Glg/Manhunt2/PC/resource3.glg', MHT::GAME_MANHUNT_2, MHT::PLATFORM_PC);

        /** @var Glg $glgHandler */
        $glgHandler = $glgResource->getHandler();

        $records = $glgHandler->convertRecords($glgResource->getInput()->binary);

//        $resource = $resources->load('tests/Resources/Archive/Inst/Manhunt1/PC/entity.inst', MHT::GAME_MANHUNT, MHT::PLATFORM_PC);
        $resource = $resources->load('tests/Resources/Archive/Inst/Manhunt2/PC/entity_pc.inst', MHT::GAME_MANHUNT_2, MHT::PLATFORM_PC);

//        $entities = $resource->getHandler()->unpack($resource->getInput(), MHT::GAME_MANHUNT, MHT::PLATFORM_PC);
        $entities = $resource->getHandler()->unpack($resource->getInput(), MHT::GAME_MANHUNT_2, MHT::PLATFORM_PC);

        $results = [];
        foreach ($entities as $entity) {
            if (!isset($records[ $entity['record'] ])){
                echo "No record found for " . $entity['record'] . "\n";
                continue;
            }
            $recordOptions = $records[ $entity['record'] ];

            $result = [
                'position' => $entity['position'],
                'internalName' => $entity['internalName'],
                'entityClass' => $entity['entityClass'],
            ];

            foreach ($recordOptions as $recordOption) {
                if ($recordOption['attr'] == "MODEL"){
                    $result['model'] = $recordOption['value'];
                    break;
                }
            }

            $results[] = $result;

        }

        echo \json_encode($results);
    }
}