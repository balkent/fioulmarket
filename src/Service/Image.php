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

    public function run(array $urls): array
    {
        $imageList = [];
        foreach ($urls as $url => $type) {
            $imageList = array_merge($imageList, $this->getImage($url, $type));
        }

        return array_unique($imageList);
    }

    public function getImage(string $url, string $type): array
    {
        switch ($type) {
            case 'RSS':
                return $this->getImageByRSS($url);
                break;

            case 'API':
                return $this->getImageByAPI($url);
                break;
        }

        return [];
    }


    /**
     * recupere liens flux rss avec images
     */
    public function getImageByRSS(string $url): array
    {
        $images     = [];
        $xmlElement = new \SimpleXMLElement($url, LIBXML_NOCDATA, TRUE);
        $items      = $xmlElement->channel->item;
        $pageAtt    = 'link';

        foreach ($items as $item) {
            $images[]       = $this->getImageInPage((string) $item->$pageAtt);
            $itemAttributes = $item->children("media", true)->content->attributes();
            $urlImage       = (string) $itemAttributes['url'];

            if (!empty($urlImage) && $this->hasMineTypeAccepted($urlImage)) {
                $images[] = $urlImage;
            }
        }

        return $images;
    }

    /**
     * recpere liens api json avec image
     */
    public function getImageByAPI(string $url): array
    {
        $images   = [];
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
        $items   = $content->articles;
        $pageAtt = 'url';

        foreach ($items as $item) {
            $images[] = $this->getImageInPage((string) $item->$pageAtt);
            $urlImage = $item->urlToImage;
            if (!empty($urlImage) && $this->hasMineTypeAccepted($urlImage)) {
                $images[] = $urlImage;
            }
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

    public function getImageInPage(string $url)
    {
        $query = $this->getQueryByURL($url);
        $doc   = new \DomDocument();
        @$doc->loadHTMLFile($url);
        $xpath = new \DomXpath($doc);
        $xq    = $xpath->query($query);

        if ($xq->length > 0) {
            return $xq[0]->value;
        }

        return null;
    }

    public function getQueryByURL(string $url): string
    {
        if (strstr($url, "commitstrip.com")) {
            return '//img[contains(@class,"size-full")]/@src';
        }

        return'//img/@src';

    }
}
