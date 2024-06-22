<?php
namespace Fernando\Db;

use Zanzara\Context;
use Fernando\Db\ContainerDb;
use Ramsey\Uuid\Uuid;
use Zanzara\Telegram\Type\User;

final class UserDb extends ContainerDb
{
    private User $user;

    public function __construct(Context $ctx)
    {
        $this->user = $ctx->getEffectiveUser();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getData(): array|bool
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->bindValue(":user_id", $this->user->getId());
        $stmt->execute();

        return $stmt->fetch();
    }

    public function get(string $column): mixed
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("SELECT {$column} FROM users WHERE user_id = :user_id");
        $stmt->bindValue(":user_id", $this->user->getId());
        $stmt->execute();

        return $stmt->fetch();
    }

    public function set(string $column, mixed $value): mixed
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("UPDATE users SET {$column} = :value WHERE user_id = :user_id");
        $stmt->bindValue(":value", $value);
        $stmt->bindValue(":user_id", $this->user->getId());
        $stmt->execute();

        return $stmt->execute();
    }

    public function unsetBalance(int|float $amount): bool
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("UPDATE users SET balance = balance - :amount WHERE user_id = :user_id");
        $stmt->bindValue(":user_id", $this->user->getId());
        $stmt->bindValue(":amount", $amount);

        return $stmt->execute();
    }

    public function addBalance(int|float $amount): bool
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("UPDATE users SET balance = balance + :amount WHERE user_id = :user_id");
        $stmt->bindValue(":user_id", $this->user->getId());
        $stmt->bindValue(":amount", $amount);

        return $stmt->execute();
    }

    public function getHistoryShopping(): array|bool
    {
        $uuid = $this->getData()["uuid"];
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("SELECT * FROM historyShopping WHERE user_uuid = :uuid");
        $stmt->bindValue(":uuid", $uuid);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function addShopping(string $type, string $list, int|float $value): bool
    {
        $uuid = Uuid::uuid4()->toString();
        $user_uuid = $this->getData()["uuid"];
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("INSERT INTO historyShopping (uuid, list, user_uuid, value, type) VALUES (:uuid, :list, :user_uuid, :value, :type)");
        $stmt->bindValue(":uuid", $uuid);
        $stmt->bindValue(":list", $list);
        $stmt->bindValue(":user_uuid", $user_uuid);
        $stmt->bindValue(":value", $value);
        $stmt->bindValue(":type", $type);

        return $stmt->execute();
    }
}