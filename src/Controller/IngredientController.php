<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use Doctrine\ORM\EntityManager;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /**
     *this controller display all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients

        ]);
    }

    /**
     *this controller add ingredients
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[route('/ingredient/nouveau', name: 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {

        $ingredient = new Ingredient();


        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            // semblable a un git commit ""
            $manager->persist($ingredient);
            //senblable a un git push
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingredient a bien été ajouter!'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * this controller to edit ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(IngredientRepository $repo, int $id, EntityManagerInterface $manager, Request $request): Response
    {
        $ingredient = $repo->findOneBy(["id" => $id]);
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            // semblable a un git commit ""
            $manager->persist($ingredient);
            //senblable a un git push
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingredient a bien été modifier!'
            );

            return $this->redirectToRoute('ingredient.index');
        }


        return $this->render('pages/ingredient/edit.html.twig', [

            'form' => $form->createView()
        ]);
    }

   
    /**
     * this controller delete ingredients
     *
     * @param IngredientRepository $repo
     * @param integer $id
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods: ['GET'])]
    public function delete(IngredientRepository $repo, int $id, EntityManagerInterface $manager ): Response
    {
        $ingredient = $repo->findOneBy(["id" => $id]);
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingredient a bien été supprimer!'
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
