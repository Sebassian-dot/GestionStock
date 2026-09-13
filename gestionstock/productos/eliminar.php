<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/conexion.php";

$id = $_GET["id"] ?? null;

if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: listar.php");
    exit;
}

$sql = "DELETE FROM productos WHERE id = :id";

$stmt = $conexion->prepare($sql);

$stmt->execute([
    "id" => $id
]);

header("Location: listar.php");
exit;