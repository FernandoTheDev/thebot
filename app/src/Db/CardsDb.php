<?php
namespace Fernando\Db;

use PDO;
use Exception;
use PDOException;
use Fernando\Db\ContainerDb;

class CardsDb extends ContainerDb
{
    public static function getCardsValids(): array
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("
        SELECT * FROM cards 
        WHERE uuid NOT IN (SELECT card_uuid FROM purchasesPending)");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getLevelData(string $level = "N/A"): array
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("SELECT * FROM levels WHERE level = :level");
        $stmt->bindParam(":level", $level, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    public static function getCardLevelValids(string $level): array
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("
        SELECT * FROM cards 
        WHERE uuid NOT IN (SELECT card_uuid FROM purchasesPending) AND cards.level = :level");
        $stmt->bindParam(":level", $level, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getCardWhereLevelAndTypeIsValid(string $level, string $type): array
    {
        $pdo = self::getContainer()["pdo"];

        try {
            $pdo->beginTransaction();

            $stmtCount = $pdo->prepare("
            SELECT COUNT(*) AS total_cards
            FROM cards c
            LEFT JOIN purchasesPending pp ON c.uuid = pp.card_uuid
            WHERE pp.card_uuid IS NULL 
              AND c.level = :level AND c.type = :type");
            $stmtCount->bindParam(":level", $level, PDO::PARAM_STR);
            $stmtCount->bindParam(":type", $type, PDO::PARAM_STR);
            $stmtCount->execute();
            $totalCards = $stmtCount->fetchColumn();

            $maxReservations = max(1, floor($totalCards * 0.1));

            $stmt = $pdo->prepare("
            SELECT c.*
            FROM cards c
            LEFT JOIN purchasesPending pp ON c.uuid = pp.card_uuid
            WHERE pp.card_uuid IS NULL 
              AND c.level = :level AND c.type = :type
            ORDER BY RANDOM()   -- Ordena aleatoriamente os resultados
            LIMIT :maxReservations");

            $stmt->bindParam(":level", $level, PDO::PARAM_STR);
            $stmt->bindParam(":type", $type, PDO::PARAM_STR);
            $stmt->bindParam(":maxReservations", $maxReservations, PDO::PARAM_INT);
            $stmt->execute();

            $reservedCards = $stmt->fetchAll();
            $pdo->commit();

            return $reservedCards;
        } catch (PDOException $e) {
            $pdo->rollback();
            throw new Exception('Erro ao buscar cartões válidos: ' . $e->getMessage());
        }
    }

    public static function getLevelsValids(): array
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("
        SELECT DISTINCT levels.level, levels.value
        FROM levels
        INNER JOIN cards ON levels.level = cards.level
        WHERE cards.uuid NOT IN (SELECT uuid FROM purchasesPending)");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getLevelValids(string $level): array
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("
        SELECT DISTINCT levels.level, levels.value
        FROM levels
        INNER JOIN cards ON levels.level = cards.level
        WHERE cards.uuid NOT IN (SELECT uuid FROM purchasesPending) AND levels.level = :level");
        $stmt->bindParam(":level", $level, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function unsetCardByUuid(string $cardUuid): bool
    {
        $pdo = self::getContainer()["pdo"];

        $stmt = $pdo->prepare("DELETE FROM cards WHERE uuid = :uuid");
        $stmt->bindParam(":uuid", $cardUuid, PDO::PARAM_STR);

        return $stmt->execute();
    }
}