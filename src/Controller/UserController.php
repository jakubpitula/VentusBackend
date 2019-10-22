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
use FOS\UserBundle\Model\UserManagerInterface;

class UserController extends AbstractController
{
    private $userService;
    private $tokenStorage;
    private $userManager;

    public function __construct(UserService $userService, TokenStorageInterface $tokenStorage, UserManagerInterface $userManager)
    {
        $this->userService = $userService;
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/api/user", name="api_user")
     * @param integer $id
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getByToken(Request $request)
    {
        $status = JsonResponse::HTTP_OK;

        $data = [];
        $user = $this->tokenStorage->getToken()->getUser();

        if(!property_exists($user, 'id')){
            $response = new JsonResponse($data,$status);

            return $response;
        }

        $id = $user->getId();

        try {
            $data = $this->userService->findAllById($id);

        } catch (\Exception $exception) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $output = new ConsoleOutput();
            $output->writeln($exception->getMessage());
        }


        $response = new JsonResponse($data,$status);
        return $response;
    }

    /**
     * @Route("/api/user/{id}", name="api_user_id")
     * @param integer $id
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getById(Request $request, $id)
    {
        $status = JsonResponse::HTTP_OK;

        $data = [];

        try {
            $data = $this->userService->findAllById($id);

        } catch (\Exception $exception) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $output = new ConsoleOutput();
            $output->writeln($exception->getMessage());
        }


        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/check_email", name="email_check", methods={"POST", "GET"})
     */
    public function checkEmail(Request $request){
        $data = $this->userManager->findUserByEmail($request->request->get('email'));
        if($data === null){
            $response = ['status' => 'register'];
        }
        else $response = ['status' => 'login'];
        
        return new JsonResponse($response);
    }
}
