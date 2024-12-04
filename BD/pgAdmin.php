<?php
// Configuração de banco de dados
$host = 'localhost';
$db_name = 'php';  // Nome do banco de dados
$db_user = 'postgres'; // Usuário do banco de dados
$db_pass = 'admin'; // Senha do banco de dados

try {
    // Conecta ao banco de dados PostgreSQL
    $pdo = new PDO("pgsql:host=$host;port=5433;dbname=$db_name", $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    //  echo "Conectado ao banco de dados!!!";

} catch (PDOException $e) {
    echo "Falha ao conectar ao banco de dados. <br/>";
    die($e->getMessage());
}
