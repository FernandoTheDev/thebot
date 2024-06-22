<?php

use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid;
use Fernando\Db\ContainerDb;

require_once (__DIR__ . "/../vendor/autoload.php");
require_once (__DIR__ . "/lib/Card.php");

function getDbBin(): PDO
{
    $pdoSettings = [
        'dsn' => 'sqlite:' . __DIR__ . '/../database/bins.db',
        'username' => null,
        'password' => null,
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    ];
    return new PDO(
        $pdoSettings['dsn'],
        $pdoSettings['username'],
        $pdoSettings['password'],
        $pdoSettings['options']
    );
}

if (count($argv) < 2) {
    die("Parâmetro esperado!\n");
}

$dotenv = Dotenv::createImmutable(__DIR__ . "/../environment/");
$dotenv->load();

$data = Card::SetCard(file_get_contents($argv[1]));
$pdo = ContainerDb::getContainer()["pdo"];

if ($data["cc"]["valid"] == null) {
    die($data["data"]);
}

$valids = $data["cc"]["valid"];
$valueDefault = $_ENV["DEFAULT_VALUE"];
$repetidos = 0;
$add = 0;
$err = 0;
$lvls = 0;
$start = microtime(true);

for ($i = 0; $i < count($valids); $i++) {
    $list = $valids[$i]["cc"];
    $uuid = Uuid::uuid4()->toString();

    $card = $list[0];
    $bin = substr($card, 0, 6);
    $month = $list[1];
    $year = $list[2];
    $cvv = $list[3];

    // Fallback
    $flag = "N/A";
    $country = "N/A";
    $bank = "N/A";
    $level = "INDEFINIDO";
    $type = "N/A";
    // End Fallback

    $stmt = $pdo->prepare("SELECT * FROM cards WHERE card = :card");
    $stmt->bindParam(":card", $card, PDO::PARAM_INT);
    $stmt->execute();
    $fetch = $stmt->fetch();

    if ($fetch != "") {
        $repetidos++;
        continue;
    }

    $stmt = getDbBin()->prepare("SELECT * FROM cartoes WHERE bin = :bin");
    $stmt->bindParam(":bin", $bin, PDO::PARAM_INT);
    $stmt->execute();
    $fetch = $stmt->fetch();

    if ($fetch != "") {
        $flag = $fetch["bandeira"];
        $bank = $fetch["banco"];
        $level = $fetch["nivel"];
        $country = $fetch["pais"];
        $type = $fetch["tipo"];
    }

    $stmt = $pdo->prepare("SELECT * FROM levels WHERE level = :level");
    $stmt->bindParam(":level", $level, PDO::PARAM_STR);
    $stmt->execute();
    $fetchLevel = $stmt->fetch();

    if ($fetchLevel == "") {
        $stmt = $pdo->prepare("INSERT INTO levels (level, value) VALUES (:level, :value)");
        $stmt->bindParam(":level", $level, PDO::PARAM_STR);
        $stmt->bindParam(":value", $valueDefault, PDO::PARAM_INT);
        $stmt->execute();
        $lvls++;
    }

    $stmt = $pdo->prepare("INSERT INTO cards 
    (uuid, bin, card, month, year, cvv, bank, level, country, flag, type)
     VALUES 
     (:uuid, :bin, :card, :month, :year, :cvv, :bank, :level, :country, :flag, :type)");

    $stmt->bindParam(":uuid", $uuid, PDO::PARAM_STR);
    $stmt->bindParam(":bin", $bin, PDO::PARAM_STR);
    $stmt->bindParam(":card", $card, PDO::PARAM_STR);
    $stmt->bindParam(":month", $month, PDO::PARAM_INT);
    $stmt->bindParam(":year", $year, PDO::PARAM_INT);
    $stmt->bindParam(":cvv", $cvv, PDO::PARAM_STR);
    $stmt->bindParam(":bank", $bank, PDO::PARAM_STR);
    $stmt->bindParam(":level", $level, PDO::PARAM_STR);
    $stmt->bindParam(":country", $country, PDO::PARAM_STR);
    $stmt->bindParam(":flag", $flag, PDO::PARAM_STR);
    $stmt->bindParam(":type", $type, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        $err++;
        continue;
    }
    $add++;
}

print ("--------- RESULTADO ---------\n");
print ("| Carregados: {$data['data']['all']}\n");
print ("| Válidos: {$data['data']['valid']}\n");
print ("| Inválidos: {$data['data']['invalid']}\n\n");
print ("| Levels novos: {$lvls}\n");
print ("| Salvos: {$add}\n");
print ("| Não foram salvos: {$err}\n");
print ("| Repetidos: {$repetidos}\n\n");
print (sprintf("| Finalizado em %.4fs\n", microtime(true) - $start));
print ("-----------------------------\n");
