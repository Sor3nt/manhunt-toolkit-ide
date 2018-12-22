<?php

namespace App\Command;

use App\Service\Archive\Txd;
use App\Service\Archive\ZLib;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ZLibCommand extends Command
{

    protected static $defaultName = 'zlib:unpack';

    protected function configure()
    {

        $this->addArgument('file', InputArgument::REQUIRED, 'file to unpack');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = file_get_contents($input->getArgument('file'));

        $uncompressed = ZLib::uncompress($file);
        file_put_contents($input->getArgument('file') . '.raw', $uncompressed);

        $output->write("\nDone.\n");
    }
}