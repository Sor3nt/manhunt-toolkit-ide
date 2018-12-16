<?php

namespace App\Service;

use App\Service\Archive\Mls;
use App\Service\Archive\Tex;
use App\Service\Archive\Txd;
use App\Service\Archive\ZLib;

class Resources
{

    public $workDirectory = '/Users/matthias/mh2';

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
//            file_put_contents('ps2-dsfe.txd', $content);
//            exit;
        }

        switch ($fileExtension){

            case 'glg':
            case 'dxt1':
            case 'dxt2':
            case 'dxt3':
            case 'dxt4':
            case 'dxt5':
                break;

            case 'scc':
            case 'mls':
                $handler = new Mls();
                $content = $handler->unpack($content, 'mh2');
                break;

            case 'tex':
                $handler = new Tex();
                $content = $handler->unpack($content);
                break;
            case 'txd':

                $handler = new Txd();
                $content = $handler->unpack($content);
                break;
            default:
                throw new \Exception(sprintf('Unable to load resource %s, unknown handler', $relativeFile));
        }

        return new Resource(
            $content,
            $fileExtension,
            $relativeFile
        );
    }

    public function saveMls( Resource $resource ){

    }

}