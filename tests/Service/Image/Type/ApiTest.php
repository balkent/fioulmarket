<?php

namespace App\Tests\Service\Image\Type;

use PHPUnit\Framework\TestCase;
use App\Service\Image\Type\Api;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiTest extends TestCase
{
    private $httpClientInterface;
    private Api $apiService;

    protected function setUp()
    {
        $this->httpClientInterface = $this->prophesize(HttpClientInterface::class);
        $this->apiService = new Api(
            $this->httpClientInterface->reveal()
        );
    }

    public function testGetUrlImage()
    {
        $item = (object) [
            "author"      => "WVTM 13 Digital",
            "title"       => "1 dead, over a dozen injured after devastating tornado in Fultondale - WVTM13",
            "description" => "One person is dead and over a dozen are injured after a tornado tore through Fultondale late Monday night, leaving the area significantly damaged.",
            "url"         => "https://www.wvtm13.com/article/1-dead-over-a-dozen-injured-after-devastating-tornado-in-fultondale-monday-night/35318002",
            "urlToImage"  => "https://kubrick.htvapps.com/htv-prod-media.s3.amazonaws.com/images/fultondale-hotel-1611671046.jpg?crop = 1.00xw: 0.752xh;0,0&resize = 1200: *",
            "publishedAt" => "2021-01-26T14: 24:00Z",
            "urlToImage"  => "https://cdn.vox-cdn.com/9596541/032_Georges_St_Pierre.jpg",
        ];

        $res = $this->apiService->getUrlImage($item);

        $this->assertEquals("https://cdn.vox-cdn.com/9596541/032_Georges_St_Pierre.jpg", $res);
    }
}
