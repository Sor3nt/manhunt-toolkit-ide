<?php
namespace App\Tests\Archive\Txd\Extract\Manhunt2;

use App\Service\Archive\Bmp;
use App\Service\Archive\Dds;
use App\Service\Archive\Dxt;
use App\Service\Resources;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PCTest extends KernelTestCase
{

    public function testLevel1()
    {

        $resources = new Resources();
        $resources->workDirectory = explode("/tests/", __DIR__)[0] . "/tests/Resources";
        $content = $resources->load('/Archive/Tex/Manhunt2/PC/gmodelspc.tex');

        $content = $content->getContent();
//
        $ddsHandler = new Dds();
        $dxtHandler = new Dxt();
        $bmpHandler = new Bmp();

        foreach ($content as $item) {


            //decode the DDS
            $ddsDecoded = $ddsHandler->decode($item['data']);

            //decode the DXT Texture
            $bmpRgba = $dxtHandler->decode(
                $ddsDecoded['data'],
                $ddsDecoded['width'],
                $ddsDecoded['height'],
                'abgr'
            );

            //Convert the RGBa values into a Bitmap
            $bmpImage = $bmpHandler->encode(
                $bmpRgba,
                $ddsDecoded['width'],
                $ddsDecoded['height']
            );

//            file_put_contents('/Users/matthias/www/privat/manhunt-toolkit-ide-git/tests/Resources/_test_exports/test-' . $item['name'] . ".bmp" , $bmpImage);

        }

        //we expect 13 results
        $this->assertEquals(13, count($content));

        //the 5t entry is CJ_crow
        $this->assertEquals("CJ_crow", $content[5]['name']);

        //the data is a DDS file
        $this->assertEquals("DDS", substr($content[5]['data'], 0, 3));

        //the data is like expected
        $this->assertEquals("71c2cf4c0361608e114eda94a0fa334b", md5($content[5]['data']));

    }

}