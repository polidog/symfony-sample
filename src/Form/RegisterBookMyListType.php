<?php

namespace App\Form;

use App\Model\BookSearch\BookSearchExecutor;
use App\Model\BookSearch\InputIsbn;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterBookMyListType extends AbstractType
{
    public function __construct(private BookSearchExecutor $bookSearchExecutor)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isbn', HiddenType::class, [
                'required' => true,
                'invalid_message' => '10か13桁で数値を入力してください',
                'constraints' => [
                    new Assert\Isbn(),
                ],
            ])
        ;

        $builder->addModelTransformer(new CallbackTransformer(
            function ($data) {
                return $data;
            },
            function ($data) {
                $searchResult = $this->bookSearchExecutor->search((new InputIsbn())->setIsbn($data['isbn']));

                return $searchResult->toEntity();
            }
        )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
