<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * This controller display all recipes
     *
     * @param PaginatorInterface $paginator
     * @param RecipeRepository $repository
     * @param Request $request
     * @return Response
     */
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, RecipeRepository $repository, Request $request): Response
    {

        $recipes = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * this controller to create new recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a bien été créer!'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * this controller edit a recipe
     *
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @param Request $request
     * 
     * @return Response
     * 
     */
    #[Route('/recette/edition/{id}', 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(RecipeRepository $repo, int $id, EntityManagerInterface $manager, Request $request): Response
    {
        $recipe = $repo->findOneBy(["id" => $id]);
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            // semblable a un git commit ""
            $manager->persist($recipe);
            //senblable a un git push
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a bien été modifier!'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [

            'form' => $form->createView()
        ]);
    }


    
    /**
     * this controller delete recipe
     *
     * @param RecipeRepository $repo
     * @param integer $id
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recipe/suppression/{id}', 'recipe.delete', methods: ['GET'])]
    public function delete(RecipeRepository $repo, int $id, EntityManagerInterface $manager): Response
    {
        $recipe = $repo->findOneBy(["id" => $id]);
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a bien été supprimer!'
        );

        return $this->redirectToRoute('recipe.index');
    }
}
