<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Advert;
use AppBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Advert controller.
 *
 * @Route("advert")
 */
class AdvertController extends Controller
{
    /**
     * Lists all advert entities.
     *
     * @Route("/", name="advert_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $doctrine = $this->getDoctrine();

        $adverts = $doctrine->getRepository('AppBundle:Advert')->showActiveAdverts();

        return $this->render('advert/index.html.twig', array(
            'adverts' => $adverts,
        ));
    }

    /**
     * Creates a new advert entity.
     *
     * @Route("/new", name="advert_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $advert = new Advert();
        $form = $this->createForm('AppBundle\Form\AdvertType', $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            if($form['picture']->getData()) {
                $file = $advert->getPicture();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                $file->move($this->getParameter('picture_directory'), $fileName);

                $advert->setPicture($fileName);
            }
            $advert->setUser($this->getUser());

            $em->persist($advert);
            $em->flush();

            return $this->redirectToRoute('advert_show', array('id' => $advert->getId()));
        }

        return $this->render('advert/new.html.twig', array(
            'advert' => $advert,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a advert entity.
     *
     * @Route("/{id}", name="advert_show", requirements={"id"="\d+"})
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request, Advert $advert)
    {
        $comment = new Comment();
        
        $commentForm = $this->createFormBuilder($comment)
                        ->add('commentContent', TextType::class)
                        ->add('save', SubmitType::class)
                        ->getForm();
        
        $commentForm->handleRequest($request);
        if($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment = $commentForm->getData();
            $comment->setUser($this->getUser());
            $comment->setAdvert($advert);
            
            //$advert->addComment($comment);
                    
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->persist($advert);
            $manager->flush();
            
            return $this->redirectToRoute('advert_show', ['id'=>$advert->getId()]);
        }
        
        $deleteForm = $this->createDeleteForm($advert);

        return $this->render('advert/show.html.twig', array(
            'advert' => $advert,
            'delete_form' => $deleteForm->createView(),
            'comment_form' => $commentForm->createView(),
        ));
    }
    
    /**
     * @Route("/{category}", name="by_category_show")
     * @Method("GET")
     */
    public function showByCategoriesAction($category)
    {
        $em = $this->getDoctrine()->getManager();

        $categoryEntity = $em->getRepository('AppBundle:Category')->findOneByTitle($category);
        $adverts = $categoryEntity->getAdverts();
        
        //$deleteForm = $this->createDeleteForm($advert);

        return $this->render('advert/index.html.twig', array(
            'adverts' => $adverts,
            //'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing advert entity.
     *
     * @Route("/{id}/edit", name="advert_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Advert $advert)
    {
        $deleteForm = $this->createDeleteForm($advert);
        $pathToFile = $advert->getPicture();
        if($advert->getPicture() !== null && file_exists($this->getParameter('picture_directory').'/'.$advert->getPicture())) {
            $advert->setPicture(new File($this->getParameter('picture_directory').'/'.$advert->getPicture()));
        }
        else {
            $advert->setPicture(null);
        }
                
        $editForm = $this->createForm('AppBundle\Form\AdvertType', $advert);
        $editForm->handleRequest($request);
        
        $user = $this->getUser();
        $adverts = $user->getAdverts();
        $isOK = $adverts->contains($advert);
        
        if($isOK === false) {
            return $this->redirectToRoute('advert_index');
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
           if($editForm['picture']->getData()) {   
                
                $file = $advert->getPicture();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                $file->move($this->getParameter('picture_directory'), $fileName);

                $advert->setPicture($fileName);
            }
            else {
                $advert->setPicture($pathToFile);
            }

            $advert->setUser($this->getUser());
            
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('advert_edit', array('id' => $advert->getId()));
        }

        return $this->render('advert/edit.html.twig', array(
            'advert' => $advert,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a advert entity.
     *
     * @Route("/{id}", name="advert_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Advert $advert)
    {
        $user = $this->getUser();
        $adverts = $user->getAdverts();
        $isOK = $adverts->contains($advert);
        
        if($isOK === false) {
            return $this->redirectToRoute('advert_index');
        }
        
        $form = $this->createDeleteForm($advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($advert);
            $em->flush();
        }

        return $this->redirectToRoute('advert_index');
    }

    /**
     * Creates a form to delete a advert entity.
     *
     * @param Advert $advert The advert entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Advert $advert)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('advert_delete', array('id' => $advert->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
