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
    public function getRecommendations(Request $request)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if(!property_exists($user, 'id')){
            $response = new JsonResponse($data,405);

            return $response;
        }

        $dataAll = $user->getCategories();
        $data = [];
        foreach ($dataAll as $d){
            $data[] = $d->getId();
        }

        $mySubcategories = $this->subcategoryService->findByUserAndCategories($user, $data);
        $myPercentages = $user->getPercentages();

        $friends = [];
        $recommendations = [];

        foreach($mySubcategories as $sub){
            foreach($sub->getUsers() as $u){
                if($u->getId() != $user->getId() && !in_array($u, $friends)) $friends[] = $u;
            }
        }

        foreach($friends as $friend){

            $friendSubcategories = $this->subcategoryService->findByUserAndCategories($friend, $data);
            $friendPercentages = $friend->getPercentages();

            $matches = [];

            foreach($mySubcategories as $my){
                if(in_array($my, $friendSubcategories)) $matches[] = $my;
            }

            $myMatchPercent = count($matches)/count($mySubcategories)*100;
            $friendMatchPercent = count($matches)/count($friendSubcategories)*100;

            $differences = [];

            foreach($matches as $match){
                $diff = abs($myPercentages[$match->getId()] - $friendPercentages[$match->getId()]);
                $interest = ($myPercentages[$match->getId()] + $friendPercentages[$match->getId()])/2;
                $equalized = $interest*(100-$diff)/100;

                $differences[] = [
                    'subcategory' => $match->getId(),
                    'diff' => $diff,
                    'interest' => $interest,
                    'equalized' => $equalized
                ];
            }

            usort($differences, function($a, $b){
                return $b['equalized'] - $a['equalized'];
            });

            $topSubcategories = [];
            foreach(array_slice($differences, 0, 5) as $diff){
                $topSubcategories[]=[
                    'name' => $this->subcategoryService->findAllById($diff['subcategory'])['name'],
                    'percentage' => $friendPercentages[$diff['subcategory']]
                ];
            }
            
            $matchSum = 0;
            foreach($differences as $match){
                $matchSum+=$match['equalized'];
            }

            $matchesEqualized = $matchSum/count($matches);
            $matchPercent = ($myMatchPercent + $friendMatchPercent)/2;
            $matchWeight = count($matches)/5;

            $recommendation = ($matchesEqualized + ($matchPercent*$matchWeight))/(1+$matchWeight);

            $recommendations[] = [
                'id' => $friend->getId(),
                'name' => $friend->getFirstName(),
                'location' => $friend->getLocation(),
                'match' => intval($recommendation),
                'top' => $topSubcategories
            ];
        }

        usort($recommendations, function($a, $b){
            return $b['match'] - $a['match'];
        });

        return new JsonResponse($recommendations);
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
