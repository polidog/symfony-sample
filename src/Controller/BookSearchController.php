<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookSearchController extends AbstractController
{
    #[Route('/book/search', name: 'app_book_search')]
    public function index(): Response
    {
        return $this->render('book_search/index.html.twig', [
            'controller_name' => 'BookSearchController',
        ]);
    }
}
