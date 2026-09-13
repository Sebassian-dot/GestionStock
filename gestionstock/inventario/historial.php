<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/conexion.php";

/* =========================================================
   OBTENER MOVIMIENTOS
   ========================================================= */

$sql = "SELECT
            m.id,
            p.nombre AS producto,
            m.tipo,
            m.cantidad,
            m.fecha
        FROM movimientos_inventario m
        INNER JOIN productos p
            ON m.producto_id = p.id
        ORDER BY m.id DESC";

$stmt = $conexion->query($sql);

$movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   ESTADÍSTICAS
   ========================================================= */

$stmt = $conexion->query(
    "SELECT COUNT(*)
     FROM movimientos_inventario"
);

$totalMovimientos = $stmt->fetchColumn();


$stmt = $conexion->query(
    "SELECT COALESCE(SUM(cantidad), 0)
     FROM movimientos_inventario
     WHERE tipo = 'ENTRADA'"
);

$totalEntradas = $stmt->fetchColumn();


$stmt = $conexion->query(
    "SELECT COALESCE(SUM(cantidad), 0)
     FROM movimientos_inventario
     WHERE tipo = 'SALIDA'"
);

$totalSalidas = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Historial de movimientos - GestionStock</title>

    <link rel="stylesheet"
          href="../css/estilos.css">

</head>


<body>

<div style="
    display: flex;
    min-height: 100vh;
">


    <!-- =================================================
         BARRA LATERAL
         ================================================= -->

    <aside style="
        width: 240px;
        background: #1e3a8a;
        color: white;
        padding: 25px 15px;
        flex-shrink: 0;
    ">


        <!-- LOGO -->

        <div style="
            text-align: center;
            margin-bottom: 35px;
        ">

            <div style="
                font-size: 28px;
                font-weight: bold;
            ">
                📦 GestionStock
            </div>

            <small style="
                color: #bfdbfe;
            ">
                Sistema de inventario
            </small>

        </div>


        <!-- MENÚ -->

        <nav>

            <a href="../dashboard.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    color: white;
               ">
                🏠 Dashboard
            </a>


            <a href="../productos/listar.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    color: white;
               ">
                📦 Productos
            </a>


            <a href="entrada.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    color: white;
               ">
                📥 Entrada de inventario
            </a>


            <a href="salida.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    color: white;
               ">
                📤 Salida de inventario
            </a>


            <a href="historial.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    background: rgba(255,255,255,0.15);
                    color: white;
               ">
                📋 Historial
            </a>

        </nav>


        <!-- USUARIO -->

        <div style="
            position: absolute;
            bottom: 25px;
            width: 205px;
        ">

            <div style="
                border-top: 1px solid rgba(255,255,255,0.2);
                padding-top: 15px;
                margin-bottom: 12px;
            ">

                <small style="
                    color: #bfdbfe;
                ">
                    Usuario
                </small>


                <div style="
                    font-weight: bold;
                ">
                    <?= htmlspecialchars(
                        $_SESSION["usuario_nombre"]
                    ) ?>
                </div>

            </div>


            <a href="../logout.php"
               style="
                    display: block;
                    padding: 10px;
                    border-radius: 8px;
                    background: rgba(255,255,255,0.1);
                    color: white;
                    text-align: center;
               ">
                🚪 Cerrar sesión
            </a>

        </div>

    </aside>


    <!-- =================================================
         CONTENIDO PRINCIPAL
         ================================================= -->

    <main style="
        flex: 1;
        padding: 35px;
        overflow-x: auto;
    ">


        <!-- ENCABEZADO -->

        <div class="encabezado-pagina">

            <div>

                <h1>
                    Historial de movimientos
                </h1>

                <p class="subtitulo">
                    Consulta las entradas y salidas registradas
                    en el inventario
                </p>

            </div>

        </div>


        <!-- =================================================
             TARJETAS DE ESTADÍSTICAS
             ================================================= -->

        <div style="
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        ">


            <!-- TOTAL -->

            <div class="panel"
                 style="margin-bottom: 0;">

                <div style="
                    font-size: 28px;
                    margin-bottom: 10px;
                ">
                    📋
                </div>

                <p class="subtitulo">
                    Movimientos registrados
                </p>

                <h2>
                    <?= $totalMovimientos ?>
                </h2>

            </div>


            <!-- ENTRADAS -->

            <div class="panel"
                 style="
                    margin-bottom: 0;
                    border-left: 5px solid #16a34a;
                 ">

                <div style="
                    font-size: 28px;
                    margin-bottom: 10px;
                ">
                    📥
                </div>

                <p class="subtitulo">
                    Unidades ingresadas
                </p>

                <h2>
                    <?= $totalEntradas ?>
                </h2>

            </div>


            <!-- SALIDAS -->

            <div class="panel"
                 style="
                    margin-bottom: 0;
                    border-left: 5px solid #dc2626;
                 ">

                <div style="
                    font-size: 28px;
                    margin-bottom: 10px;
                ">
                    📤
                </div>

                <p class="subtitulo">
                    Unidades retiradas
                </p>

                <h2>
                    <?= $totalSalidas ?>
                </h2>

            </div>

        </div>


        <!-- =================================================
             TABLA DE MOVIMIENTOS
             ================================================= -->

        <div class="panel">

            <div style="
                margin-bottom: 20px;
            ">

                <h2>
                    Registro de movimientos
                </h2>

                <p class="subtitulo">
                    Historial de operaciones realizadas
                </p>

            </div>


            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Producto
                            </th>

                            <th>
                                Tipo
                            </th>

                            <th>
                                Cantidad
                            </th>

                            <th>
                                Fecha
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if (count($movimientos) > 0): ?>

                        <?php foreach (
                            $movimientos as $movimiento
                        ): ?>

                            <tr>

                                <td>
                                    #<?= $movimiento["id"] ?>
                                </td>


                                <td>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $movimiento["producto"]
                                        ) ?>
                                    </strong>

                                </td>


                                <td>

                                    <?php if (
                                        $movimiento["tipo"]
                                        === "ENTRADA"
                                    ): ?>

                                        <span class="movimiento entrada">
                                            📥 Entrada
                                        </span>

                                    <?php else: ?>

                                        <span class="movimiento salida">
                                            📤 Salida
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <strong>
                                        <?= $movimiento["cantidad"] ?>
                                    </strong>

                                    unidades

                                </td>


                                <td>

                                    <?= date(
                                        "d/m/Y H:i",
                                        strtotime(
                                            $movimiento["fecha"]
                                        )
                                    ) ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="5"
                                style="
                                    text-align: center;
                                    padding: 40px;
                                    color: #6b7280;
                                ">

                                📋 No hay movimientos registrados.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <a href="../dashboard.php">
            ← Volver al Dashboard
        </a>


    </main>

</div>

</body>

</html>