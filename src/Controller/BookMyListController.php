<?php

namespace App\Controller;

use App\Entity\BookMyList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

final class BookMyListController extends AbstractController
{
    private const HEADER = [
        'id',
        'ISBN',
        'book_title',
        'author',
        'created_at',
    ];

    private const FILE_NAME = 'book_list.csv';

    #[Route('/book_my_list/page/{page}', name: 'book_my_list/page', requirements: ['page' => '\d+'])]
    public function page(int $page = 1): Response
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
    public function csvDownload(): Response
    {
        // @see https://qiita.com/PruneMazui/items/f9f932bfaf1fb7ebb583
        // @see https://www.karakaram.com/symfony-download-large-csv/
        $response = (new StreamedResponse())->setCallback(function () {
            $contents = $this->getDoctrine()->getRepository(BookMyList::class)->findAll();
            $file = new \SplFileObject('php://output', 'w');

            // BOM入れないとExcelで開く際に文字コード変換が必要になってしまう
            $file->fwrite(pack('C*', 0xEF, 0xBB, 0xBF));
            $file->fputcsv(self::HEADER);

            foreach ($contents as $row) {
                $file->fputcsv([
                    $row->getId(),
                    $row->getIsbn(),
                    $row->getBookTitle(),
                    $row->getAuthor(),
                    $row->getCreatedAt()->format('Y-m-d H:i:s'),
                ], ',', '"');
                flush();
            }
        });

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename='.self::FILE_NAME);

        return $response;
    }
}
