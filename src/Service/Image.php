<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Image
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function run(): array
    {
        $ls = $this->getImageByRSS('http://www.commitstrip.com/en/feed/');
        $ls2 = $this->getImageByAPI('https://newsapi.org/v2/top-headlines?country=us&apiKey=c782db1cd730403f88a544b75dc2d7a0');

        //on fait un de doublonnage
        foreach ($ls as $k => $v) {
            if (empty($f)) $f = array();
            if ($this->doublon($ls, $ls2) == false) $f[$k] = $v;
        }
        foreach ($ls2 as $k2 => $v2) {
            if (empty($f)) $f = array();
            if ($this->doublon($ls2, $ls) == false) $f[$k2] = $v2;
        }

        //recupere dans chaque url l'image
        $j = 0;
        $images = array();
        while ($j < count($f)) {
            if (isset($f[$j])) {
                try {
                    $images[] = $this->getImageInPage($f[$j]['page']);
                } catch (\Exception $e) { /* erreur */
                }
            }
            $j++;
        }

        return $images;
    }

    /**
     * recupere liens flux rss avec images
     */
    public function getImageByRSS(string $url): array
    {
        $images = [];
        $xmlElement = new \SimpleXMLElement($url, LIBXML_NOCDATA, TRUE);
        $items = $xmlElement->channel->item;
        $pageAtt = 'link';

        foreach ($items as $item) {
            $image = [];
            $image['page'] = (string) $item->$pageAtt;
            $itemAttributes = $item->children("media", true)->content->attributes();
            $urlImage = (string) $itemAttributes['url'];
            if (!empty($urlImage) && $this->hasMineTypeAccepted($urlImage)) {
                $image['item'] = $urlImage;
            }

            $images[] = $image;
        }

        return $images;
    }

    /**
     * recpere liens api json avec image
     */
    public function getImageByAPI(string $url): array
    {
        $images = [];
        $response = $this->client->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        );
        $content = json_decode($response->getContent());
        $items = $content->articles;
        $pageAtt = 'url';

        foreach ($items as $item) {
            $image = [];
            $image['page'] = (string) $item->$pageAtt;
            $urlImage = $item->urlToImage;
            if (!empty($urlImage) && $this->hasMineTypeAccepted($urlImage)) {
                $image['item'] = $urlImage;
            }

            $images[] = $image;
        }

        return $images;
    }

    public function hasMineTypeAccepted(string $url): bool
    {
        $mineTypes = ['jpg', 'gif', 'png'];

        foreach ($mineTypes as $mineType) {
            if (substr_count($url, '.' . strtolower($mineType)) > 0 || substr_count($url, '.' . strtoupper($mineType)) > 0) {
                return true;
            }
        }

        return false;
    }

    public function doublon($t1, $t2)
    {
        foreach ($t1 as $k1 => $v1) {
            $doublon = 0;
            foreach ($t2 as $v2) {
                if ($v2 == $v1) {
                    $doublon = 1;
                }
            }
        }
        return $doublon;
    }

    public function getImageInPage(string $url)
    {
        $query = $this->getQueryByURL($url);
        $doc = new \DomDocument();
        @$doc->loadHTMLFile($url);
        $xpath = new \DomXpath($doc);
        $xq = $xpath->query($query);
        $src = $xq[0]->value;

        return $src;
    }

    public function getQueryByURL(string $url): string
    {
        if (strstr($url, "commitstrip.com")) {
            return '//img[contains(@class,"size-full")]/@src';
        }

        return'//img/@src';

    }
}