<?php

namespace App\Normalizer;

use mysql_xdevapi\Collection;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\SubcategoryRepository;

class UserNormalizer implements NormalizerInterface
{

    private $container;
    private $categoryRepository;
    private $subcategoryRepository;

    public function __construct(ContainerInterface $container, CategoryRepository $categoryRepository, SubcategoryRepository $subcategoryRepository)
    {
        $this->container = $container;
        $this->categoryRepository = $categoryRepository;
        $this->subcategoryRepository = $subcategoryRepository;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $categories = $this->categoryRepository->findByUser(['user' => $object->getId()]);

        $categoryIds = [];
        foreach ($categories as $cat){
            $categoryIds[] = $cat->getId();
        }

        $presubcategories = $this->subcategoryRepository->findByUser(['user' => $object->getId()]);

        $subcategories = [];
        foreach ($presubcategories as $sub){
            $subcategories[] = [
                'id' => $sub->getId(),
                'category' => $sub->getCategory()->getId(),
                'percentage' => $object->getPercentages()[$sub->getId()]
            ];
        }

        return [
            'id' => $object->getId(),
            'email' => $object->getEmail(),
            'first_name' => $object->getFirstName(),
            'gender' => $object->getGender(),
            'picture' => 'https://ventusapi.s3.amazonaws.com/pictures/'.$object->getPictureName(),
            'birthday' => $object->getBirthday(),
            'location' => $object->getLocation(),
            'messenger' => $object->getMessenger(),
            'categories' => $categoryIds,
            'subcategories' => $subcategories,
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User;
    }
}