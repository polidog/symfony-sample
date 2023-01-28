<?php

namespace App\Tests\Model\BookSearch;

use App\Model\BookSearch\InputIsbn;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InputIsbnTest extends KernelTestCase
{
    /**
     * @dataProvider dpIsbn
     */
    public function testValidation(string $isbn, bool $expected): void
    {
        $model = new InputIsbn();
        $model->setIsbn($isbn);

        $validator = self::bootKernel()->getContainer()->get('validator');
        $errors = $validator->validate($model);

        self::assertSame($expected, 0 === count($errors));
    }

    public function dpIsbn(): array
    {
        return [
            [
                '978-4798161501', // isbn 13
                true,
            ],
            [
                '4798161500', // isbn 10
                true,
            ],
            [
                'abc',
                false,
            ],
        ];
    }
}
