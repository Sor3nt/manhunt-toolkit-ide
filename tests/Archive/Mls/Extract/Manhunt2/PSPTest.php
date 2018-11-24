<?php
namespace App\Tests\Archive\Mls\Extract\Manhunt2;

use App\Service\Resources;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PSPTest extends KernelTestCase
{

    public function testLevel1()
    {

        $resources = new Resources();
        $resources->workDirectory = explode("/tests/", __DIR__)[0] . "/tests/Resources";
        $mlsContent = $resources->load('/Manhunt2/Archive/Mls/PSP/A01_ES.MLS');

        $mhls = $mlsContent->getContent();

        foreach ($mhls as $index =>  $mhsc) {
//            echo "if (\$index == ". $index . ") \n";
//            echo "    \$this->assertTrue(md5(\\json_encode(\$mhsc)) == \"".md5(\json_encode($mhsc))."\", \"Wrong MD5\");\n";

            if ($index == 0)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 1)
                $this->assertTrue(md5(\json_encode($mhsc)) == "cf39fe713e58e4bc7ec7cc86daf1409f", "Wrong MD5");
            if ($index == 2)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 3)
                $this->assertTrue(md5(\json_encode($mhsc)) == "6ec333d7cf662f3fe0f06cb210b3ca73", "Wrong MD5");
            if ($index == 4)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 5)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 6)
                $this->assertTrue(md5(\json_encode($mhsc)) == "247b2bf342a63a17140e000bbb40abee", "Wrong MD5");
            if ($index == 7)
                $this->assertTrue(md5(\json_encode($mhsc)) == "78a74625385b05698bdf3b7a6ca69c0d", "Wrong MD5");
            if ($index == 8)
                $this->assertTrue(md5(\json_encode($mhsc)) == "2debe26dcb9fe20d0fd42405e24163b7", "Wrong MD5");
            if ($index == 9)
                $this->assertTrue(md5(\json_encode($mhsc)) == "0a8f2f237ffb33d467eddea188b0328e", "Wrong MD5");
            if ($index == 10)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d95d4942647e223e3a57a3f491aace12", "Wrong MD5");
            if ($index == 11)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 12)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 13)
                $this->assertTrue(md5(\json_encode($mhsc)) == "4a4c9038479b067012e750fad4b4d325", "Wrong MD5");
            if ($index == 14)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 15)
                $this->assertTrue(md5(\json_encode($mhsc)) == "14b808137437ce1b7d28f8e52dccb285", "Wrong MD5");
            if ($index == 16)
                $this->assertTrue(md5(\json_encode($mhsc)) == "3653a79bb49cf8b3b94f7efcd374dff5", "Wrong MD5");
            if ($index == 17)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 18)
                $this->assertTrue(md5(\json_encode($mhsc)) == "7e208af479a73c60b9fb5a9d24e5845c", "Wrong MD5");
            if ($index == 19)
                $this->assertTrue(md5(\json_encode($mhsc)) == "2be9e5a441316fb3dc9eb59810932536", "Wrong MD5");
            if ($index == 20)
                $this->assertTrue(md5(\json_encode($mhsc)) == "981796686bd1601a799c767b9d77ccf9", "Wrong MD5");
            if ($index == 21)
                $this->assertTrue(md5(\json_encode($mhsc)) == "5b234ef75c3b8fb9bb4f90ab47c0350a", "Wrong MD5");
            if ($index == 22)
                $this->assertTrue(md5(\json_encode($mhsc)) == "dc391b4ab26b0efdc9731eca7ebd3e0b", "Wrong MD5");
            if ($index == 23)
                $this->assertTrue(md5(\json_encode($mhsc)) == "aa2427af8207f05157f7fc769219867b", "Wrong MD5");
            if ($index == 24)
                $this->assertTrue(md5(\json_encode($mhsc)) == "f43c39d6d0489acb2c2441b20193ee3b", "Wrong MD5");
            if ($index == 25)
                $this->assertTrue(md5(\json_encode($mhsc)) == "6cd0266ae1c88b6e0fb0ae2ff3f13bad", "Wrong MD5");
            if ($index == 26)
                $this->assertTrue(md5(\json_encode($mhsc)) == "6920e2c78926b323126c4f30fc3d54cb", "Wrong MD5");
            if ($index == 27)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 28)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 29)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 30)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 31)
                $this->assertTrue(md5(\json_encode($mhsc)) == "7aa3973b03752bad32f17a092e6625c5", "Wrong MD5");
            if ($index == 32)
                $this->assertTrue(md5(\json_encode($mhsc)) == "bc8c60b6a1f4f8dc60da51ffb2f41dee", "Wrong MD5");
            if ($index == 33)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 34)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 35)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 36)
                $this->assertTrue(md5(\json_encode($mhsc)) == "e61b7e7aa4136ed92fb4daf89aba359b", "Wrong MD5");
            if ($index == 37)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 38)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 39)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 40)
                $this->assertTrue(md5(\json_encode($mhsc)) == "e4bbf3f0850f3cf91a3205a299221fbd", "Wrong MD5");
            if ($index == 41)
                $this->assertTrue(md5(\json_encode($mhsc)) == "7227ccb29a3fcefbd28ec0967b68a6d9", "Wrong MD5");
            if ($index == 42)
                $this->assertTrue(md5(\json_encode($mhsc)) == "4cf1b80bc589e73d557782d5199b24cf", "Wrong MD5");
            if ($index == 43)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 44)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 45)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 46)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 47)
                $this->assertTrue(md5(\json_encode($mhsc)) == "3b72e3ad8906ab9a89a11e0cc0a344a3", "Wrong MD5");
            if ($index == 48)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 49)
                $this->assertTrue(md5(\json_encode($mhsc)) == "c74e2364fc2e7ebcec62e9ee9321d387", "Wrong MD5");
            if ($index == 50)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 51)
                $this->assertTrue(md5(\json_encode($mhsc)) == "ca11f1b7d8e32d66b388968903b314e0", "Wrong MD5");
            if ($index == 52)
                $this->assertTrue(md5(\json_encode($mhsc)) == "9257fef0766fc9e0038d914afac2e4d2", "Wrong MD5");
            if ($index == 53)
                $this->assertTrue(md5(\json_encode($mhsc)) == "27389674d694b13a51440569aed92cc3", "Wrong MD5");
            if ($index == 54)
                $this->assertTrue(md5(\json_encode($mhsc)) == "5e3303a6360fa0b93d9bf05a9aeb630d", "Wrong MD5");
            if ($index == 55)
                $this->assertTrue(md5(\json_encode($mhsc)) == "f948059014bd6c4af8963bd2ee870055", "Wrong MD5");
            if ($index == 56)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 57)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 58)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 59)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 60)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 61)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 62)
                $this->assertTrue(md5(\json_encode($mhsc)) == "9b4b3f9828e30b0fe265e8fd169e93ad", "Wrong MD5");
            if ($index == 63)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 64)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 65)
                $this->assertTrue(md5(\json_encode($mhsc)) == "9148c527b0f748cf5652906186be070a", "Wrong MD5");
            if ($index == 66)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 67)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 68)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 69)
                $this->assertTrue(md5(\json_encode($mhsc)) == "6e2b8b2d4c704e08e32772549f627956", "Wrong MD5");
            if ($index == 70)
                $this->assertTrue(md5(\json_encode($mhsc)) == "125a1f01d13ac121015686153a6674f9", "Wrong MD5");
            if ($index == 71)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 72)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 73)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 74)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 75)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 76)
                $this->assertTrue(md5(\json_encode($mhsc)) == "574256f23184f81dc57355a13b6a6e07", "Wrong MD5");
            if ($index == 77)
                $this->assertTrue(md5(\json_encode($mhsc)) == "6ae63b5b2d857cb417cf580f773f45a8", "Wrong MD5");
            if ($index == 78)
                $this->assertTrue(md5(\json_encode($mhsc)) == "19627cfc47a58dc973147738974deefe", "Wrong MD5");
            if ($index == 79)
                $this->assertTrue(md5(\json_encode($mhsc)) == "acc05a9b7b7fce1b590048436c640e8c", "Wrong MD5");
            if ($index == 80)
                $this->assertTrue(md5(\json_encode($mhsc)) == "8fe585308429f200780c0c122589acfd", "Wrong MD5");
            if ($index == 81)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 82)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 83)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 84)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 85)
                $this->assertTrue(md5(\json_encode($mhsc)) == "229704f6eba96b40904c0df821d3c678", "Wrong MD5");
            if ($index == 86)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 87)
                $this->assertTrue(md5(\json_encode($mhsc)) == "6b592ff691581f31eaddb549b887c1d3", "Wrong MD5");
            if ($index == 88)
                $this->assertTrue(md5(\json_encode($mhsc)) == "a289ad624e9e6a875ab2916d7562b7ea", "Wrong MD5");
            if ($index == 89)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 90)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 91)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
            if ($index == 92)
                $this->assertTrue(md5(\json_encode($mhsc)) == "d41d8cd98f00b204e9800998ecf8427e", "Wrong MD5");
        }
    }
}