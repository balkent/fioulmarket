<?php

declare(strict_types=1);

namespace App\Service\Image\Type;

class Rss extends Type
{
    /**
     * get url images from rss feed links
     *
     * @param   string  $url
     *
     * @return  array  array of url images by rss
     */
    public function handler(string $url): array
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
}
