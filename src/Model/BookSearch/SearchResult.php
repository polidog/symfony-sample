<?php

namespace App\Model\BookSearch;

use App\Entity\BookMyList;

final class SearchResult
{
    public function __construct(private string $title, private string $author, private string $content, private string $isbn, private string $imageUrl)
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function summary(): string
    {
        if (strlen($this->content) > 330) {
            return mb_substr($this->content, 0, 330).'...';
        } else {
            return $this->content;
        }
    }

    public function toEntity(): BookMyList
    {
        return (new BookMyList())
            ->setBookTitle($this->title)
            ->setIsbn($this->isbn)
            ->setAuthor($this->author)
            ->setCreatedAt(new \DateTimeImmutable());
    }

    public static function createFromApi(array $json): self
    {
        return new self(
            $json[0]['onix']['DescriptiveDetail']['TitleDetail']['TitleElement']['TitleText']['content'],
            $json[0]['onix']['DescriptiveDetail']['Contributor'][0]['PersonName']['content'],
            $json[0]['onix']['CollateralDetail']['TextContent'][0]['Text'],
            $json[0]['onix']['RecordReference'],
            $json[0]['onix']['CollateralDetail']['SupportingResource'][0]['ResourceVersion'][0]['ResourceLink']
        );
    }
}
