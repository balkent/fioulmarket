<?php

declare(strict_types=1);

namespace App\Service\Image\Type;

class Type
{
    private $mineTypes = ['jpg', 'gif', 'png'];

    /**
     * for yes or no url is accepted image type
     *
     * @param   string  $url
     *
     * @return  bool
     */
    public function hasMineTypeAccepted(string $url): bool
    {
        foreach ($this->mineTypes as $mineType) {
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

        return '//img/@src';
    }
}
