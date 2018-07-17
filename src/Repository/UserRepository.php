<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Service\RepositoryInterface;
use App\Service\RepositoryRegistry;

class UserRepository implements RepositoryInterface
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
     * @param string $email
     * @return User|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function loadUserByEmail(string $email): ?User
    {
        $row = $this->registry->getConnection()
            ->executeQuery('SELECT * FROM users WHERE email = :email', [':email' => $email])->fetch();

        return $this->hydrateRow($row);
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
            $affected = $connection->executeUpdate('UPDATE users SET balance = balance - :sum WHERE id = :ownerId AND balance >= :sum', [
                ':ownerId' => $user->getId(),
                ':sum' => $sum
            ]);

            if ($affected === 0) {
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

        return true;
    }

    /**
     * @param $row
     * @return User|null
     */
    private function hydrateRow($row): ?User
    {
        if ($row === false) {
            return null;
        }

        $user = new User();
        $user->setId((int)$row['id']);
        $user->setEmail((string)$row['email']);
        $user->setPassword((string)$row['password']);
        $user->setBalance((float)$row['balance']);

        return $user;
    }
}