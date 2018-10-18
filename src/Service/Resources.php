<?php

namespace App\Service;

use App\Service\Archive\Mls;
use App\Service\Archive\ZLib;

class Resources
{

    private $workDirectory = '/Users/matthias/mh2';

    /**
     * @param $relativeFile
     * @return \App\Service\Resource
     * @throws \Exception
     *
     * todo: caching verbauen
     */
    public function load( $relativeFile ){

        $absoluteFile = $this->workDirectory . $relativeFile;
        if (!file_exists( $absoluteFile )) throw new \Exception(sprintf('File not found: %s', $absoluteFile));

        $fileExtension = strtolower(pathinfo($absoluteFile, PATHINFO_EXTENSION));

        $content = file_get_contents($absoluteFile);
        $contentAsHex = bin2hex($content);

        // we found a zLib compressed file
        if (substr($contentAsHex, 0, 8) === "5a32484d"){
            $content = ZLib::uncompress( $content );
            $contentAsHex = bin2hex($content);
        }

        switch ($fileExtension){

            case 'glg':
                return new Resource(
                    $content,
                    $fileExtension,
                    $relativeFile
                );
                break;
            case 'mls':
                $mlsHandler = new Mls();
                $mlsContent = $mlsHandler->unpack($content, 'mh2');

                return new Resource(
                    $mlsContent,
                    $fileExtension,
                    $relativeFile
                );
                break;
            default:
                throw new \Exception(sprintf('Unable to load resource %s, unknown handler', $relativeFile));
        }


    }

}