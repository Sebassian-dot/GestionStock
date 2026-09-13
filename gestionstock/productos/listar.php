<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/conexion.php";

$sql = "SELECT
            p.id,
            p.nombre,
            p.descripcion,
            p.precio,
            p.stock,
            c.nombre AS categoria
        FROM productos p
        INNER JOIN categorias c
            ON p.categoria_id = c.id
        ORDER BY p.id ASC";

$stmt = $conexion->query($sql);

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Productos - GestionStock</title>

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

            <small style="color: #bfdbfe;">
                Sistema de inventario
            </small>

        </div>


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

            <a href="listar.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    background: rgba(255,255,255,0.15);
                    color: white;
               ">
                📦 Productos
            </a>

            <a href="../inventario/entrada.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    color: white;
               ">
                📥 Entrada de inventario
            </a>

            <a href="../inventario/salida.php"
               style="
                    display: block;
                    padding: 12px 15px;
                    margin-bottom: 8px;
                    border-radius: 8px;
                    color: white;
               ">
                📤 Salida de inventario
            </a>

        </nav>


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

                <small style="color: #bfdbfe;">
                    Usuario
                </small>

                <div style="font-weight: bold;">
                    <?= htmlspecialchars($_SESSION["usuario_nombre"]) ?>
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
         CONTENIDO
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
                    Productos
                </h1>

                <p class="subtitulo">
                    Administra los productos registrados en el inventario
                </p>

            </div>

            <a href="crear.php"
               class="btn">

                ➕ Nuevo producto

            </a>

        </div>


        <!-- =================================================
             TABLA
             ================================================= -->

        <div class="panel">

            <div style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            ">

                <div>

                    <h2 style="margin-bottom: 5px;">
                        Lista de productos
                    </h2>

                    <p class="subtitulo">
                        <?= count($productos) ?> productos registrados
                    </p>

                </div>

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
                                Descripción
                            </th>

                            <th>
                                Precio
                            </th>

                            <th>
                                Stock
                            </th>

                            <th>
                                Categoría
                            </th>

                            <th>
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if (count($productos) > 0): ?>

                        <?php foreach ($productos as $producto): ?>

                            <tr>

                                <td>
                                    #<?= $producto["id"] ?>
                                </td>


                                <td>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $producto["nombre"]
                                        ) ?>
                                    </strong>

                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $producto["descripcion"]
                                    ) ?>
                                </td>


                                <td>

                                    <strong>
                                        $<?= number_format(
                                            $producto["precio"],
                                            0,
                                            ",",
                                            "."
                                        ) ?>
                                    </strong>

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


                                <td>
                                    <?= htmlspecialchars(
                                        $producto["categoria"]
                                    ) ?>
                                </td>


                                <td>

                                    <div class="acciones-producto">

                                        <a href="editar.php?id=<?= $producto["id"] ?>"
                                            class="btn-accion btn-editar">

                                                ✏️ Editar

                                        </a>

                                        <a href="eliminar.php?id=<?= $producto["id"] ?>"
                                        class="btn-accion btn-eliminar"
                                        onclick="return confirm(
                                            '¿Desea eliminar este producto?'
                                        );">

                                            🗑️ Eliminar

                                        </a>

                                    </div>

                                    

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="7"
                                style="
                                    text-align: center;
                                    padding: 40px;
                                    color: #6b7280;
                                ">

                                📦 No hay productos registrados.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- VOLVER -->

        <a href="../dashboard.php">
            ← Volver al Dashboard
        </a>

    </main>

</div>

</body>

</html>