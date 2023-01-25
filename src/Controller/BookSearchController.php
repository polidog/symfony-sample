<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\BookMyList;
use Monolog\DateTimeImmutable;

class BookSearchController extends AbstractController
{
    private string $book_title = '';

    private string $book_content = '';

    private string $book_image = '';

    private string $book_isbn = '';

    private string $book_author = '';

    #[Route('/book_search', name: 'book_search')]
    public function index(Request $request): Response
    {
         $book_search_form = $this->createFormBuilder()
             ->add('search_word', NumberType::class, [
                 'label' => false,
                 'required' => true,
                 'invalid_message' => '10か13桁で数値を入力してください',
                 'attr' => [
                     'placeholder' => 'ISBNコードを入力 ',
                     'class' => 'input'
                 ]
             ])
             ->add('search_button', SubmitType::class, [
                 'label' => '検索する',
                 'attr' => [
                     'class' => 'form_search'
                 ]
             ])
             ->getForm();

        $book_search_form->handleRequest($request);
        $search_word = $book_search_form->get('search_word')->getData();
        if (!is_null($search_word)) {
            $this->bookSearchApiExec($search_word);
        }

        $book_my_list = new BookMyList();
        $book_my_list->setIsbn($this->book_isbn)
            ->setBookTitle($this->book_title)
            ->setAuthor($this->book_author);

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

        return $this->render('book_search/index.html.twig', [
            'title'              => 'ISBN検索画面',
            'book_search_form'   => $book_search_form->createView(),
            'book_register_form' => $book_register_form->createView(),
            'book_title'         => $this->book_title,
            'book_content'       => $this->book_content,
            'book_image'         => $this->book_image,
        ]);
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

    private function bookSearchApiExec(string $isbn) {
        $url = 'https://api.openbd.jp/v1/get?isbn=' . $isbn;

        $ch = curl_init(); //開始

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $result   = json_decode($response, true);

        curl_close($ch);
        $this->book_title   = $result[0]['onix']['DescriptiveDetail']['TitleDetail']['TitleElement']['TitleText']['content'];
        $this->book_isbn    = $result[0]['onix']['RecordReference'];
        $this->book_author  = $result[0]['onix']['DescriptiveDetail']['Contributor'][0]['PersonName']['content'];
        $this->book_content = $result[0]['onix']['CollateralDetail']['TextContent'][1]['Text'];
        $this->book_image   = $result[0]['onix']['CollateralDetail']['SupportingResource'][0]['ResourceVersion'][0]['ResourceLink'];
    }
}
