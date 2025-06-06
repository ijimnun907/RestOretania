<?php

namespace App\Repository;

use App\Entity\Mesa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mesa>
 *
 * @method Mesa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mesa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mesa[]    findAll()
 * @method Mesa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MesaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mesa::class);
    }

    public function save()
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Mesa $mesa)
    {
        $this->getEntityManager()->remove($mesa);
    }

    public function add(Mesa $mesa)
    {
        $this->getEntityManager()->persist($mesa);
    }

    public function findMesaOrdenadaPorCapacidad() : array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.capacidad', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Mesa[] Returns an array of Mesa objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mesa
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
