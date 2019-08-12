<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use App\Form\VideoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class VideoController extends AbstractController
{
    /**
     * @Route("/main", name="main")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $videos = $entityManager->getRepository(Video::class);

        $videos = $videos->findBy(
            ['published' =>     true]
        );        return $this->render('video/index.html.twig', [
            'videos' => $videos,
        ]);
    }


    /**
     * @Route("/video/{id}", name="v_details")
     *
     */
    public function detailsVideo(Video $video)
    {
        return $this->render('video/details.html.twig', [
            'video' => $video,
        ]);
    }

    /**
     * @Route("/upload", name="upload")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function upload(Request $request,  EntityManagerInterface $entityManager, Security $security)
    {

        $vod = new Video();
        $vod->setCreatedAt(new \DateTime());
        $vod->setUser($this->getUser());
        $form = $this->createForm(VideoType::class, $vod);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vod);
            $entityManager->flush();
            return $this->redirectToRoute('main');
        }
        return $this->render('video/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mesvideos/{id}", name="list_vod_usr")
     */
    public function listUserVideo(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Video::class);
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneById($id);
        $videos = $repository->findBy(
            ['user' =>     $user]
        );
        $categories = $entityManager->getRepository(Category::class)->findAll();
        $auteurs = $entityManager->getRepository(User::class)->findAll();
        return $this->render('video/video_by_usr.html.twig', [
            'videos' => $videos,
            'categories' => $categories,
            'auteurs' => $auteurs
        ]);
    }

    /**
     * @Route("/video/edit/{id}", name="video_edit")
     *
     */
    public function edit(Video $video, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($video);
            $entityManager->flush();
            return $this->redirectToRoute('list_vod_usr', ['id' => $video->getUser()->getId()] );
        }
        $repository = $entityManager->getRepository(User::class)->findAll();
        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'users' => $repository,
        ]);
    }


    /**
     * @Route("/video/remove/{id}", name="video_remove")
     * @param Video $video
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove(Video $video, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($video);
        $entityManager->flush();
        $this->addFlash('notice', 'Your changes were saved!');
        return $this->redirectToRoute('list_vod_usr', ['id' => $video->getUser()->getId()] );
    }


}