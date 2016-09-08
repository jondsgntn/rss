<?php
/**
 * Created by PhpStorm.
 * User: jonathanturner
 * Date: 9/7/16
 * Time: 10:35 PM
 */

namespace AppBundle\Command;

use AppBundle\Entity\Feed;
use AppBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class rssCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('rss:newArticle')
            ->setDescription('Get a new article');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $feeds = $em->getRepository('AppBundle:Feed')
            ->findAll();
        $reader = $this->getContainer()->get('debril.reader');
        foreach ($feeds as $feed){
            $url = $feed->getUrl();
            $rss = $reader->getFeedContent($url);
            $items = $rss->getItems();
            foreach ( $items as $item ) {
                $title = $item->getTitle();
                $url = $item->GetLink();
                $pubDate = $item->getUpdated();
                $article = new Article();
                $article->setName($title);
                $article->setUrl($url);
                $article->setPubDate($pubDate);
                $output->writeln("Adding article: ".$title."\n");
                $em->persist($article);
                $em->flush();
            }
        }
        $output->writeln("Finished adding articles");
    }
}