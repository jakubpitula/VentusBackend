<?php

namespace App\Services;

use App\Normalizer\CategoryNormalizer;
use App\Repository\CategoryRepository;

class CategoryService
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var php
     */
    private $categoryNormalizer;

    /**
     * categoryService constructor.
     * @param CategoryRepository $categoryRepository
     * @param php $normalizer
     */
    public function __construct(CategoryRepository $categoryRepository, CategoryNormalizer $normalizer)
    {
        $this->categoryRepository = $categoryRepository;
        $this->categoryNormalizer = $normalizer;
    }

    /**
     * @param int $id
     * @return array
     * @throws \App\Exception\InvalidcategoryName
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function findAllById(?int $id)
    {
        $data = $this->categoryRepository->findById($id);
        $normalized = [];

        foreach ($data as $d) {
            $normalized[] = $this->categoryNormalizer->normalize($d);
        }
        return empty($normalized) ? $normalized : $normalized[0];
    }

    public function findAll()
    {
        $data = $this->categoryRepository->findAll();
        $normalized = [];

        foreach ($data as $d) {
            $normalized[] = $this->categoryNormalizer->normalize($d);
        }

        return $normalized;
    }

    public function findBySubcategory($sub)
    {
        $data = $this->categoryRepository->findById($id);
        $normalized = [];

        foreach ($data as $d) {
            $normalized[] = $this->categoryNormalizer->normalize($d);
        }
        return empty($normalized) ? $normalized : $normalized[0];
    }
}
