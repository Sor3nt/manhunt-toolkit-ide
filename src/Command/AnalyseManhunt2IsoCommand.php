<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class AnalyseManhunt2IsoCommand extends Command
{

    protected static $defaultName = 'analyse:manhunt2';

    protected function configure()
    {

        $this->addArgument('folder', InputArgument::REQUIRED, 'Manhunt 2 Folder');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $finder = new Finder();
        $finder->files()->in($input->getArgument('folder') . '/levels');
        $finder->sort(function ($a, $b){
            return $a > $b;
        });
        foreach ($finder as $file) {
            echo $file->getRelativePathname() . "," . md5($file->getContents()) . "\n";
        }
    }
}