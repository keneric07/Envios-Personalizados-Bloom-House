# Changelog - Envío Personalizado con Fee

## 3.0.13 - 16 Dec 2025
- Agregado calendario datepicker para seleccionar fecha visualmente (además de escritura manual)
- El datepicker respeta el formato dd/mm/yyyy y la configuración de fecha mínima

## 3.0.12 - 16 Dec 2025
- Agregado switch en configuración para permitir o no pedidos del mismo día (sin necesidad de modificar código)
- Cambiado formato de fecha de mm/dd/yyyy a dd/mm/yyyy (formato más común)
- Mejorada validación de fecha con formato automático mientras el usuario escribe

## 3.0.11 - 16 Dec 2025
- El campo de fecha de envío ahora se autoselecciona con el día de mañana por defecto

## 3.0.10 - 16 Dec 2025
- Agregados enlaces directos en el listado de plugins para acceder a Configuración y Changelog

## 3.0.9 - 16 Dec 2025
- Ahora la fecha de envío no puede ser el día actual; solo se permiten fechas a partir de mañana.

## 3.0.8 - 16 Dec 2025
- Agregado campo obligatorio "Horario de envío o retiro" con opciones 9am-12pm y 1pm-4pm
- Mejorada validación de campos obligatorios en checkout (fecha, tipo, zona, dirección, horario)
- Horario y fecha ahora se combinan en la dirección 2 del pedido
- Validación reforzada con múltiples hooks para garantizar que no se procesen pedidos incompletos

## 3.0.6 - 2024
- Agregado formateo de fecha en español (día de la semana y mes traducidos)
- Fecha formateada se guarda automáticamente en dirección 2 del pedido
- Validación de fecha para evitar fechas anteriores a hoy

## 3.0.5 - 2024
- Mejorado sistema de cálculo de fees con prevención de duplicados
- Optimizado recálculo de totales en checkout con debounce
- Campos de delivery se muestran/ocultan dinámicamente según tipo de envío

## 3.0.4 - 2024
- Agregado campo de dirección de entrega personalizada
- Dirección de entrega se guarda en dirección 1 cuando es delivery
- Retiro en tienda muestra "Retiro en tienda" en dirección 1

## 3.0.3 - 2024
- Sistema de zonas de envío completamente configurable desde admin
- Interfaz de administración para agregar/editar/eliminar zonas
- Zonas con activación/desactivación individual
- Precios y descripciones personalizables por zona

## 3.0.2 - 2024
- Agregado campo de tipo de envío (Retiro en tienda / Delivery)
- Cálculo automático de fees según zona seleccionada
- Retiro en tienda sin costo adicional

## 3.0.1 - 2024
- Agregado campo de fecha de envío en checkout
- Campo de zona de envío con selector dinámico
- Integración con sistema de sesiones de WooCommerce

## 3.0.0 - 2024
- Versión inicial del plugin
- Sistema básico de zonas de envío predefinidas (7 zonas)
- Integración con WooCommerce checkout


