<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

require_once "config/conexion.php";

/* =========================================================
   DATOS DEL DASHBOARD
   ========================================================= */

$stmt = $conexion->query("SELECT COUNT(*) FROM productos");
$totalProductos = $stmt->fetchColumn();

$stmt = $conexion->query("SELECT COUNT(*) FROM categorias");
$totalCategorias = $stmt->fetchColumn();

$stmt = $conexion->query("SELECT COALESCE(SUM(stock), 0) FROM productos");
$totalStock = $stmt->fetchColumn();

$stmt = $conexion->query("SELECT COUNT(*) FROM movimientos_inventario");
$totalMovimientos = $stmt->fetchColumn();

/* Productos registrados */

$sqlProductos = "SELECT
                    p.id,
                    p.nombre,
                    p.precio,
                    p.stock,
                    c.nombre AS categoria
                FROM productos p
                INNER JOIN categorias c
                    ON p.categoria_id = c.id
                ORDER BY p.id DESC
                LIMIT 5";

$stmtProductos = $conexion->query($sqlProductos);
$productos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>GestionStock - Dashboard</title>

    <link rel="stylesheet"
          href="css/estilos.css">

</head>

<body>

<!-- =====================================================
     ESTRUCTURA PRINCIPAL
     ===================================================== -->

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

            <a href="dashboard.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    background: rgba(255,255,255,0.15);
                    color: white;
               ">
                🏠 Dashboard
            </a>

            <a href="productos/listar.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    color: white;
               ">
                📦 Productos
            </a>

            <a href="inventario/entrada.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    color: white;
               ">
                📥 Entrada de inventario
            </a>

            <a href="inventario/salida.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    color: white;
               ">
                📤 Salida de inventario
            </a>

            <a href="inventario/historial.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    color: white;
               ">
                 📋 Movimientos
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
                    <?= htmlspecialchars($_SESSION["usuario_nombre"]) ?>
                </div>

            </div>

            <a href="logout.php"
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
                    Dashboard
                </h1>

                <p class="subtitulo">
                    Resumen general del inventario
                </p>

            </div>

            <div>

                <strong>
                    👋 Hola,
                    <?= htmlspecialchars($_SESSION["usuario_nombre"]) ?>
                </strong>

            </div>

        </div>


        <!-- =================================================
             TARJETAS
             ================================================= -->

        <div class="tarjetas">


            <!-- PRODUCTOS -->

            <div class="tarjeta">

                <div class="tarjeta-icono">
                    📦
                </div>

                <h3>
                    Productos registrados
                </h3>

                <div class="numero">
                    <?= $totalProductos ?>
                </div>

            </div>


            <!-- CATEGORÍAS -->

            <div class="tarjeta">

                <div class="tarjeta-icono">
                    🗂️
                </div>

                <h3>
                    Categorías
                </h3>

                <div class="numero">
                    <?= $totalCategorias ?>
                </div>

            </div>


            <!-- STOCK -->

            <div class="tarjeta">

                <div class="tarjeta-icono">
                    📊
                </div>

                <h3>
                    Unidades en inventario
                </h3>

                <div class="numero">
                    <?= $totalStock ?>
                </div>

            </div>


            <!-- MOVIMIENTOS -->

            <div class="tarjeta">

                <div class="tarjeta-icono">
                    🔄
                </div>

                <h3>
                    Movimientos
                </h3>

                <div class="numero">
                    <?= $totalMovimientos ?>
                </div>

            </div>

        </div>


        <!-- =================================================
             ACCIONES RÁPIDAS
             ================================================= -->

        <div class="panel">

            <h2>
                Acciones rápidas
            </h2>

            <div style="
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            ">

                <a href="productos/crear.php"
                   class="btn">
                    ➕ Nuevo producto
                </a>

                <a href="inventario/entrada.php"
                   class="btn btn-exito">
                    📥 Registrar entrada
                </a>

                <a href="inventario/salida.php"
                   class="btn btn-peligro">
                    📤 Registrar salida
                </a>

                <a href="inventario/historial.php"
                    class="btn-accion btn-editar">

                        📋 Historial de movimientos

                </a>

            </div>

        </div>


        <!-- =================================================
             PRODUCTOS
             ================================================= -->

        <div class="panel">

            <div style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            ">

                <h2 style="margin: 0;">
                    Productos registrados
                </h2>

                <a href="productos/listar.php">
                    Ver todos →
                </a>

            </div>


            <div class="tabla-contenedor">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Producto
                            </th>

                            <th>
                                Categoría
                            </th>

                            <th>
                                Precio
                            </th>

                            <th>
                                Stock
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($productos as $producto): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($producto["nombre"]) ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($producto["categoria"]) ?>
                            </td>

                            <td>
                                $<?= number_format(
                                    $producto["precio"],
                                    0,
                                    ",",
                                    "."
                                ) ?>
                            </td>

                            <td>

                                <?php if ($producto["stock"] == 0): ?>

                                    <span class="stock stock-agotado">
                                        Agotado
                                    </span>

                                <?php elseif ($producto["stock"] <= 5): ?>

                                    <span class="stock stock-bajo">
                                        <?= $producto["stock"] ?> unidades
                                    </span>

                                <?php else: ?>

                                    <span class="stock stock-disponible">
                                        <?= $producto["stock"] ?> unidades
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>


    </main>

</div>

</body>

</html>