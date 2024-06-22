<?php
namespace Fernando\Db;

use Fernando\Db\ContainerDb;
use Ramsey\Uuid\Uuid;

final class GiftDb extends ContainerDb
{
    public function createGift(string $uuid, int|float $value): array
    {
        $uuidNew = Uuid::uuid4()->toString();
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("INSERT INTO gifts (uuid, value, createByUuid) VALUES (:uuid, :value, :createByUuid)");
        $stmt->bindValue(":uuid", $uuidNew);
        $stmt->bindValue(":value", $value);
        $stmt->bindValue(":createByUuid", $uuid);

        return [$stmt->execute(), $uuidNew];
    }

    public function getGift(string $uuid): bool|array
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("SELECT * FROM gifts WHERE uuid = :uuid");
        $stmt->bindValue(":uuid", $uuid);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function consumeGift(string $uuid, string $userUuid): bool
    {
        $useAt = sprintf("%s às %s", date("d/m/Y"), date("H:i:s"));
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("UPDATE gifts SET status = false, redeemedByUuid = :redeemedByUuid, redeemedAt = :redeemedAt WHERE uuid = :uuid");
        $stmt->bindValue(":uuid", $uuid);
        $stmt->bindValue(":redeemedByUuid", $userUuid);
        $stmt->bindValue(":redeemedAt", $useAt);

        return $stmt->execute();
    }
}