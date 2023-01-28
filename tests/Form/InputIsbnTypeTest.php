<?php

namespace App\Tests\Form;

use App\Form\InputIsbnType;
use App\Model\BookSearch\InputIsbn;
use Symfony\Component\Form\Test\TypeTestCase;

class InputIsbnTypeTest extends TypeTestCase
{
    /**
     * @return void
     */
    public function testSubmitdData(): void
    {
        $isbn = '978-4798161501';
        $formData = [
            'isbn' => $isbn,
        ];

        $model = new InputIsbn();
        $form = $this->factory->create(InputIsbnType::class, $model);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());

        $this->assertSame($isbn, $model->getIsbn());
    }


}
