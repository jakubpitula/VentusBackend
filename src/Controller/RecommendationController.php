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

/**
 * @Route("/api/user")
 */
class RecommendationController extends AbstractController
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
     * @Route("/recommendations", name="recommendations", methods={"POST", "GET"})
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
     * @Route("/recommended_subcategories", name="recommended_subcategories", methods={"POST"})
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

    public function __toString() {
        return '';
    }
}
