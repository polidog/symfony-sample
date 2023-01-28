<?php

namespace App\Model\BookSearch;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class BookSearchExecutor
{
    private const BASE_URI = 'https://api.openbd.jp/v1/get?isbn=';

    public function __construct(private Client $client)
    {
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function search(InputIsbn $inputIsbn): SearchResult
    {
        return $inputIsbn->search($this->client, self::BASE_URI);
    }
}
