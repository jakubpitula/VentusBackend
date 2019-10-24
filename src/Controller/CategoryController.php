<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\CategoryService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Subcategory;
use App\Services\SubcategoryService;
use Symfony\Component\Console\Output\ConsoleOutput;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/api/category")
 */
class CategoryController extends AbstractController
{
    private $categoryService;
    private $subcategoryService;
    private $em;

    public function __construct(CategoryService $categoryService, SubcategoryService $subcategoryService, EntityManagerInterface $em)
    {
        $this->categoryService = $categoryService;
        $this->subcategoryService = $subcategoryService;
        $this->em = $em;
    }
    
    /**
     * @Route("/", name="category_index", methods={"GET"})
     */
    public function getAll(): Response
    {
        $status = JsonResponse::HTTP_OK;

        $data = [];

        try {
            $data = $this->categoryService->findAll();

        } catch (\Exception $exception) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $output = new ConsoleOutput();
            $output->writeln($exception->getMessage());
        }


        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function getById(Category $category): Response
    {
        $status = JsonResponse::HTTP_OK;

        $data = [];

        try {
            $data = $this->categoryService->findAllById($category->getId());

        } catch (\Exception $exception) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $output = new ConsoleOutput();
            $output->writeln($exception->getMessage());
        }


        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/{id}/subcategories")
     */
    public function getSubcategories(Category $category): Response
    {
        $status = JsonResponse::HTTP_OK;

        $data = [];

        try {
            $data = $this->subcategoryService->findAllByCategory($category);

        } catch (\Exception $exception) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $output = new ConsoleOutput();
            $output->writeln($exception->getMessage());
        }


        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/new", name="category_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        $data = $request->request->all();

        $category = new Category();

        if(!isset($data['name'])) return new JsonResponse(['error' => 'Name not set'], 400);

        $category->setName($data['name']);

        $this->em->persist($category);
        $this->em->flush();

        return new JsonResponse([], 201);
    }


    /**
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_index');
    }
}
