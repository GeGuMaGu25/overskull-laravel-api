<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class CategoryHasProductsException extends ConflictHttpException
{
    public function __construct()
    {
        parent::__construct('No se puede eliminar una categoría que tiene productos asociados');
    }
}
