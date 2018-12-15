<?php

namespace App\Command;

use App\Service\Archive\Txd;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class SearchExtractTXDCommand extends Command
{

    protected static $defaultName = 'mass-extract:txd';

    protected function configure()
    {

        $this->addArgument('folder', InputArgument::REQUIRED, 'Folder to search');
        $this->addArgument('outputTo', InputArgument::REQUIRED, 'Output folder');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $txd = new Txd();

        $collection = [];

        $folder = realpath($input->getArgument('folder'));
        $outputTo = realpath($input->getArgument('outputTo'));

        @mkdir($outputTo, 0777, true);

        $finder = new Finder();
        $finder->name('*.TXD')->name('*.txd')->name('*.tex')->name('*.TEX')->files()->in($folder );

        foreach ($finder as $file) {

            $output->write('Process ' . $file->getRelativePathname() . ' ');

            $path = $outputTo . '/' . $file->getRelativePathname();
            @mkdir($path, 0777, true);

            $content = $file->getContents();
            $contentMd5 = md5($content);

            $existingFiles = scandir($path);

            $skip = false;
            foreach ($existingFiles as $existingFile) {
                if (strpos($existingFile, $contentMd5) !== false){

                    list($textureName, $contentMd5, $textureMd5) = explode("___", $existingFile);

                    if (!isset($collection[ $textureName ])) $collection[ $textureName ] = [];

                    $collection[ $textureName ][ $textureMd5 ] = $path . '/' . $existingFile;
                    $output->write("s");
                    $skip = true;

                    continue;
                }
            }

            if ($skip){
                $output->write("\n");
                continue;
            }

            $textures = $txd->unpack( $content );

            foreach ($textures as $texture) {
                $output->write('.');

                $textureMd5 = md5($texture['data']);
                $fileName = $texture['name'] . "___" . $contentMd5 . "___" . $textureMd5 . ".dds";

                file_put_contents($path . '/' . $fileName, $texture['data']);

                if (!isset($collection[ $texture['name'] ])) $collection[ $texture['name'] ] = [];

                $collection[ $texture['name'] ][ md5($texture['data']) ] = $path . '/' . $fileName;
            }
            $output->write("\n");

        }

        $collectionOutput = $outputTo . '/__differences';
        @mkdir($collectionOutput, 0777, true);

        $output->write("\n");
        $output->write('Save file differences ');

        foreach ($collection as $entries) {
            if (count($entries) == 1) continue;
            $output->write('.');

            $index = 1;
            foreach ($entries as $texture) {
                list($textureName, $contentMd5, $textureMd5) = explode("___", $texture);
                $textureName = array_reverse(explode("/", $textureName))[0];

                copy($texture, $collectionOutput . '/' . $textureName . "_" . $index . ".dds");
                $index++;
            }
        }
        $output->write("\nDone.\n");
    }
}