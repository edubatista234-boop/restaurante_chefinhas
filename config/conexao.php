<?php
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "restaurante_chefinhas";

try {
    
    $conn = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuario, $senha);
    
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    
    die("Falha na conexão com o banco de dados via PDO: " . $e->getMessage());
}
?>