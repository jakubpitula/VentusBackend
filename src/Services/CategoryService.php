<?php

namespace App\Services;

use App\Normalizer\CategoryNormalizer;
use App\Repository\CategoryRepository;
use App\Normalizer\CategoryIndexNormalizer;

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
     * @var php
     */
    private $categoryIndexNormalizer;

    /**
     * categoryService constructor.
     * @param CategoryRepository $categoryRepository
     * @param php $normalizer
     */
    public function __construct(CategoryRepository $categoryRepository, CategoryNormalizer $normalizer, CategoryIndexNormalizer $categoryIndexNormalizer)
    {
        $this->categoryRepository = $categoryRepository;
        $this->categoryNormalizer = $normalizer;
        $this->categoryIndexNormalizer = $categoryIndexNormalizer;
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
            $normalized[] = $this->categoryIndexNormalizer->normalize($d);
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

    public function findByName(?string $name)
    {
        $data = $this->categoryRepository->findBy(['name' =>  $name]);
        $normalized = [];

        foreach ($data as $d) {
            $normalized[] = $this->categoryNormalizer->normalize($d);
        }
        return empty($normalized) ? $normalized : $normalized[0];
    }

    // public function findAllByUser($user)
    // {
    //     $data = $this->categoryRepository->findBy
    // }
}
