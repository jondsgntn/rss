<?php
/**
 * Created by PhpStorm.
 * User: jonathanturner
 * Date: 9/8/16
 * Time: 12:19 AM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Article;
use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    /**
     * @return Article[]
     */
    public function findAllOrderedByDate()
    {
        return $this->createQueryBuilder('article')
            ->orderBy('article.pubDate', 'DESC')
            ->getQuery()
            ->execute();
    }
}