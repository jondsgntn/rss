<?php
/**
 * Created by PhpStorm.
 * User: jonathanturner
 * Date: 9/7/16
 * Time: 9:21 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleController extends Controller
{
    /**
     * @Route("/article/new")
     */
    public function newAction($name, $url)
    {
        // create a task and give it some dummy data for this example
        $article = new Article();
        $article->setName($name);
        $article->setUrl($url);

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

    }

    /**
     * @Route("/article", name="article_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('AppBundle:Article')
            ->findAllOrderedByDate();

        return $this->render('article/list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/article/show/{articleName}", name="article_show")
     */
    public function showAction($articleName)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')
            ->findOneBy(['name' => $articleName]);

        if (!$article) {
            throw $this->createNotFoundException('No article found');
        }
        $this->get('logger')
            ->info('Showing feed: '.$articleName);

        return $this->render('article/show.html.twig', [
            'article' => $article
        ]);

    }

    /**
     * @route("/article/edit/{id}", name="article_edit")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')->find($id);
        if (!$article) {
            throw $this->createNotFoundException(
                'No articles found with id ' . $id
            );
        }

        $form = $this->createFormBuilder($article)
            ->add('name', TextType::Class)
            ->add('url', TextType::Class)
            ->add('pubDate', DateType::Class)
            ->add('save', SubmitType::class, array('label' => 'Edit Article'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('article_list');
        }

        $build['form'] = $form->createView();

        return $this->render('article/edit.html.twig', $build);
    }

}