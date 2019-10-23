<?php

namespace App\Normalizer;

use mysql_xdevapi\Collection;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\User;
use App\Services\CategoryService;

class UserNormalizer implements NormalizerInterface
{

    private $container;
    private $categoryService;

    public function __construct(ContainerInterface $container, CategoryService $categoryService)
    {
        $this->container = $container;
        $this->categoryService = $categoryService;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId(),
            'email' => $object->getEmail(),
            'first_name' => $object->getFirstName(),
            'gender' => $object->getGender(),
            'picture' => $object->getPictureName(),
            'birthday' => $object->getBirthday(),
            'location' => $object->getLocation(),
            'messenger' => $object->getMessenger(),
            // 'categories' => $this->categoryService->findByUser($object->getId())
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User;
    }
}