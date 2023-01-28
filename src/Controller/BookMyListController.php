<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\BookMyList;

final class BookMyListController extends AbstractController
{
    const HEADER = [
        'id',
        'ISBN',
        'book_title',
        'author',
        'created_at'
    ];

    const FILE_NAME = 'book_list.csv';

    #[Route('/book_my_list/page/{page}', name: 'book_my_list/page')]
    public function page($page = 1): Response
    {
        $limit = 9;
        $repository = $this->getDoctrine()->getRepository(BookMyList::class);
        $paginator = $repository->getPage($page, $limit);
        $num_of_books = $paginator->count();

        $maxPages = ceil($num_of_books / $limit);

        return $this->render('book_my_list/index.html.twig', [
            'title' => '持っている本一覧',
            'book_list' => $paginator->getIterator(),
            'maxPages' => $maxPages,
            'thisPage' => $page,
            'num_of_books' => $num_of_books,
        ]);
    }

    #[Route('/book_my_list/csv_download', name: 'csv_download')]
    public function csvDownload()
    {
        $contents = $this->getDoctrine()->getRepository(BookMyList::class)->findAll();

        $fp = fopen('php://output', 'w');

        fputcsv($fp, BookMyListController::HEADER);
        foreach ($contents as $row) {
            fputcsv($fp, [
                $row->getId(),
                $row->getIsbn(),
                $row->getBookTitle(),
                $row->getAuthor(),
                $row->getCreatedAt()->format('Y-m-d H:i:s')
            ], ',', '"');
        }
        fclose($fp);

        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=" . BookMyListController::FILE_NAME);
        header('Content-Transfer-Encoding: binary');
        exit;
    }

}
