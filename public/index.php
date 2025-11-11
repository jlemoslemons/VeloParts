<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Carrega .env
$root = dirname(__DIR__); // Corrigido para o diretório raiz
if (file_exists($root . '/.env')) {
    $dotenv = Dotenv::createImmutable($root);
    $dotenv->load();
}

// Inicia o roteador
require_once("routes.php");