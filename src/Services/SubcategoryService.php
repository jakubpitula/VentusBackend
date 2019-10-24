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

    public function findAllByCategories($cats)
    {
        $res = [];
        $normalized = [];
        foreach ($cats as $cat){
            if(null != $this->subcategoryRepository->findByCategory($cat)){
                $r = $this->subcategoryRepository->findByCategory($cat);
                $res[] = $r;
            }
        }

        foreach ($res as $re) {
            foreach($re as $r){
                $normalized[] = $this->subcategoryNormalizer->normalize($r);
            }
        }   

        usort($normalized, function($a, $b) {
            return strcmp(
                count($this->subcategoryRepository->findById($b['id'])[0]->getUsers()),
                count($this->subcategoryRepository->findById($a['id'])[0]->getUsers())
            );
        });

       return $normalized;
    }

    public function findByName(?string $name)
    {
        $data = $this->subcategoryRepository->findBy(['name' =>  $name]);
        $normalized = [];

        foreach ($data as $d) {
            $normalized[] = $this->subcategoryNormalizer->normalize($d);
        }
        return empty($normalized) ? $normalized : $normalized[0];
    }

    public function findByUserAndCategories($user, $cats)
    {
        $res = [];

        foreach($cats as $cat){
            if(null != $this->subcategoryRepository->findByUserAndCategory($user, $cat)){
                $r = $this->subcategoryRepository->findByUserAndCategory($user, $cat);
                $res[] = $r[0];
            }
        }

        return $res;
    }
}
