<?php

declare(strict_types=1);

namespace App\Service\Image\Type;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Api extends Type
{
    protected $pageAtt = 'url';

    private $client;

    /**
     * @param   HttpClientInterface  $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * get url images from api feed links
     *
     * @param   string  $url
     *
     * @return  array array of url images by api
     */
    public function handler(string $url): array
    {
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

        return $this->getImage($items);
    }

    public function getUrlImage($item): string
    {
        return $item->urlToImage;
    }
}
