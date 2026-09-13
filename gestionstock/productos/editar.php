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
   VALIDAR ID DEL PRODUCTO
   ========================================================= */

$id = $_GET["id"] ?? null;

if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: listar.php");
    exit;
}


/* =========================================================
   OBTENER PRODUCTO
   ========================================================= */

$sql = "SELECT *
        FROM productos
        WHERE id = :id
        LIMIT 1";

$stmt = $conexion->prepare($sql);

$stmt->execute([
    "id" => $id
]);

$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: listar.php");
    exit;
}


/* =========================================================
   OBTENER CATEGORÍAS
   ========================================================= */

$stmt = $conexion->query(
    "SELECT id, nombre
     FROM categorias
     ORDER BY nombre ASC"
);

$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   DATOS INICIALES
   ========================================================= */

$nombre = $producto["nombre"];
$descripcion = $producto["descripcion"];
$precio = $producto["precio"];
$stock = $producto["stock"];
$categoria_id = $producto["categoria_id"];


/* =========================================================
   PROCESAR FORMULARIO
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio = $_POST["precio"] ?? "";
    $stock = $_POST["stock"] ?? "";
    $categoria_id = $_POST["categoria_id"] ?? "";


    /* Validar nombre */

    if ($nombre === "") {

        $error = "El nombre del producto es obligatorio.";

    }


    /* Validar precio */

    elseif (!validarPrecio($precio)) {

        $error = "El precio debe ser mayor que cero.";

    }


    /* Validar stock */

    elseif (!validarStock($stock)) {

        $error = "El stock debe ser un número entero mayor o igual a cero.";

    }


    /* Validar categoría */

    elseif (
        $categoria_id === "" ||
        !filter_var($categoria_id, FILTER_VALIDATE_INT)
    ) {

        $error = "Debe seleccionar una categoría.";

    }


    /* Actualizar producto */

    else {

        $sql = "UPDATE productos
                SET nombre = :nombre,
                    descripcion = :descripcion,
                    precio = :precio,
                    stock = :stock,
                    categoria_id = :categoria_id
                WHERE id = :id";

        $stmt = $conexion->prepare($sql);

        $stmt->execute([
            "nombre" => $nombre,
            "descripcion" => $descripcion,
            "precio" => $precio,
            "stock" => $stock,
            "categoria_id" => $categoria_id,
            "id" => $id
        ]);

        $exito = "Producto actualizado correctamente.";

        /* Actualizar datos mostrados */

        $producto["nombre"] = $nombre;
        $producto["descripcion"] = $descripcion;
        $producto["precio"] = $precio;
        $producto["stock"] = $stock;
        $producto["categoria_id"] = $categoria_id;
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Editar producto - GestionStock</title>

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
                    Editar producto
                </h1>

                <p class="subtitulo">
                    Modifica la información del producto seleccionado
                </p>

            </div>

        </div>


        <!-- =================================================
             FORMULARIO
             ================================================= -->

        <div class="panel">

            <h2>
                Información del producto
            </h2>


            <!-- IDENTIFICADOR -->

            <div style="
                background: #eff6ff;
                border: 1px solid #dbeafe;
                padding: 12px 15px;
                border-radius: 8px;
                margin-bottom: 25px;
                color: #1e40af;
            ">

                📦 Editando producto
                <strong>
                    #<?= $producto["id"] ?>
                </strong>

            </div>


            <!-- ERROR -->

            <?php if ($error): ?>

                <div class="error">

                    ⚠️ <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <!-- ÉXITO -->

            <?php if ($exito): ?>

                <div class="exito">

                    ✅ <?= htmlspecialchars($exito) ?>

                </div>

            <?php endif; ?>


            <form method="POST">


                <!-- NOMBRE -->

                <div class="formulario-grupo">

                    <label for="nombre">
                        Nombre del producto
                    </label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="<?= htmlspecialchars($nombre) ?>"
                        maxlength="150"
                        required
                    >

                </div>


                <!-- DESCRIPCIÓN -->

                <div class="formulario-grupo">

                    <label for="descripcion">
                        Descripción
                    </label>

                    <textarea
                        id="descripcion"
                        name="descripcion"
                        maxlength="255"
                    ><?= htmlspecialchars($descripcion) ?></textarea>

                </div>


                <!-- PRECIO Y STOCK -->

                <div style="
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                ">


                    <!-- PRECIO -->

                    <div class="formulario-grupo">

                        <label for="precio">
                            Precio
                        </label>

                        <input
                            type="number"
                            id="precio"
                            name="precio"
                            value="<?= htmlspecialchars($precio) ?>"
                            min="0"
                            step="0.01"
                            required
                        >

                        <small style="
                            display: block;
                            margin-top: 6px;
                            color: #6b7280;
                        ">
                            El precio debe ser mayor que $0.
                        </small>

                    </div>


                    <!-- STOCK -->

                    <div class="formulario-grupo">

                        <label for="stock">
                            Stock
                        </label>

                        <input
                            type="number"
                            id="stock"
                            name="stock"
                            value="<?= htmlspecialchars($stock) ?>"
                            min="0"
                            step="1"
                            required
                        >

                        <small style="
                            display: block;
                            margin-top: 6px;
                            color: #6b7280;
                        ">
                            Debe ser un número entero mayor o igual a 0.
                        </small>

                    </div>

                </div>


                <!-- CATEGORÍA -->

                <div class="formulario-grupo">

                    <label for="categoria_id">
                        Categoría
                    </label>

                    <select
                        id="categoria_id"
                        name="categoria_id"
                        required
                    >

                        <?php foreach ($categorias as $categoria): ?>

                            <option
                                value="<?= $categoria["id"] ?>"
                                <?= (
                                    $categoria_id ==
                                    $categoria["id"]
                                )
                                ? "selected"
                                : ""
                                ?>
                            >

                                <?= htmlspecialchars(
                                    $categoria["nombre"]
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

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
                        class="btn"
                    >
                        💾 Guardar cambios
                    </button>


                    <a
                        href="listar.php"
                        class="btn btn-secundario"
                    >
                        ← Cancelar
                    </a>

                </div>


            </form>

        </div>


        <!-- INFORMACIÓN -->

        <div class="panel">

            <h2>
                💡 Información
            </h2>

            <p style="color: #6b7280;">

                Los cambios realizados se actualizarán
                inmediatamente en el inventario.

                Verifica que los datos sean correctos antes
                de guardar.

            </p>

        </div>


    </main>

</div>

</body>

</html>