<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\BlogRepository;
use App\Entity\Blog;
use App\Form\BlogFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class MainController extends AbstractController
{
    private $blogRepository;
    private $em;
    private $slugger;
    public function __construct(BlogRepository $blogRepository, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $this->blogRepository = $blogRepository;
        $this->em = $em;
        $this->slugger = $slugger;
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $blogs = $this->blogRepository->findAll();
        //dd($blogs);
        return $this->render('main/index.html.twig', [
            'blogs' => $blogs,
        ]);
    }
    #[Route('/create', name: 'app_create')]
    public function create(Request $request): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogFormType::class, $blog);
        //$form = $form->createView();
        //dd($form);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image cannot be saved.');
                }
                $blog->setImage($newFilename);
            }


            $newBlog = $form->getData();
            $this->em->persist($newBlog);
            $this->em->flush();
            $this->addFlash('success', 'Blog was created!');

            return $this->redirectToRoute('app_main');
        }



        return $this->render('main/details.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
