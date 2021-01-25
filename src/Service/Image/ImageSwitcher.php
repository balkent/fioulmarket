<?php

declare(strict_types=1);

namespace App\Service\Image;

use App\Service\Image\Type\Rss;
use App\Service\Image\Type\Api;

class ImageSwitcher
{
    /**
     * @var App\Service\Image\Type\Rss
     */
    private $rssImage;

    /**
     * @var App\Service\Image\Type\Api
     */
    private $apiImage;

    /**
     * @param   Rss  $rssImage
     * @param   Api  $apiImage
     */
    public function __construct(
        Rss $rssImage,
        Api $apiImage
    )
    {
        $this->rssImage = $rssImage;
        $this->apiImage = $apiImage;
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
                return $this->rssImage->handler($url);
                break;

            case 'API':
                return $this->apiImage->handler($url);
            break;
        }

        return [];
    }
}
