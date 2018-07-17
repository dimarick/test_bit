<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Service\RepositoryInterface;
use App\Service\RepositoryRegistry;

class PayoutRepository implements RepositoryInterface
{
    /**
     * @var RepositoryRegistry
     */
    private $registry;

    public function __construct(RepositoryRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param User $owner
     * @param float $sum
     * @throws \Doctrine\DBAL\DBALException
     */
    public function registerPayout(User $owner, float $sum): void
    {
        $this->registry->getConnection()->executeUpdate('INSERT INTO payouts(owner_id, sum) VALUES (:ownerId, :sum)', [
            ':ownerId' => $owner->getId(),
            ':sum' => $sum
        ]);
    }
}