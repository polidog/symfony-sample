<?php

namespace App\Controller;

use App\Form\InputIsbnType;
use App\Form\RegisterBookMyListType;
use App\Model\BookSearch\BookSearchExecutor;
use App\Model\BookSearch\InputIsbn;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\BookMyList;
use Monolog\DateTimeImmutable;

final class BookSearchController extends AbstractController
{
    private string $book_title = '';

    private string $book_isbn = '';

    private string $book_author = '';


    public function __construct(private BookSearchExecutor $bookSearchExecutor)
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

    #[Route('/book_list_save', name: 'book_list_save')]
    public function save(Request $request): Response
    {
        $book_my_list = new BookMyList();
        $book_my_list->setIsbn($this->book_isbn)
            ->setBookTitle($this->book_title)
            ->setAuthor($this->book_author)
            ->setCreatedAt(new DateTimeImmutable(date("Y-m-d H:i:s")));

        $book_register_form = $this->createFormBuilder($book_my_list)
            ->add('isbn', HiddenType::class)
            ->add('book_title', HiddenType::class)
            ->add('author', HiddenType::class)
            ->add('register', SubmitType::class, [
                'label' => '持ってる本に登録する',
                'attr' => [
                    'class' => 'register-button'
                ]
            ])
            ->getForm();

        $book_register_form->handleRequest($request);
        if ($book_register_form->isSubmitted() && $book_register_form->isValid()) {
            $book_my_list = $book_register_form->getData();
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($book_my_list);
            $manager->flush();
        }

        return $this->redirect('book_my_list/page');
    }

}
