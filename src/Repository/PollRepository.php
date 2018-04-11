<?php

namespace App\Repository;

use App\Entity\Poll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Poll|null find($id, $lockMode = null, $lockVersion = null)
 * @method Poll|null findOneBy(array $criteria, array $orderBy = null)
 * @method Poll[]    findAll()
 * @method Poll[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PollRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Poll::class);
    }

    public function findOneByIdJoinedToAnswer($answerUid)
    {
        return $this->createQueryBuilder('p')
            // p.answer refers to the "answer" property on poll
            ->innerJoin('p.answers', 'a')
            // selects all the answer data to avoid the query
            ->addSelect('a')
            ->andWhere('a.uid = :uid')
            ->setParameter('uid', $answerUid)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
