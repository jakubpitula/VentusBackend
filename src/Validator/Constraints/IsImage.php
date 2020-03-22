<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsImage extends Constraint
{
    public $message = 'Uploaded file is not an image.';
}