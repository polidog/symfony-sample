<?php

namespace App\Model\BookSearch;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Validator\Constraints as Assert;

final class InputIsbn
{
    #[Assert\Isbn(
        message: '10か13桁で数値を入力してください',
    )]
    private ?string $isbn = null;

    /**
     * @return string
     */
    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     */
    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function search(Client $client, string $baseUrl): SearchResult
    {
        $url = $baseUrl.$this->isbn;
        $response = $client->request('GET', $url);
        $body = $response->getBody();
        $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        return SearchResult::createFromApi($json);
    }
}
