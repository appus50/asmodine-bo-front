<?php

namespace Asmodine\FrontBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CategoryRepository.
 */
class CategoryRepository extends EntityRepository
{
    /**
     * Update fields.
     */
    public function optimize()
    {
        $sql = 'UPDATE front_orm_fromback_category AS c INNER JOIN front_orm_fromback_category AS p ON c.back_parent_id = p.back_id SET c.gender = p.gender WHERE c.gender IS NULL AND c.depth = :depth';

        $connection = $this->getEntityManager()->getConnection();

        $statement = $connection->prepare($sql);
        $statement->bindValue('depth', 1);
        $statement->execute();

        $statement = $connection->prepare($sql);
        $statement->bindValue('depth', 2);
        $statement->execute();
    }
}
