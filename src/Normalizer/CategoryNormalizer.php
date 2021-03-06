<?php

namespace App\Normalizer;

use mysql_xdevapi\Collection;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\Category;
use App\Services\SubcategoryService;

class CategoryNormalizer implements NormalizerInterface
{

    private $container;
    private $subcategoryService;

    public function __construct(ContainerInterface $container, SubcategoryService $subcategoryService)
    {
        $this->container = $container;
        $this->subcategoryService = $subcategoryService;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'subcategories' => $this->subcategoryService->findAllByCategory($object->getId())
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Category;
    }
}