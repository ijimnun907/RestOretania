<?php

namespace App\Repository;

use App\Entity\Mesa;
use App\Entity\Reserva;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reserva>
 *
 * @method Reserva|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reserva|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reserva[]    findAll()
 * @method Reserva[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reserva::class);
    }

    public function save()
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Reserva $reserva)
    {
        $this->getEntityManager()->remove($reserva);
    }

    public function add(Reserva $reserva)
    {
        $this->getEntityManager()->persist($reserva);
    }

    public function findReservaOrdenadaPorFecha() : array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.fechaHora', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findReservasDeUsuarioOrdenadas(Usuario $usuario)
    {
        return $this->createQueryBuilder('r')
            ->where('r.usuario = :usuario')
            ->setParameter('usuario', $usuario)
            ->orderBy('r.fechaHora', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findReservasDeHoy(): array
    {
        $inicio = new \DateTimeImmutable('today midnight');
        $fin = $inicio->modify('+1 day');

        return $this->createQueryBuilder('r')
            ->andWhere('r.fechaHora >= :inicio')
            ->andWhere('r.fechaHora < :fin')
            ->setParameter('inicio', $inicio)
            ->setParameter('fin', $fin)
            ->orderBy('r.fechaHora', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findReservasPorFechaYMesa(\DateTimeInterface $fecha, Mesa $mesa): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.mesa = :mesa')
            ->andWhere('r.fechaHora >= :start')
            ->andWhere('r.fechaHora < :end')
            ->setParameter('mesa', $mesa)
            ->setParameter('start', $fecha->format('Y-m-d 00:00:00'))
            ->setParameter('end', $fecha->format('Y-m-d 23:59:59'))
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Reserva[] Returns an array of Reserva objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reserva
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
