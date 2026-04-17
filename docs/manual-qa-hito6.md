# Checklist manual — TPV Hito 6

Guía corta para validar en unos minutos las funcionalidades añadidas:

- descuentos por línea y por total
- división de cuenta igual y personalizada
- devoluciones parciales y completas
- apertura y cierre de caja por turno
- ticket provisional
- traslado de mesa

## Preparación

1. Levanta el entorno.
2. Asegúrate de haber ejecutado las migraciones nuevas.
3. Entra con un usuario `admin` o `supervisor` para backoffice y con un usuario operativo para TPV.

Comandos útiles:

```bash
docker compose up -d
docker compose exec -T api php artisan migrate --force
```

## 1. Ticket provisional

Objetivo: comprobar que la comanda puede imprimirse sin cerrar la venta.

1. Ve a `TPV`.
2. Abre una mesa libre.
3. Añade 2 o 3 productos.
4. Pulsa `Ticket provisional`.
5. Verifica:
   - se abre la ventana de impresión del navegador
   - aparecen mesa, operador, comensales y líneas
   - el documento indica que no es factura
   - la mesa sigue abierta después de cerrar la impresión

## 2. Descuento por línea

Objetivo: comprobar que el descuento afecta solo a una línea concreta.

1. En una comanda abierta, pulsa `%` sobre una línea.
2. Introduce un descuento en porcentaje, por ejemplo `%10`.
3. Verifica:
   - aparece la insignia del descuento en la línea
   - baja el subtotal de esa línea
   - el total de la comanda se recalcula
4. Repite con un importe fijo, por ejemplo `2.50`.
5. Elimina el descuento dejando el prompt vacío.

Resultado esperado:
- solo cambia la línea editada
- los impuestos y el total se recalculan

## 3. Descuento total de la comanda

Objetivo: comprobar descuento global adicional.

1. En la cabecera de la comanda, pulsa `Descuento total`.
2. Introduce `%15`.
3. Verifica:
   - aparece el valor del descuento global en la cabecera
   - aparece una fila `Descuento total` en el resumen
   - el total final baja
4. Repite con importe fijo.
5. Elimina el descuento dejando el prompt vacío.

Resultado esperado:
- el descuento total se aplica sobre la base ya afectada por descuentos de línea

## 4. División de cuenta igual

Objetivo: comprobar el reparto automático por comensales.

1. Abre el modal de pago de una comanda con varios comensales.
2. Revisa el bloque `División de cuenta`.
3. Pulsa varias `Partes` del reparto igual.
4. Verifica:
   - la cantidad a pagar cambia al importe de la parte elegida
   - la descripción se rellena con el tramo correspondiente
5. Registra uno o más pagos parciales.

Resultado esperado:
- el pendiente baja correctamente
- el reparto se recalcula sobre el pendiente restante al reabrir el modal

## 5. División de cuenta personalizada

Objetivo: comprobar importes manuales por persona o grupo.

1. En el modal de pago, edita uno o varios campos del bloque personalizado.
2. Pulsa `Usar` en uno de ellos.
3. Verifica:
   - el importe pasa a `Cantidad a pagar`
   - la descripción cambia a `Cuenta personalizada`
4. Repite con varios importes hasta completar el total.

Resultado esperado:
- se pueden registrar pagos parciales con importes arbitrarios
- el total pagado no supera lo pendiente salvo propina

## 6. Traslado de mesa

Objetivo: comprobar que una comanda abierta se mueve a otra mesa libre.

1. Con una comanda abierta, pulsa `Trasladar mesa`.
2. Selecciona una mesa libre.
3. Verifica:
   - la comanda se abre en la nueva mesa
   - la mesa origen queda libre en el plano
   - la mesa destino aparece ocupada

## 7. Cierre de venta con descuentos

Objetivo: comprobar que el cierre persiste correctamente una venta con descuentos.

1. Aplica descuento por línea y/o total.
2. Cierra y cobra la venta.
3. Ve a `Backoffice > Ventas`.
4. Abre el detalle del ticket.
5. Verifica:
   - el ticket aparece en la lista
   - el neto coincide con lo cobrado
   - las líneas muestran importes y descuentos aplicados

## 8. Devolución parcial

Objetivo: comprobar devolución de líneas concretas sin anular toda la venta.

1. En `Backoffice > Ventas`, abre un ticket cerrado.
2. Selecciona método de devolución.
3. Introduce un motivo.
4. En una línea, indica una cantidad menor o igual a la disponible.
5. Pulsa `Devolver`.
6. Verifica:
   - la línea refleja unidades devueltas
   - el neto del ticket baja
   - aparece el importe devuelto en la lista

Resultado esperado:
- no se puede devolver más cantidad de la disponible
- la devolución queda trazada por método

## 9. Devolución completa

Objetivo: comprobar anulación económica total de una venta cerrada.

1. En el detalle de una venta, pulsa `Devolver venta completa`.
2. Verifica:
   - todas las líneas quedan devueltas
   - el `neto` de la venta pasa a cero
   - la venta sigue visible para trazabilidad

## 10. Apertura de caja

Objetivo: validar inicio de turno manual.

1. Entra con `admin` o `supervisor`.
2. Ve a `Backoffice > Caja`.
3. Si no hay turno abierto, introduce `Fondo inicial`.
4. Pulsa `Abrir caja`.
5. Verifica:
   - aparece el estado `Abierto`
   - se muestra la hora de apertura
   - no permite abrir una segunda caja simultánea

## 11. Cierre de caja

Objetivo: validar cierre profesional de turno con desglose.

1. Con la caja abierta, registra algunas ventas con varios métodos de pago.
2. Si es posible, haz también una devolución.
3. Vuelve a `Backoffice > Caja`.
4. Verifica:
   - aparecen `Efectivo neto`, `Tarjeta neto`, `Bizum neto` y `Devoluciones`
   - `Efectivo esperado` incluye el fondo inicial y el efectivo neto
5. Introduce `Efectivo contado`.
6. Pulsa `Cerrar caja`.
7. Verifica:
   - la caja pasa a estado cerrado
   - se calcula la diferencia entre esperado y contado
   - queda visible el último cierre

## 12. Permisos

Objetivo: comprobar acceso correcto por rol.

### `admin`
- Debe poder usar `Ventas`, `Informes`, `Registro`, `Caja` y `Ajustes`

### `supervisor`
- Debe poder usar `Ventas`, `Informes`, `Registro`, `Caja` y `Ajustes`

### `waiter`
- No debe entrar en backoffice
- Debe seguir usando TPV

## Resultado esperado final

La validación puede darse por buena si se cumple todo esto:

- se puede abrir una venta, moverla, imprimir ticket provisional y cobrarla
- descuentos de línea y total recalculan bien el importe final
- la división de cuenta permite pagos iguales y personalizados
- las devoluciones parciales y completas reducen el neto sin borrar trazabilidad
- la caja se abre y se cierra por turno, con desglose por método y diferencia de efectivo
- `admin` y `supervisor` acceden a caja; `waiter` no accede al backoffice
