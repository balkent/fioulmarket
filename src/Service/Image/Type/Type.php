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
    protected function hasMineTypeAccepted(string $url): bool
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
    protected function getImageInPage(string $url): ?string
    {
        $doc   = new \DomDocument();
        @$doc->loadHTMLFile($url);
        $xpath = new \DomXpath($doc);
        $xq    = $xpath->query($this->query);

        if ($xq->length > 0) {
            return $xq[0]->value;
        }

        return null;
    }

    protected function getImage($items): array
    {
        $images = [];

        foreach ($items as $item) {
            $att      = $this->pageAtt;
            $images[] = $this->getImageInPage((string) $item->$att);
            $urlImage = $this->getUrlImage($item);

            if (!empty($urlImage) && $this->hasMineTypeAccepted($urlImage)) {
                $images[] = $urlImage;
            }
        }

        return $images;
    }
}
