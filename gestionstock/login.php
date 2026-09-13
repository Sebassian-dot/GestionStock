<?php

session_start();

require_once "config/conexion.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($correo === "" || $password === "") {
        $error = "Todos los campos son obligatorios.";
    } else {

        $sql = "SELECT * FROM usuarios WHERE correo = :correo LIMIT 1";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            "correo" => $correo
        ]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario["password"])) {

            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nombre"] = $usuario["nombre"];
            $_SESSION["usuario_rol"] = $usuario["rol"];

            header("Location: dashboard.php");
            exit;

        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GestionStock - Iniciar sesión</title>

    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<div class="contenedor-login">

    <div class="login-card">

        <h1>GestionStock</h1>

        <p>Sistema de gestión de inventario</p>

        <?php if ($error): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="formulario-grupo">

                <label for="correo">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    id="correo"
                    name="correo"
                    placeholder="correo@ejemplo.com"
                    required
                >

            </div>

            <div class="formulario-grupo">

                <label for="password">
                    Contraseña
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Ingrese su contraseña"
                    required
                >

            </div>

            <button type="submit" class="btn">
                Iniciar sesión
            </button>

        </form>

    </div>

</div>

</body>

</html>