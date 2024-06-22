<?php

/**
 * Script para contar o número de linhas de código em arquivos PHP,
 * ignorando a pasta "vendor".
 */

function contarLinhas(string $dir): int {
    $contador = 0;

    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::SELF_FIRST);

    foreach ($it as $file) {
        if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getPathname(), DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) === false) {
            $contador += count(file($file->getPathname()));
        }
    }

    return $contador;
}

$diretorio = __DIR__ . "/../";
$totalLinhas = contarLinhas($diretorio);

echo "O total de linhas de código PHP no projeto (excluindo 'vendor') é: " . $totalLinhas . PHP_EOL;
