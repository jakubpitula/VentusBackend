<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\HttpFoundation\JsonResponse;

class IsImageValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof IsImage) {
            return new JsonResponse(['error' => 'Wrong constraint type'], 500);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof FileObject) {
            $value = new FileObject($value);
        }

        $mimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/tiff'
        ];
        $mime = $value->getMimeType();

        foreach ($mimeTypes as $mimeType) {
            if ($mimeType === $mime) {
                return;
            }

            if ($discrete = strstr($mimeType, '/*', true)) {
                if (strstr($mime, '/', true) === $discrete) {
                    return;
                }
            }
        }

        return new JsonResponse(['error' => 'Uploaded file is not an image.'], 400);
    }
}