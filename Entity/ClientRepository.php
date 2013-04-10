<?php

/*
 * This file is part of the SGLFLTSBundle package.
 *
 * (c) Simon Guillem-Lessard <s.g.lessard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SGL\FLTSBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ClientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClientRepository extends EntityRepository
{
    /*
     * Retrieve Clients (custom find)
     *
     * @param bool $queryBuilder (return query builder only)
     * @return Doctrine Collection or Query Builder
     */
    public function retrieve($queryBuilder = false)
    {
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.name', 'ASC')
        ;

        return $this->dispatch($query, $queryBuilder);
    }

    /*
     * Retrieve Clients With any projects and any parts (event none)
     *
     * @param bool $queryBuilder (return query builder only)
     * @return Doctrine Collection or Query Builder
     */
    public function retrieveWithPartsProjects($queryBuilder = false)
    {
        $query = $this->createQueryBuilder('c')
            ->select('c,p,pp')
            ->innerJoin('c.projects','pp')
            ->leftJoin('pp.parts','p')
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('pp.identification', 'ASC')
            ->addOrderBy('pp.name', 'ASC')
        ;

        return $this->dispatch($query, $queryBuilder);
    }

    /*
     * Retrieve Clients with opened project(s)
     *
     * @param bool $queryBuilder (return query builder only)
     * @return Doctrine Collection or Query Builder
     */
    public function retrieveWithOpenedProjects($queryBuilder = false) {
        $query = $this->retrieve(true);

        $query->select('c, cp, cpp')
              ->innerjoin('c.projects', 'cp')
              ->innerjoin('cp.parts', 'cpp')
              ->where('cpp.closed_at is null');

        return $this->dispatch($query, $queryBuilder);
    }

    /*
     * dispatch
     *
     * @param Query Builder $query
     * @param boolean  $queryBuilder (return query builder only)
     *
     * @return Doctrine Collection or Query Builder
     */
    private function dispatch($query, $queryBuilder) {
        if ($queryBuilder) {
            return $query;
        } else {
            return $query->getQuery()->getResult();
        }
    }
}