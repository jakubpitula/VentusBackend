<?php

namespace App\Services;

use App\Normalizer\SubcategoryNormalizer;
use App\Repository\SubcategoryRepository;

class SubcategoryService
{
    /**
     * @var SubcategoryRepository
     */
    private $subcategoryRepository;

    /**
     * @var php
     */
    private $subcategoryNormalizer;

    /**
     * SubcategoryService constructor.
     * @param SubcategoryRepository $subcategoryRepository
     * @param php $normalizer
     */
    public function __construct(SubcategoryRepository $subcategoryRepository, SubcategoryNormalizer $normalizer)
    {
        $this->subcategoryRepository = $subcategoryRepository;
        $this->subcategoryNormalizer = $normalizer;
    }

    /**
     * @param int $id
     * @return array
     * @throws \App\Exception\InvalidsubcategoryName
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function findAllById(?int $id)
    {
        $data = $this->subcategoryRepository->findById($id);
        $normalized = [];

        foreach ($data as $d) {
            $normalized[] = $this->subcategoryNormalizer->normalize($d);
        }
        return empty($normalized) ? $normalized : $normalized[0];
    }

    public function findAllByCategory($cat)
    {
        $data = $this->subcategoryRepository->findByCategory($cat);
        $normalized = [];

        foreach ($data as $d) {
            $normalized[] = $this->subcategoryNormalizer->normalize($d);
        }

        return $normalized;
    }
}
