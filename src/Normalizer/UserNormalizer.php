<?php

namespace App\Normalizer;

use mysql_xdevapi\Collection;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\User;

class UserNormalizer implements NormalizerInterface
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId(),
            'email' => $object->getEmail(),
            'facebook_id' => $object->getFacebookId(),
            'first_name' => $object->getFirstName(),
            'last_name' => $object->getLastName(),
            'gender' => $object->getGender(),
            'picture_url' => $object->getPictureUrl(),
            'birthday' => $object->getBirthday(),
            'location' => $object->getLocation()
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User;
    }
}