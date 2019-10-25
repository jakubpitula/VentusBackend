<?php

namespace App\Services;

use App\Normalizer\UserNormalizer;
use App\Repository\UserRepository;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var php
     */
    private $userNormalizer;

    /**
     * userService constructor.
     * @param UserRepository $userRepository
     * @param php $normalizer
     */
    public function __construct(UserRepository $userRepository, UserNormalizer $normalizer)
    {
        $this->userRepository = $userRepository;
        $this->userNormalizer = $normalizer;
    }

    /**
     * @param int $id
     * @return array
     * @throws \App\Exception\InvaliduserName
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function findAllById(?int $id)
    {
        $data = $this->userRepository->findById($id);
        
        $normalized = [];

        foreach ($data as $d) {
            $normalized[] = $this->userNormalizer->normalize($d);
        }
        return empty($normalized) ? $normalized : $normalized[0];
    }
}
