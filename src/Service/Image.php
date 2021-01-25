<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class for get images by url
 */
class Image
{
    private $client;

    /**
     * @param   HttpClientInterface  $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * entrypoint of this class
     *
     * @param   array  $urls  array of url to get images
     *
     * @return  array  array of sort and unique url image
     */
    public function run(array $urls): array
    {
        $imageList = [];
        foreach ($urls as $url => $type) {
            $imageList = array_merge($imageList, $this->getImage($url, $type));
        }

        return array_unique($imageList);
    }

    /**
     * switcher for type of url
     *
     * @param   string  $url
     * @param   string  $type  RSS|API
     *
     * @return  array empty|array of images by url
     */
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
     * get url images from rss feed links
     *
     * @param   string  $url
     *
     * @return  array  array of url images by rss
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
     * get url images from api feed links
     *
     * @param   string  $url
     *
     * @return  array array of url images by api
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

    /**
     * for yes or no url is accepted image type
     *
     * @param   string  $url
     *
     * @return  bool
     */
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

    /**
     * get the image url by web page
     *
     * @param   string  $url
     *
     * @return  null|string
     */
    public function getImageInPage(string $url): ?string
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

    /**
     * get the query for get image on web
     *
     * @param   string  $url
     *
     * @return  string  the query
     */
    public function getQueryByURL(string $url): string
    {
        if (strstr($url, "commitstrip.com")) {
            return '//img[contains(@class,"size-full")]/@src';
        }

        return'//img/@src';

    }
}
