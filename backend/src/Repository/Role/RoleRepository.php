<?php

namespace App\Repository\Role;

use App\Entity\Role\Role;
use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<Role>
 *
 *
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends EntityRepository
{

    public function findByIds(array $ids)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.id IN(:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
        ;
    }

}
