<?php

declare(strict_types=1);

namespace App\Service\Image\Type;

class Rss extends Type
{
    protected $pageAtt = 'link';
    protected $query = '//img[contains(@class,"size-full")]/@src';

    /**
     * get url images from rss feed links
     *
     * @param   string  $url
     *
     * @return  array  array of url images by rss
     */
    public function handler(string $url): array
    {
        $xmlElement = new \SimpleXMLElement($url, LIBXML_NOCDATA, TRUE);
        $items      = $xmlElement->channel->item;

        return $this->getImage($items);
    }

    public function getUrlImage($item): string
    {
        $itemAttributes = $item->children("media", true)->content->attributes();

        return (string) $itemAttributes['url'];
    }
}
