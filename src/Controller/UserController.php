<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;;
use App\Services\UserService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    private $userService;
    private $tokenStorage;

    public function __construct(UserService $userService, TokenStorageInterface $tokenStorage)
    {
        $this->userService = $userService;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/api/user", name="api_user")
     * @param integer $id
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getById(Request $request)
    {
        $status = JsonResponse::HTTP_OK;

        $data = [];

        $id = $this->tokenStorage->getToken()->getUser()->getId();

        try {
            $data = $this->userService->findAllById($id);

        } catch (\Exception $exception) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $output = new ConsoleOutput();
            $output->writeln($exception->getMessage());
        }


        return new JsonResponse($data, $status);
    }
}
