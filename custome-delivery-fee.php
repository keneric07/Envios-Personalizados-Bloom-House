<?php
/**
 * Plugin Name: Envío Personalizado con Fee (Configurable)
 * Description: Campos de fecha, tipo de envío, zona y dirección en checkout, con configuración de zonas editable desde el admin.
 * Version: 3.0.7
 * Author: Keneric / ChatGPT
 * Text Domain: envio-fee
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// Default zones
function envio_fee_default_zones() {
    return array(
        'zona1' => array(
            'activo' => true,
            'nombre' => 'Área 1',
            'precio' => 5.00,
            'descripcion' => 'Obarrio, Vía España, Calle 50, San Francisco, El Carmen, El Cangrejo, Vía Brasil'
        ),
        'zona2' => array(
            'activo' => true,
            'nombre' => 'Área 2',
            'precio' => 6.00,
            'descripcion' => 'Punta Pacífica, Paitilla, Marbella, Coco del Mar, La Cresta, Bella Vista'
        ),
        'zona3' => array(
            'activo' => true,
            'nombre' => 'Área 3',
            'precio' => 9.00,
            'descripcion' => 'Costa del Este, Ave. Balboa, Hato Pintado, Parque Lefevre, Chanis'
        ),
        'zona4' => array(
            'activo' => true,
            'nombre' => 'Área 4',
            'precio' => 10.00,
            'descripcion' => 'Santa María, Bethania, El Dorado, Condado, Plaza Edison'
        ),
        'zona5' => array(
            'activo' => true,
            'nombre' => 'Área 5',
            'precio' => 10.00,
            'descripcion' => 'Albrook, Clayton, Ciudad del Saber, Casco Viejo, Villa Lucre'
        ),
        'zona6' => array(
            'activo' => true,
            'nombre' => 'Área 6',
            'precio' => 15.00,
            'descripcion' => 'Costa Sur, Versalles, Brisas del Golf, Cerro Viento, San Antonio, El Crisol'
        ),
        'zona7' => array(
            'activo' => true,
            'nombre' => 'Área 7',
            'precio' => 20.00,
            'descripcion' => 'Panamá Pacífico, Paseo del Norte, Playa Bonita, Villa Zaita, Ciudad de la Salud'
        ),
    );
}

// Get zones
function envio_fee_get_zones() {
    $zones = get_option('envio_fee_zones');
    if ( ! is_array($zones) ) {
        $zones = envio_fee_default_zones();
        update_option('envio_fee_zones', $zones);
    }
    return $zones;
}

// Enqueue Dashicons for frontend
add_action('wp_enqueue_scripts', function(){
    if (is_checkout()) {
        wp_enqueue_style('dashicons');
    }
});

// Admin menu
add_action('admin_menu', function() {
    add_submenu_page(
        'woocommerce',
        __('Envío Personalizado', 'envio-fee'),
        __('Envío Personalizado', 'envio-fee'),
        'manage_options',
        'envio-fee-settings',
        'envio_fee_settings_page'
    );
});

// Admin page HTML
function envio_fee_settings_page() {
    if ( isset($_POST['envio_fee_save']) && check_admin_referer('envio_fee_save_action', 'envio_fee_nonce') ) {
        $zones = array();
        if (!empty($_POST['nombre'])) {
            foreach ($_POST['nombre'] as $i => $nombre) {
                $key = sanitize_title($nombre ?: 'zona' . $i);
                $zones[$key] = array(
                    'activo' => !empty($_POST['activo'][$i]),
                    'nombre' => sanitize_text_field($nombre),
                    'precio' => floatval($_POST['precio'][$i]),
                    'descripcion' => sanitize_textarea_field($_POST['descripcion'][$i]),
                );
            }
        }
        update_option('envio_fee_zones', $zones);
        echo '<div class="updated"><p>'.__('Zonas guardadas correctamente.', 'envio-fee').'</p></div>';
    }

    $zones = envio_fee_get_zones();
    ?>
    <div class="wrap">
        <h1><?php _e('Configuración de Zonas de Envío', 'envio-fee'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('envio_fee_save_action', 'envio_fee_nonce'); ?>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('Activo', 'envio-fee'); ?></th>
                        <th><?php _e('Nombre', 'envio-fee'); ?></th>
                        <th><?php _e('Precio', 'envio-fee'); ?></th>
                        <th><?php _e('Descripción', 'envio-fee'); ?></th>
                        <th><?php _e('Eliminar', 'envio-fee'); ?></th>
                    </tr>
                </thead>
                <tbody id="envio-fee-rows">
                    <?php $i=0; foreach ($zones as $zone): ?>
                        <tr>
                            <td><input type="checkbox" name="activo[<?php echo $i; ?>]" <?php checked($zone['activo']); ?>></td>
                            <td><input type="text" name="nombre[<?php echo $i; ?>]" value="<?php echo esc_attr($zone['nombre']); ?>"></td>
                            <td><input type="number" step="0.01" name="precio[<?php echo $i; ?>]" value="<?php echo esc_attr($zone['precio']); ?>"></td>
                            <td><textarea name="descripcion[<?php echo $i; ?>]"><?php echo esc_textarea($zone['descripcion']); ?></textarea></td>
                            <td><button type="button" class="button remove-row">X</button></td>
                        </tr>
                    <?php $i++; endforeach; ?>
                </tbody>
            </table>
            <p><button type="button" class="button" id="add-zone"><?php _e('Agregar Zona', 'envio-fee'); ?></button></p>
            <p><input type="submit" name="envio_fee_save" class="button-primary" value="<?php _e('Guardar Cambios', 'envio-fee'); ?>"></p>
        </form>
    </div>
    <script>
    document.getElementById('add-zone').addEventListener('click', function(){
        var tbody = document.getElementById('envio-fee-rows');
        var index = tbody.rows.length;
        var row = document.createElement('tr');
        row.innerHTML = '<td><input type="checkbox" name="activo['+index+']"></td>'+
                        '<td><input type="text" name="nombre['+index+']"></td>'+
                        '<td><input type="number" step="0.01" name="precio['+index+']"></td>'+
                        '<td><textarea name="descripcion['+index+']"></textarea></td>'+
                        '<td><button type="button" class="button remove-row">X</button></td>';
        tbody.appendChild(row);
    });
    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('remove-row')){
            e.target.closest('tr').remove();
        }
    });
    </script>
    <?php
}

// Checkout fields
add_action('woocommerce_review_order_after_payment', function(){
    $hoy = date('Y-m-d');
    $zones = envio_fee_get_zones();
    ?>
    <div id="envio-fee-fields">
        <style>
            #envio-fee-fields label {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            #envio-fee-fields .dashicons {
                font-size: 18px;
                width: 18px;
                height: 18px;
                color: #666;
            }
        </style>
        <p class="form-row form-row-wide validate-required">
            <label for="fecha_envio_custom" class="">
                <span class="dashicons dashicons-calendar-alt"></span>
                <?php _e('Fecha de envío', 'envio-fee'); ?>
                <abbr class="required" title="required">*</abbr>
            </label>
            <input type="date" id="fecha_envio_custom" name="fecha_envio_custom" class="input-text update_totals_on_change" min="<?php echo esc_attr($hoy); ?>" required aria-required="true">
        </p>
        <p class="form-row form-row-wide">
            <label for="custom_shipping_type">
                <span class="dashicons dashicons-cart"></span>
                <?php _e('Tipo de envío', 'envio-fee'); ?>
            </label>
            <select id="custom_shipping_type" name="custom_shipping_type" class="update_totals_on_change" required>
                <option value="retiro"><?php _e('Retiro en tienda (gratis)', 'envio-fee'); ?></option>
                <option value="delivery"><?php _e('Delivery', 'envio-fee'); ?></option>
            </select>
        </p>
        <p class="form-row form-row-wide delivery-only validate-required" style="display:none;">
            <label for="custom_shipping_zone" class="">
                <span class="dashicons dashicons-location-alt"></span>
                <?php _e('Zona de envío', 'envio-fee'); ?>
                <abbr class="required" title="required">*</abbr>
            </label>
            <select id="custom_shipping_zone" name="custom_shipping_zone" class="select update_totals_on_change" aria-required="true">
                <option value=""><?php _e('Selecciona tu zona…', 'envio-fee'); ?></option>
                <?php foreach ($zones as $key=>$zone): if($zone['activo']): ?>
                    <option value="<?php echo esc_attr($key); ?>">
                        <?php echo esc_html($zone['nombre'] . ' - $' . number_format($zone['precio'], 2) . ' – ' . $zone['descripcion']); ?>
                    </option>
                <?php endif; endforeach; ?>
            </select>
        </p>
        <p class="form-row form-row-wide delivery-only validate-required" style="display:none;">
            <label for="direccion_delivery_custom" class="">
                <span class="dashicons dashicons-location"></span>
                <?php _e('Dirección de entrega', 'envio-fee'); ?>
                <abbr class="required" title="required">*</abbr>
            </label>
            <input type="text" id="direccion_delivery_custom" name="direccion_delivery_custom" class="input-text update_totals_on_change" aria-required="true">
        </p>
        <script>
        (function($){
            function toggleDeliveryFields(){
                if($('#custom_shipping_type').val()=='delivery'){
                    $('.delivery-only').show();
                    $('#custom_shipping_zone, #direccion_delivery_custom').attr('required', true).attr('aria-required', 'true');
                    $('.delivery-only').addClass('validate-required');
                } else {
                    $('.delivery-only').hide();
                    $('#custom_shipping_zone, #direccion_delivery_custom').removeAttr('required').removeAttr('aria-required');
                    $('.delivery-only').removeClass('validate-required');
                }
            }
            var debounceTimer;
            function pingTotals(){
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function(){
                    $(document.body).trigger('update_checkout');
                }, 150);
            }
            $(document).on('change input', '#custom_shipping_type, #custom_shipping_zone, #direccion_delivery_custom, #fecha_envio_custom', function(){
                toggleDeliveryFields();
                pingTotals();
            });
            $(document).ready(function(){
                toggleDeliveryFields();
                // primer recálculo al cargar
                pingTotals();
            });
        })(jQuery);
        </script>
    </div>
    <?php
});

// Save session from order review update (string or array)
add_action('woocommerce_checkout_update_order_review', function($post_data){
    $data = array();
    if (is_string($post_data)) { parse_str($post_data, $data); }
    elseif (is_array($post_data)) { $data = $post_data; }
    if (function_exists('WC') && WC()->session){
        $tipo = isset($data['custom_shipping_type']) ? sanitize_text_field($data['custom_shipping_type']) : '';
        $zona = isset($data['custom_shipping_zone']) ? sanitize_text_field($data['custom_shipping_zone']) : '';
        WC()->session->set('custom_shipping_type', $tipo);
        WC()->session->set('custom_shipping_zone', $zona);
    }
}, 10, 1);

// Validation
add_action('woocommerce_checkout_process', function(){
    $tipo = sanitize_text_field($_POST['custom_shipping_type'] ?? '');
    
    // Validar fecha de envío (obligatoria siempre)
    if (empty($_POST['fecha_envio_custom'])) {
        wc_add_notice(__('Por favor selecciona la fecha de envío.', 'envio-fee'), 'error');
    } else {
        // Validar que la fecha sea válida y no sea anterior a hoy
        $fecha = sanitize_text_field($_POST['fecha_envio_custom']);
        $hoy = date('Y-m-d');
        if ($fecha < $hoy) {
            wc_add_notice(__('La fecha de envío no puede ser anterior a hoy.', 'envio-fee'), 'error');
        }
    }
    
    // Validar campos obligatorios cuando es delivery
    if ($tipo === 'delivery') {
        if (empty($_POST['custom_shipping_zone'])) {
            wc_add_notice(__('Selecciona tu zona de envío.', 'envio-fee'), 'error');
        }
        if (empty($_POST['direccion_delivery_custom'])) {
            wc_add_notice(__('Indica la dirección de entrega.', 'envio-fee'), 'error');
        }
    }
});

// Calculate fees (robust, single-pass)
add_action('woocommerce_cart_calculate_fees', function($cart){
    if (is_admin() && !defined('DOING_AJAX')) return;
    if (did_action('woocommerce_cart_calculate_fees') > 1) return;
    if (!function_exists('WC') || !WC()->session) return;

    $tipo = WC()->session->get('custom_shipping_type');
    $zona = WC()->session->get('custom_shipping_zone');
    $zones = envio_fee_get_zones();

    if ($tipo === 'delivery' && !empty($zona) && isset($zones[$zona]) && !empty($zones[$zona]['activo'])) {
        $fee = floatval($zones[$zona]['precio']);
        if ($fee > 0) {
            $cart->add_fee(__('Costo de Envío', 'envio-fee'), $fee, true, '');
        }
    }
}, 20, 1);

// Format date in Spanish
function envio_fee_format_date_spanish($date) {
    if (empty($date)) {
        return '';
    }
    
    // Arrays de traducción
    $dias = array(
        'Sunday' => 'domingo',
        'Monday' => 'lunes',
        'Tuesday' => 'martes',
        'Wednesday' => 'miércoles',
        'Thursday' => 'jueves',
        'Friday' => 'viernes',
        'Saturday' => 'sábado'
    );
    
    $meses = array(
        'January' => 'enero',
        'February' => 'febrero',
        'March' => 'marzo',
        'April' => 'abril',
        'May' => 'mayo',
        'June' => 'junio',
        'July' => 'julio',
        'August' => 'agosto',
        'September' => 'septiembre',
        'October' => 'octubre',
        'November' => 'noviembre',
        'December' => 'diciembre'
    );
    
    // Convertir fecha a timestamp
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return '';
    }
    
    // Obtener día de la semana y mes en inglés
    $dia_semana_eng = date('l', $timestamp);
    $mes_eng = date('F', $timestamp);
    $dia = date('j', $timestamp);
    $ano = date('Y', $timestamp);
    
    // Traducir a español
    $dia_semana_esp = isset($dias[$dia_semana_eng]) ? ucfirst($dias[$dia_semana_eng]) : $dia_semana_eng;
    $mes_esp = isset($meses[$mes_eng]) ? $meses[$mes_eng] : $mes_eng;
    
    return sprintf('Enviar el día: %s %d de %s de %s', $dia_semana_esp, $dia, $mes_esp, $ano);
}

// Save order meta
add_action('woocommerce_checkout_create_order', function($order, $data){
    // Obtener datos del formulario
    $tipo_envio = sanitize_text_field($_POST['custom_shipping_type'] ?? '');
    $fecha_envio = sanitize_text_field($_POST['fecha_envio_custom'] ?? '');
    $direccion_delivery = sanitize_text_field($_POST['direccion_delivery_custom'] ?? '');
    
    // Copiar direcciones de facturación a envío
    $billing_address_1 = $order->get_billing_address_1();
    $billing_address_2 = $order->get_billing_address_2();
    
    // Establecer dirección 1 de envío igual a facturación
    if (!empty($billing_address_1)) {
        $order->set_shipping_address_1($billing_address_1);
    }
    
    // Establecer dirección 2 de envío igual a facturación
    if (!empty($billing_address_2)) {
        $order->set_shipping_address_2($billing_address_2);
    }
    
    // Formatear fecha en español
    $fecha_formateada = envio_fee_format_date_spanish($fecha_envio);
    
    // Agregar fecha formateada a dirección 2 (combinar con dirección 2 de facturación si existe)
    if (!empty($fecha_formateada)) {
        $direccion_2_actual = $order->get_shipping_address_2();
        if (!empty($direccion_2_actual)) {
            // Si ya hay dirección 2, combinar con fecha
            $order->set_shipping_address_2($direccion_2_actual . ' | ' . $fecha_formateada);
        } else {
            // Si no hay dirección 2, solo poner la fecha
            $order->set_shipping_address_2($fecha_formateada);
        }
    }
    
    // Sobrescribir dirección 1 según el tipo de envío
    if ($tipo_envio === 'delivery' && !empty($direccion_delivery)) {
        $order->set_shipping_address_1($direccion_delivery);
    } elseif ($tipo_envio === 'retiro') {
        $order->set_shipping_address_1('Retiro en tienda');
    }
    
    // Mantener meta data para compatibilidad
    $order->update_meta_data('_fecha_envio_custom', $fecha_envio);
    $order->update_meta_data('_custom_shipping_type', $tipo_envio);
    $order->update_meta_data('_custom_shipping_zone', sanitize_text_field($_POST['custom_shipping_zone'] ?? ''));
    $order->update_meta_data('_direccion_delivery_custom', $direccion_delivery);
}, 10, 2);
