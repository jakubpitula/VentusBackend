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
use Aws\S3\S3Client;

class UserController extends AbstractController
{
    private $userService;
    private $tokenStorage;
    private $userManager;
    private $em;
    private $categoryRepository;
    private $subcategoryRepository;
    private $subcategoryService;
    private $s3Client;

    public function __construct(S3Client $s3Client, SubcategoryService $subcategoryService, SubcategoryRepository $subcategoryRepository, EntityManagerInterface $em, UserService $userService, TokenStorageInterface $tokenStorage, CategoryRepository $categoryRepository, UserManagerInterface $userManager)
    {
        $this->userService = $userService;
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
        $this->subcategoryRepository = $subcategoryRepository;
        $this->subcategoryService = $subcategoryService;
        $this->s3Client = $s3Client;
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
     * @Route("/api/user/{id}", name="api_user_id")
     * @param integer $id
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getById(Request $request, $id)
    {
        try {
            $friend = $this->userManager->findUserBy(['id' => $id]);

        } catch (\Exception $exception) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $output = new ConsoleOutput();
            $output->writeln($exception->getMessage());
            return new JsonResponse([], $status);
        }
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
        foreach(array_slice($differences, 0, 20) as $diff){
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
        $recommendation = intval(($matchesEqualized + ($matchPercent*$matchWeight))/(1+$matchWeight));
        // dd($recommendation);

        $subcategories = [];
        foreach($matches as $match){
            $subcategories[] = [
                'name' => $match->getName(),
                'percentage' => $friendPercentages[$match->getId()]
            ];
        }

        $response = [
            'id' => $friend->getId(),
            'picture' => 'https://ventusapi.s3.amazonaws.com/pictures/'.$object->getPictureName(),
            'name' => $friend->getFirstName(),
            'location' => $friend->getLocation(),
            'birthday' => $friend->getBirthday(),
            'match' => $recommendation,
            'subcategories' => $subcategories
        ];
        return new JsonResponse($response);
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

        if($data === null) return new JsonResponse(['error' => 'No data in request'], 400);

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

        if(!isset($data['subcategory'])) return new JsonResponse(['error' => 'No subcategory set'], 400);
        if(!isset($data['percentage'])) return new JsonResponse(['error' => 'No percentages set'], 400);

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
