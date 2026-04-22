# ADR 0001: items aparcados del plan de refactor TPV

## Estado

Aceptado.

## Contexto

Se revisó `PLAN_REFACTOR_TPV.md` con el criterio acordado de aplicar solo cambios que aporten valor claro ahora mismo, sin introducir sobreingeniería y manteniendo la arquitectura DDD/hexagonal/SOLID ya presente en el backend.

Durante esa revisión se implementaron varias mejoras de bajo riesgo y alto retorno, pero algunos puntos del plan se aparcan porque hoy añaden coste estructural superior a su beneficio, duplican abstracciones existentes o fuerzan cambios de contrato más amplios de los que esta iteración necesita.

## Decisión

Los siguientes puntos quedan aparcados hasta que exista una necesidad funcional o técnica concreta que justifique su coste:

### `2.2` `OrderLinePricingService` / ACL entre contextos

- Hoy el cálculo relevante ya queda encapsulado en el dominio de `Order` y `OrderLine` mediante `DiscountPolicy`, `Money` y `OrderTotals`.
- Extraer otro servicio o una ACL adicional ahora mismo solo desplazaría lógica sin reducir complejidad real.
- Se reabre si aparece una segunda fuente de pricing con reglas distintas o si el cálculo debe compartirse entre bounded contexts con contratos incompatibles.

### `2.6` optimización con `persistenceId`

- El coste principal detectado no estaba en un `persistenceId`, sino en accesos N+1 y búsquedas repetidas, ya atacados con cargas en bloque, eager loading y traducción de errores en adaptadores.
- Introducir identificadores internos persistentes en puertos de dominio mezclaría aún más modelo de dominio e infraestructura.
- Se reabre si el profiling muestra que la conversión `uuid -> id` sigue siendo un cuello de botella relevante tras las optimizaciones actuales.

### `1.7` `CurrentUserProviderInterface`

- El código actual ya pasa `AuditContext` de forma explícita en los casos de uso sensibles.
- Añadir otro proveedor global de usuario actual ocultaría dependencias y haría menos claro qué flujos dependen del actor autenticado.
- Se reabre si aparece un número significativo de casos de uso con el mismo patrón repetitivo que no pueda resolverse manteniendo dependencias explícitas.

### `1.8` unificación/renombre de `Quantity`

- Existen dos value objects con semánticas distintas: `Order\Domain\ValueObject\Quantity` exige mínimo `1`, mientras `Shared\Domain\ValueObject\Quantity` permite `0` porque sirve para stock, acumulados y restas seguras.
- Unificarlos hoy forzaría una semántica ambigua o validaciones condicionales menos claras.
- Se documenta la divergencia en ambos archivos y se reabre si se define un lenguaje ubicuo más preciso para separar definitivamente `OrderLineQuantity`, `StockQuantity` u otro nombre equivalente.

### `1.9` eliminación total de getters gemelos

- Ya se limpió una parte del ruido más visible, pero forzar una eliminación completa ahora tocaría demasiada superficie sin impacto funcional directo.
- Mientras no generen ambigüedad real o contratos públicos redundantes problemáticos, se priorizan cambios con retorno mayor.
- Se reabre cuando se aborde una pasada dedicada de API de dominio o cuando un módulo concreto quede bloqueado por esa duplicidad.

### `3.3` value objects en puertos

- Llevar VOs a todos los puertos incrementaría el acoplamiento transversal entre aplicación, transporte e infraestructura.
- En varios límites externos el contrato en escalares sigue siendo más estable y más fácil de serializar, validar y versionar.
- Se reabre solo donde exista un error repetido de primitivas o donde un puerto interno estrictamente de dominio gane claridad neta con VOs extremos a extremo.

## Consecuencias

- El refactor actual se mantiene enfocado en problemas reales detectados: cálculo de totales, enums nativos, invalidación de caché, locking de refund, traducción de excepciones, unicidad de órdenes abiertas y eliminación de N+1 en órdenes abiertas.
- Se evita ampliar el alcance con abstracciones que todavía no pagan su complejidad.
- La deuda queda explícita y trazable para una futura iteración, en lugar de perderse como una decisión implícita.

## Señales para reabrir esta ADR

- Nuevos requisitos de pricing compartido entre contextos.
- Métricas de profiling que sigan mostrando coste relevante en resolución `uuid -> id`.
- Repetición sistemática del acceso al usuario autenticado que empeore la legibilidad de los casos de uso.
- Errores funcionales derivados de la ambigüedad entre cantidades de pedido y cantidades compartidas.
- Contratos de puertos donde el uso de primitivas esté causando bugs repetidos o validaciones duplicadas.