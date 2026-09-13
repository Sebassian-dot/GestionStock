<?php

/**
 * Valida que un precio sea mayor que cero.
 */
function validarPrecio($precio)
{
    return is_numeric($precio) && $precio > 0;
}

/**
 * Valida que el stock sea un número entero
 * mayor o igual a cero.
 */
function validarStock($stock)
{
    return filter_var(
        $stock,
        FILTER_VALIDATE_INT
    ) !== false && $stock >= 0;
}

/**
 * Valida una salida de inventario.
 *
 * La cantidad solicitada debe ser mayor que cero
 * y no puede superar el stock disponible.
 */
function validarSalidaInventario($stockDisponible, $cantidad)
{
    return validarStock($stockDisponible)
        && is_numeric($cantidad)
        && $cantidad > 0
        && $cantidad <= $stockDisponible;
}