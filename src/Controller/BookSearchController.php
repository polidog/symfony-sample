<?php

namespace App\Controller;

use App\Entity\BookMyList;
use App\Form\InputIsbnType;
use App\Form\RegisterBookMyListType;
use App\Model\BookSearch\BookSearchExecutor;
use App\Model\BookSearch\InputIsbn;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BookSearchController extends AbstractController
{
    private string $book_title = '';

    private string $book_isbn = '';

    private string $book_author = '';

    public function __construct(private BookSearchExecutor $bookSearchExecutor, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/book_search', name: 'book_search', methods: ['GET'])]
    #[Template('book_search/index.html.twig')]
    public function index(Request $request): array
    {
        $model = new InputIsbn();
        $form = $this->createForm(InputIsbnType::class, $model);
        $form->handleRequest($request);

        $searchResult = match ($form->isSubmitted() && $form->isValid()) {
            true => $this->bookSearchExecutor->search($model),
            false => null,
        };

        $registerForm = match ($searchResult) {
            null => null,
            default => $this->createForm(RegisterBookMyListType::class, ['isbn' => $searchResult->getIsbn()]),
        };

        return [
            'title' => 'ISBN検索画面',
            'form' => $form->createView(),
            'registerForm' => $registerForm?->createView(),
            'searchResult' => $searchResult,
            'book_title' => '',
        ];
    }

    #[Route('/book_list_save', name: 'book_list_save', methods: ['POST'])]
    public function save(Request $request): Response
    {
        $form = $this->createForm(RegisterBookMyListType::class);
        $form->handleRequest($request);
        if (!$form->isValid()) {
            // TODO エラーメッセージ
            return $this->redirect('book_search');
        }
        /** @var BookMyList $bookMyList */
        $bookMyList = $form->getData();
        $this->entityManager->persist($bookMyList);
        $this->entityManager->flush();

        return $this->redirect('book_my_list/page');
    }
}
