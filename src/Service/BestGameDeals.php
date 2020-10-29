<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;



class BestGameDeals
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchGameDeals($title): array
    {
        $response = $this->client->request(
            'GET',
            'https://www.cheapshark.com/api/1.0/games',
            [
                'query' => [
                    'title' => $title
                ]
            ]
        );

        return $response->toArray();
    }

}