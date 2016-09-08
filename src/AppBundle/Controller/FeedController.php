<?php
/**
 * Created by PhpStorm.
 * User: jonathanturner
 * Date: 9/7/16
 * Time: 9:21 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Feed;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FeedController extends Controller
{
    /**
     * @Route("/feed/new")
     */
    public function newAction(Request $request)
    {
        // create a task and give it some dummy data for this example
        $feed = new Feed();
        $feed->setName('Test Feed');
        $feed->setUrl('http://www.google.com');

        $form = $this->createFormBuilder($feed)
            ->add('name', TextType::class)
            ->add('url', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Feed'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $_feed = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($_feed);
            $em->flush();

            return $this->redirectToRoute('feed_list');
        }

        return $this->render('feed/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/feed", name="feed_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $feeds = $em->getRepository('AppBundle:Feed')
            ->findAll();

        return $this->render('feed/list.html.twig', [
            'feeds' => $feeds,
        ]);
    }

    /**
     * @Route("/feed/show/{feedName}", name="feed_show")
     */
    public function showAction($feedName)
    {
        $em = $this->getDoctrine()->getManager();
        $feed = $em->getRepository('AppBundle:Feed')
            ->findOneBy(['name' => $feedName]);

        if (!$feed) {
            throw $this->createNotFoundException('No feed found');
        }
        $this->get('logger')
            ->info('Showing feed: '.$feedName);

        return $this->render('feed/show.html.twig', [
            'feed' => $feed
        ]);

    }
}