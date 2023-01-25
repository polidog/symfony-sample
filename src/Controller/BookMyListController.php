<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\BookMyList;

class BookMyListController extends AbstractController
{
    #[Route('/book_my_list/page/{page}', name: 'book_my_list/page')]
    public function page($page = 1): Response
    {
        $limit = 1;
        $repository = $this->getDoctrine()->getRepository(BookMyList::class);
        $paginator = $repository->getPage($page, $limit);
        $maxPages = ceil($paginator->count() / $limit);

        return $this->render('book_my_list/index.html.twig', [
            'title' => '持っている本一覧',
            'book_list' => $paginator->getIterator(),
            'maxPages' => $maxPages,
            'thisPage' => $page,
//            'empty_row_len' => $empty_row_len
            'empty_row_len' => 5
        ]);
    }
}
