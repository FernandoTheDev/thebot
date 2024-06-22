<?php

namespace Fernando\Middleware;

use PDO;
use Exception;
use Zanzara\Context;
use Fernando\Db\ContainerDb;
use Ramsey\Uuid\Uuid;

final class Middleware
{
    public function process(Context $ctx, callable $next): void
    {
        $this->register($ctx->getEffectiveUser()->getId());
        $next($ctx);
    }

    private function register(int $user): void
    {
        $microtime = microtime(true);
        $register = sprintf("%s às %s", date("d/m/Y"), date("H:i:s"));

        $pdo = ContainerDb::getContainer()['pdo'];
        $pdo->beginTransaction();
        $uuid = Uuid::uuid4()->toString();

        try {
            // Register User if not exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
            $stmt->bindParam(":user_id", $user, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt->fetch()) {
                $balance = 0;
                $stmt = $pdo->prepare("INSERT INTO users (uuid, user_id, balance, register_at, microtime) VALUES (:uuid, :user_id, :balance, :register_at, :microtime)");
                $stmt->bindParam(":uuid", $uuid, PDO::PARAM_STR);
                $stmt->bindParam(":user_id", $user, PDO::PARAM_INT);
                $stmt->bindParam(":balance", $balance, PDO::PARAM_INT);
                $stmt->bindParam(":register_at", $register, PDO::PARAM_STR);
                $stmt->bindParam(":microtime", $microtime, PDO::PARAM_INT);

                $stmt->execute();
            }
            
            $pdo->commit();
            return;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
