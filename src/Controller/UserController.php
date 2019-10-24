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
use App\Repository\CategoryRepository;
use App\Repository\SubcategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use App\Services\SubcategoryService;

class UserController extends AbstractController
{
    private $userService;
    private $tokenStorage;
    private $userManager;
    private $em;
    private $categoryRepository;
    private $subcategoryRepository;
    private $subcategoryService;

    public function __construct(SubcategoryService $subcategoryService, SubcategoryRepository $subcategoryRepository, EntityManagerInterface $em, UserService $userService, TokenStorageInterface $tokenStorage, CategoryRepository $categoryRepository, UserManagerInterface $userManager)
    {
        $this->userService = $userService;
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
        $this->subcategoryRepository = $subcategoryRepository;
        $this->subcategoryService = $subcategoryService;
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
            $response = new JsonResponse($data,405);

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
     * @Route("/api/user/recommendations", name="recommendations", methods={"POST", "GET"})
     */
    public function getRecommendations(Request $request){
        $data = json_decode($request->getContent(), true);

        $user = $this->tokenStorage->getToken()->getUser();

        $subcategories = $this->subcategoryService->findByUserAndCategories($user, $data);
        dd($subcategories);

        if(!property_exists($user, 'id')){
            $response = new JsonResponse($data,405);

            return $response;
        }
    }

    /**
     * @Route("/api/user/recommended_subcategories", name="recommended_subcategories", methods={"POST"})
     */
    public function getRecommendedSubcategories()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if(!property_exists($user, 'id')){
            $response = new JsonResponse($data,405);

            return $response;
        }

        $categories = $this->categoryRepository->findByUser(['user' => $user->getId()]);
        $subcategories = $this->subcategoryService->findAllByCategories($categories);

        return new JsonResponse($subcategories);
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
    public function checkEmail(Request $request)
    {
        $data = $this->userManager->findUserByEmail($request->request->get('email'));
        if($data === null){
            $response = ['status' => 'register'];
        }
        else $response = ['status' => 'login'];
        
        return new JsonResponse($response);
    }

    /**
     * @Route("/api/user/category/new", name="api_user_category_new", methods={"POST"})
     */
    public function newCategory(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->tokenStorage->getToken()->getUser();

        if(!property_exists($user, 'id')){
            $response = new JsonResponse($data,405);

            return $response;
        }

        foreach($data as $cat){
            if(null!==$this->categoryRepository->findOneBy(['id' => $cat])){
                $user->addCategory($this->categoryRepository->findOneBy(['id' => $cat]));
            }
            else return new JsonResponse(['error' => "Category doesn't exist"], 418);
        }

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/api/user/subcategory/new", name="api_user_subcategory_new", methods={"POST"})
     */
    public function newSubcategory(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->tokenStorage->getToken()->getUser();

        if(!property_exists($user, 'id')){
            $response = new JsonResponse($data,405);

            return $response;
        }

        $sub = $data['subcategory'];

        $isSubSet = false;
        foreach($user->getSubcategories() as $subcat){
            if($subcat->getId() === $sub) $isSubSet = true;
        }

        if(null!==$this->subcategoryRepository->findOneBy(['id' => $sub])){
            $user->addSubcategory($this->subcategoryRepository->findOneBy(['id' => $sub]));
            if(!$isSubSet) $user->setPercentages($data['subcategory'], intval($data['percentage']));
        }
        else return new JsonResponse(['error' => "Subcategory doesn't exist"], 418);


        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse([], 200);
    }

    public function __toString() {
        return '';
    }
}
