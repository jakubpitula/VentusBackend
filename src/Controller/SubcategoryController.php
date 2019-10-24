<?php

namespace App\Controller;

use App\Entity\Subcategory;
use App\Form\SubcategoryType;
use App\Repository\SubcategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\SubcategoryService;

/**
 * @Route("/api/subcategory")
 */
class SubcategoryController extends AbstractController
{
    private $subcategoryRepository;
    private $em;
    private $subcategoryService;

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $em, SubcategoryService $subcategoryService)
    {
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->subcategoryService = $subcategoryService;
    }

    /**
     * @Route("/", name="subcategory_index", methods={"GET"})
     */
    // public function index(SubcategoryRepository $subcategoryRepository): Response
    // {
    //     return $this->render('subcategory/index.html.twig', [
    //         'subcategories' => $subcategoryRepository->findAll(),
    //     ]);
    // }

    /**
     * @Route("/new", name="subcategory_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        $data = $request->request->all();

        $subcategory = new Subcategory();

        if(!isset($data['name'])) return new JsonResponse(['error' => 'Name not set'], 400);
        if(!isset($data['category'])) return new JsonResponse(['error' => 'Category not set'], 400);

        $isNameUnique = $this->subcategoryService->findByName($data['name']);
        if($isNameUnique != null) return new JsonResponse(['error' => 'Subcategory name already in use'], 400);

        $category = $this->categoryRepository->findOneBy(['id' => $data['category']]);

        if($category !== null) $subcategory->setCategory($category);
        else return new JsonResponse(['error' => "Category doesn't exist"], 418);

        $subcategory->setName($data['name']);

        $this->em->persist($subcategory);
        $this->em->flush();

        return new JsonResponse([], 201);
    }

    /**
     * @Route("/{id}", name="subcategory_show", methods={"GET"})
     */
    public function show(Subcategory $subcategory): Response
    {
        return $this->render('subcategory/show.html.twig', [
            'subcategory' => $subcategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="subcategory_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Subcategory $subcategory): Response
    {
        $form = $this->createForm(SubcategoryType::class, $subcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('subcategory_index');
        }

        return $this->render('subcategory/edit.html.twig', [
            'subcategory' => $subcategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="subcategory_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Subcategory $subcategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subcategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($subcategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('subcategory_index');
    }
}
