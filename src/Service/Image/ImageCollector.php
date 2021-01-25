<?php

declare(strict_types=1);

namespace App\Service\Image;

/**
 * Class for get images by url
 */
class ImageCollector
{
    private $imageSwitcher;

    /**
     * @param   ImageSwitcher  $imageSwitcher
     */
    public function __construct(ImageSwitcher $imageSwitcher)
    {
        $this->imageSwitcher = $imageSwitcher;
    }

    /**
     * entrypoint of this class
     *
     * @param   array  $urls  array of url to get images
     *
     * @return  array  array of sort and unique url image
     */
    public function handler(array $urls): array
    {
        $imageList = [];
        foreach ($urls as $url => $type) {
            $imageList = array_merge($imageList, $this->imageSwitcher->getImage($url, $type));
        }

        return array_unique($imageList);
    }
}
