<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once "../config/conexion.php";
require_once "../includes/funciones.php";

$error = "";
$exito = "";


/* =========================================================
   OBTENER PRODUCTOS
   ========================================================= */

$stmt = $conexion->query(
    "SELECT id, nombre, stock
     FROM productos
     ORDER BY nombre ASC"
);

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   PROCESAR SALIDA
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $producto_id = $_POST["producto_id"] ?? "";
    $cantidad = $_POST["cantidad"] ?? "";

    if (
        $producto_id === "" ||
        !filter_var($producto_id, FILTER_VALIDATE_INT)
    ) {

        $error = "Debe seleccionar un producto.";

    } elseif (
        !filter_var(
            $cantidad,
            FILTER_VALIDATE_INT
        ) ||
        $cantidad <= 0
    ) {

        $error = "La cantidad debe ser un número entero mayor que cero.";

    } else {

        try {

            /* Obtener stock actual */

            $sql = "SELECT stock
                    FROM productos
                    WHERE id = :producto_id
                    LIMIT 1";

            $stmt = $conexion->prepare($sql);

            $stmt->execute([
                "producto_id" => $producto_id
            ]);

            $producto = $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$producto) {

                $error = "El producto seleccionado no existe.";

            } elseif (
                !validarSalidaInventario(
                    $producto["stock"],
                    $cantidad
                )
            ) {

                $error =
                    "La cantidad solicitada supera el stock disponible.";

            } else {

                $conexion->beginTransaction();


                /* Descontar stock */

                $sql = "UPDATE productos
                        SET stock = stock - :cantidad
                        WHERE id = :producto_id";

                $stmt = $conexion->prepare($sql);

                $stmt->execute([
                    "cantidad" => $cantidad,
                    "producto_id" => $producto_id
                ]);


                /* Registrar movimiento */

                $sql = "INSERT INTO movimientos_inventario
                        (producto_id, tipo, cantidad)
                        VALUES
                        (:producto_id, 'SALIDA', :cantidad)";

                $stmt = $conexion->prepare($sql);

                $stmt->execute([
                    "producto_id" => $producto_id,
                    "cantidad" => $cantidad
                ]);


                $conexion->commit();

                $exito =
                    "Salida de inventario registrada correctamente.";

                $producto_id = "";
                $cantidad = "";
            }

        } catch (Exception $e) {

            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }

            $error = "No fue posible registrar la salida.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Salida de inventario - GestionStock</title>

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
                    background: rgba(255,255,255,0.15);
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
         CONTENIDO
         ================================================= -->

    <main style="
        flex: 1;
        padding: 35px;
        overflow-x: auto;
    ">


        <div class="encabezado-pagina">

            <div>

                <h1>
                    Salida de inventario
                </h1>

                <p class="subtitulo">
                    Retira unidades del stock de un producto
                </p>

            </div>

        </div>


        <!-- FORMULARIO -->

        <div class="panel">

            <h2>
                📤 Registrar salida
            </h2>

            <p class="subtitulo"
               style="margin-bottom: 25px;">

                Selecciona el producto e indica la cantidad
                que deseas retirar del inventario.

            </p>


            <?php if ($error): ?>

                <div class="error">

                    ⚠️ <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <?php if ($exito): ?>

                <div class="exito">

                    ✅ <?= htmlspecialchars($exito) ?>

                </div>

            <?php endif; ?>


            <form method="POST">


                <!-- PRODUCTO -->

                <div class="formulario-grupo">

                    <label for="producto_id">
                        Producto
                    </label>

                    <select
                        id="producto_id"
                        name="producto_id"
                        required
                    >

                        <option value="">
                            Seleccione un producto
                        </option>

                        <?php foreach ($productos as $producto): ?>

                            <option
                                value="<?= $producto["id"] ?>"
                                <?= (
                                    ($producto_id ?? "") ==
                                    $producto["id"]
                                )
                                ? "selected"
                                : ""
                                ?>
                            >

                                <?= htmlspecialchars(
                                    $producto["nombre"]
                                ) ?>

                                — Stock disponible:
                                <?= $producto["stock"] ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- CANTIDAD -->

                <div class="formulario-grupo">

                    <label for="cantidad">
                        Cantidad a retirar
                    </label>

                    <input
                        type="number"
                        id="cantidad"
                        name="cantidad"
                        value="<?= htmlspecialchars(
                            $cantidad ?? ""
                        ) ?>"
                        min="1"
                        step="1"
                        placeholder="Ej. 5"
                        required
                    >

                    <small style="
                        display: block;
                        margin-top: 6px;
                        color: #6b7280;
                    ">
                        La cantidad no puede superar el stock disponible.
                    </small>

                </div>


                <!-- BOTONES -->

                <div style="
                    display: flex;
                    gap: 12px;
                    margin-top: 25px;
                    flex-wrap: wrap;
                ">

                    <button
                        type="submit"
                        class="btn btn-peligro"
                    >
                        📤 Registrar salida
                    </button>


                    <a
                        href="../dashboard.php"
                        class="btn btn-secundario"
                    >
                        ← Cancelar
                    </a>

                </div>

            </form>

        </div>


        <!-- ADVERTENCIA -->

        <div class="panel">

            <h2>
                ⚠️ Control de stock
            </h2>

            <p style="color: #6b7280;">

                GestionStock verifica automáticamente que la
                cantidad solicitada no sea superior al stock
                disponible. Si supera las existencias, la
                operación será rechazada y no se registrará
                ningún movimiento.

            </p>

        </div>

    </main>

</div>

</body>

</html>