<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\PayoutRepository;
use App\Repository\UserRepository;

class PayoutManager
{
    /**
     * @var RepositoryRegistry
     */
    private $registry;

    /**
     * @param RepositoryRegistry $registry
     */
    public function __construct(RepositoryRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param User $user
     * @param float $sum
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Throwable
     */
    public function processPayout(User $user, float $sum): bool
    {
        $connection = $this->registry->getConnection();

        $connection->beginTransaction();
        try {
            /** @var UserRepository $userRepository */
            $userRepository = $this->registry->getRepository(UserRepository::class);
            $processed = $userRepository->processPayout($user, $sum);

            if ($processed === 0) {
                $connection->rollBack();

                return false;
            }

            /** @var PayoutRepository $payoutsRepository */
            $payoutsRepository = $this->registry->getRepository(PayoutRepository::class);
            $payoutsRepository->registerPayout($user, $sum);

            $connection->commit();
        } catch (\Throwable $e) {
            $connection->rollBack();

            throw $e;
        }

        $user->setBalance($user->getBalance() - $sum);

        return true;
    }
}