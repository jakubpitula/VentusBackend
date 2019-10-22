<?php

namespace App\Normalizer;

use mysql_xdevapi\Collection;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\Subcategory;
use App\Services\CategoryService;

/**
 * TUTAJ JEST PROBLEM Z CIRCULAR REFERENCE
 */

class SubcategoryNormalizer implements NormalizerInterface
{

    private $container;
    private $categoryService;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        // $this->categoryService = $categoryService;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'category' => $object->getCategory()->getId()
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Subcategory;
    }
}