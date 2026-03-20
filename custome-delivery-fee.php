<?php
/**
 * Plugin Name: Delivery Express - Envíos Programados y Personalizados
 * Description: Permite a tus clientes elegir fecha y horario de entrega, configurar zonas de envío con precios personalizados, y gestionar retiros en tienda. Mejora la experiencia de compra con entregas a medida.
 * Version: 3.17.14
 * Author: Keneric / HWStudio Labs
 * Text Domain: envio-fee
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// Agregar enlaces de acción en el listado de plugins
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=envio-fee-settings&tab=settings') . '">' . __('Configuración', 'envio-fee') . '</a>';
    $changelog_link = '<a href="' . admin_url('admin.php?page=envio-fee-settings&tab=changelog') . '">' . __('Changelog', 'envio-fee') . '</a>';
    array_unshift($links, $changelog_link, $settings_link);
    return $links;
});

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

// Enqueue Dashicons and jQuery UI Datepicker for frontend
add_action('wp_enqueue_scripts', function(){
    if (is_checkout()) {
        wp_enqueue_style('dashicons');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-datepicker', 'https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css', array(), '1.13.2');

        $phone_inline_js = <<<'JS'
(function($){
    var countries = [
        { label: 'United States', code: '1' }, { label: 'United Kingdom', code: '44' }, { label: 'Afghanistan', code: '93' },
        { label: 'Albania', code: '355' }, { label: 'Algeria', code: '213' }, { label: 'American Samoa', code: '1' },
        { label: 'Andorra', code: '376' }, { label: 'Angola', code: '244' }, { label: 'Anguilla', code: '1' },
        { label: 'Antigua & Barbuda', code: '1' }, { label: 'Argentina', code: '54' }, { label: 'Armenia', code: '374' },
        { label: 'Aruba', code: '297' }, { label: 'Ascension Island', code: '247' }, { label: 'Australia', code: '61' },
        { label: 'Austria', code: '43' }, { label: 'Azerbaijan', code: '994' }, { label: 'Bahamas', code: '1' },
        { label: 'Bahrain', code: '973' }, { label: 'Bangladesh', code: '880' }, { label: 'Barbados', code: '1' },
        { label: 'Belarus', code: '375' }, { label: 'Belgium', code: '32' }, { label: 'Belize', code: '501' },
        { label: 'Benin', code: '229' }, { label: 'Bermuda', code: '1' }, { label: 'Bhutan', code: '975' },
        { label: 'Bolivia', code: '591' }, { label: 'Bosnia & Herzegovina', code: '387' }, { label: 'Botswana', code: '267' },
        { label: 'Brazil', code: '55' }, { label: 'British Indian Ocean Territory', code: '246' }, { label: 'British Virgin Islands', code: '1' },
        { label: 'Brunei', code: '673' }, { label: 'Bulgaria', code: '359' }, { label: 'Burkina Faso', code: '226' },
        { label: 'Burundi', code: '257' }, { label: 'Cambodia', code: '855' }, { label: 'Cameroon', code: '237' },
        { label: 'Canada', code: '1' }, { label: 'Cape Verde', code: '238' }, { label: 'Caribbean Netherlands', code: '599' },
        { label: 'Cayman Islands', code: '1' }, { label: 'Central African Republic', code: '236' }, { label: 'Chad', code: '235' },
        { label: 'Chile', code: '56' }, { label: 'China', code: '86' }, { label: 'Christmas Island', code: '61' },
        { label: 'Cocos (Keeling) Islands', code: '61' }, { label: 'Colombia', code: '57' }, { label: 'Comoros', code: '269' },
        { label: 'Congo - Brazzaville', code: '242' }, { label: 'Congo - Kinshasa', code: '243' }, { label: 'Cook Islands', code: '682' },
        { label: 'Costa Rica', code: '506' }, { label: 'Côte d’Ivoire', code: '225' }, { label: 'Croatia', code: '385' },
        { label: 'Cuba', code: '53' }, { label: 'Curaçao', code: '599' }, { label: 'Cyprus', code: '357' },
        { label: 'Czech Republic', code: '420' }, { label: 'Denmark', code: '45' }, { label: 'Djibouti', code: '253' },
        { label: 'Dominica', code: '1' }, { label: 'Dominican Republic', code: '1' }, { label: 'Ecuador', code: '593' },
        { label: 'Egypt', code: '20' }, { label: 'El Salvador', code: '503' }, { label: 'Equatorial Guinea', code: '240' },
        { label: 'Eritrea', code: '291' }, { label: 'Estonia', code: '372' }, { label: 'Eswatini', code: '268' },
        { label: 'Ethiopia', code: '251' }, { label: 'Falkland Islands', code: '500' }, { label: 'Faroe Islands', code: '298' },
        { label: 'Fiji', code: '679' }, { label: 'Finland', code: '358' }, { label: 'France', code: '33' },
        { label: 'French Guiana', code: '594' }, { label: 'French Polynesia', code: '689' }, { label: 'Gabon', code: '241' },
        { label: 'Gambia', code: '220' }, { label: 'Georgia', code: '995' }, { label: 'Germany', code: '49' },
        { label: 'Ghana', code: '233' }, { label: 'Gibraltar', code: '350' }, { label: 'Greece', code: '30' },
        { label: 'Greenland', code: '299' }, { label: 'Grenada', code: '1' }, { label: 'Guadeloupe', code: '590' },
        { label: 'Guam', code: '1' }, { label: 'Guatemala', code: '502' }, { label: 'Guernsey', code: '44' },
        { label: 'Guinea', code: '224' }, { label: 'Guinea-Bissau', code: '245' }, { label: 'Guyana', code: '592' },
        { label: 'Haiti', code: '509' }, { label: 'Honduras', code: '504' }, { label: 'Hong Kong', code: '852' },
        { label: 'Hungary', code: '36' }, { label: 'Iceland', code: '354' }, { label: 'India', code: '91' },
        { label: 'Indonesia', code: '62' }, { label: 'Iran', code: '98' }, { label: 'Iraq', code: '964' },
        { label: 'Ireland', code: '353' }, { label: 'Isle of Man', code: '44' }, { label: 'Israel', code: '972' },
        { label: 'Italy', code: '39' }, { label: 'Jamaica', code: '1' }, { label: 'Japan', code: '81' },
        { label: 'Jersey', code: '44' }, { label: 'Jordan', code: '962' }, { label: 'Kazakhstan', code: '7' },
        { label: 'Kenya', code: '254' }, { label: 'Kiribati', code: '686' }, { label: 'Kosovo', code: '383' },
        { label: 'Kuwait', code: '965' }, { label: 'Kyrgyzstan', code: '996' }, { label: 'Laos', code: '856' },
        { label: 'Latvia', code: '371' }, { label: 'Lebanon', code: '961' }, { label: 'Lesotho', code: '266' },
        { label: 'Liberia', code: '231' }, { label: 'Libya', code: '218' }, { label: 'Liechtenstein', code: '423' },
        { label: 'Lithuania', code: '370' }, { label: 'Luxembourg', code: '352' }, { label: 'Macau', code: '853' },
        { label: 'Madagascar', code: '261' }, { label: 'Malawi', code: '265' }, { label: 'Malaysia', code: '60' },
        { label: 'Maldives', code: '960' }, { label: 'Mali', code: '223' }, { label: 'Malta', code: '356' },
        { label: 'Marshall Islands', code: '692' }, { label: 'Martinique', code: '596' }, { label: 'Mauritania', code: '222' },
        { label: 'Mauritius', code: '230' }, { label: 'Mayotte', code: '262' }, { label: 'Mexico', code: '52' },
        { label: 'Micronesia', code: '691' }, { label: 'Moldova', code: '373' }, { label: 'Monaco', code: '377' },
        { label: 'Mongolia', code: '976' }, { label: 'Montenegro', code: '382' }, { label: 'Montserrat', code: '1' },
        { label: 'Morocco', code: '212' }, { label: 'Mozambique', code: '258' }, { label: 'Myanmar (Burma)', code: '95' },
        { label: 'Namibia', code: '264' }, { label: 'Nauru', code: '674' }, { label: 'Nepal', code: '977' },
        { label: 'Netherlands', code: '31' }, { label: 'New Caledonia', code: '687' }, { label: 'New Zealand', code: '64' },
        { label: 'Nicaragua', code: '505' }, { label: 'Niger', code: '227' }, { label: 'Nigeria', code: '234' },
        { label: 'Niue', code: '683' }, { label: 'Norfolk Island', code: '672' }, { label: 'North Korea', code: '850' },
        { label: 'North Macedonia', code: '389' }, { label: 'Northern Mariana Islands', code: '1' }, { label: 'Norway', code: '47' },
        { label: 'Oman', code: '968' }, { label: 'Pakistan', code: '92' }, { label: 'Palau', code: '680' },
        { label: 'Palestine', code: '970' }, { label: 'Panama', code: '507' }, { label: 'Papua New Guinea', code: '675' },
        { label: 'Paraguay', code: '595' }, { label: 'Peru', code: '51' }, { label: 'Philippines', code: '63' },
        { label: 'Poland', code: '48' }, { label: 'Portugal', code: '351' }, { label: 'Puerto Rico', code: '1' },
        { label: 'Qatar', code: '974' }, { label: 'Réunion', code: '262' }, { label: 'Romania', code: '40' },
        { label: 'Russia', code: '7' }, { label: 'Rwanda', code: '250' }, { label: 'Samoa', code: '685' },
        { label: 'San Marino', code: '378' }, { label: 'São Tomé & Príncipe', code: '239' }, { label: 'Saudi Arabia', code: '966' },
        { label: 'Senegal', code: '221' }, { label: 'Serbia', code: '381' }, { label: 'Seychelles', code: '248' },
        { label: 'Sierra Leone', code: '232' }, { label: 'Singapore', code: '65' }, { label: 'Sint Maarten', code: '1' },
        { label: 'Slovakia', code: '421' }, { label: 'Slovenia', code: '386' }, { label: 'Solomon Islands', code: '677' },
        { label: 'Somalia', code: '252' }, { label: 'South Africa', code: '27' }, { label: 'South Korea', code: '82' },
        { label: 'South Sudan', code: '211' }, { label: 'Spain', code: '34' }, { label: 'Sri Lanka', code: '94' },
        { label: 'St Barthélemy', code: '590' }, { label: 'St Helena', code: '290' }, { label: 'St Kitts & Nevis', code: '1' },
        { label: 'St Lucia', code: '1' }, { label: 'St Martin', code: '590' }, { label: 'St Pierre & Miquelon', code: '508' },
        { label: 'St Vincent & Grenadines', code: '1' }, { label: 'Sudan', code: '249' }, { label: 'Suriname', code: '597' },
        { label: 'Svalbard & Jan Mayen', code: '47' }, { label: 'Sweden', code: '46' }, { label: 'Switzerland', code: '41' },
        { label: 'Syria', code: '963' }, { label: 'Taiwan', code: '886' }, { label: 'Tajikistan', code: '992' },
        { label: 'Tanzania', code: '255' }, { label: 'Thailand', code: '66' }, { label: 'Timor-Leste', code: '670' },
        { label: 'Togo', code: '228' }, { label: 'Tokelau', code: '690' }, { label: 'Tonga', code: '676' },
        { label: 'Trinidad & Tobago', code: '1' }, { label: 'Tunisia', code: '216' }, { label: 'Turkey', code: '90' },
        { label: 'Turkmenistan', code: '993' }, { label: 'Turks & Caicos Islands', code: '1' }, { label: 'Tuvalu', code: '688' },
        { label: 'Uganda', code: '256' }, { label: 'Ukraine', code: '380' }, { label: 'United Arab Emirates', code: '971' },
        { label: 'Uruguay', code: '598' }, { label: 'US Virgin Islands', code: '1' }, { label: 'Uzbekistan', code: '998' },
        { label: 'Vanuatu', code: '678' }, { label: 'Vatican City', code: '39' }, { label: 'Venezuela', code: '58' },
        { label: 'Vietnam', code: '84' }, { label: 'Wallis & Futuna', code: '681' }, { label: 'Western Sahara', code: '212' },
        { label: 'Yemen', code: '967' }, { label: 'Zambia', code: '260' }, { label: 'Zimbabwe', code: '263' },
        { label: 'Åland Islands', code: '358' }
    ];

    // Mantener un unico registro para codigo +1: solo United States
    countries = countries.filter(function(c){
        if (c.code === '1') {
            return c.label === 'United States';
        }
        return true;
    });

    function uniqueCodesDesc() {
        var map = {};
        countries.forEach(function(c){ map[c.code] = true; });
        return Object.keys(map).sort(function(a,b){ return b.length - a.length; });
    }

    function detectCountryCodeFromPhone(phone) {
        if (!phone || phone.charAt(0) !== '+') return null;
        var digits = phone.replace(/\D+/g, '');
        if (!digits) return null;
        var codes = uniqueCodesDesc();
        for (var i = 0; i < codes.length; i++) {
            if (digits.indexOf(codes[i]) === 0) return codes[i];
        }
        return null;
    }

    function stripPrefixFromPhone(phone, countryCode) {
        if (!phone) return '';
        var digits = phone.replace(/\D+/g, '');
        if (!countryCode || digits.indexOf(countryCode) !== 0) return phone;
        return digits.slice(countryCode.length);
    }

    function buildCountrySelect(selectedCode) {
        var html = '<select id="billing_phone_country_code" name="billing_phone_country_code" class="input-text">';
        countries.forEach(function(c){
            var selected = (c.code === selectedCode) ? ' selected="selected"' : '';
            html += '<option value="' + c.code + '"' + selected + '>' + c.label + ' (+' + c.code + ')</option>';
        });
        html += '</select>';
        return html;
    }

    function syncPhoneToInternational() {
        var $phone = $('#billing_phone');
        var $country = $('#billing_phone_country_code');
        if (!$phone.length || !$country.length) return;
        var code = ($country.val() || '').replace(/\D+/g, '');
        var local = ($phone.val() || '').replace(/\D+/g, '');

        // Si el usuario escribe el numero con prefijo internacional (ej: +1 (209)...),
        // quitar una sola vez el codigo pais para evitar duplicarlo.
        if (code && local.indexOf(code) === 0 && local.length > (code.length + 6)) {
            local = local.slice(code.length);
        }

        if (code && local) {
            $phone.val('+' + code + ' ' + local);
        }
    }

    function initPhoneCountrySelect() {
        var $phone = $('#billing_phone');
        if (!$phone.length || $phone.data('envioPhoneInit') === 1) return;

        var currentVal = ($phone.val() || '').trim();
        var detectedCode = detectCountryCodeFromPhone(currentVal) || '507';
        var localNumber = stripPrefixFromPhone(currentVal, detectedCode);

        var $wrapper = $('<div class="envio-fee-phone-row" />');
        $phone.before($wrapper);
        $wrapper.append(buildCountrySelect(detectedCode));
        $wrapper.append($phone);

        $phone.val(localNumber);
        $phone.data('envioPhoneInit', 1);

        if (!$(document).data('envioPhoneFieldBind')) {
            // Al cambiar país o salir del input, persistir el formato internacional
            $(document).on('change', '#billing_phone_country_code', function(){
                syncPhoneToInternational();
            });
            $(document).on('blur', '#billing_phone', function(){
                syncPhoneToInternational();
            });
            // Compatibilidad con botones "Guardar los cambios" de checkout por pasos
            $(document).on('click', 'button, input[type="submit"]', function(){
                var text = '';
                if (typeof $(this).text === 'function') {
                    text = ($(this).text() || '').toLowerCase();
                }
                var val = (($(this).val && $(this).val()) || '').toLowerCase();
                if (text.indexOf('guardar') !== -1 || val.indexOf('guardar') !== -1) {
                    syncPhoneToInternational();
                }
            });

            // Fluent Checkout y otros checkouts AJAX: sincronizar antes de cualquier request
            $(document).ajaxSend(function(){
                syncPhoneToInternational();
            });

            // Si Fluent re-renderiza bloques sin updated_checkout, volver a inyectar el select
            var moTarget = document.body;
            if (moTarget && typeof MutationObserver !== 'undefined') {
                var observer = new MutationObserver(function(){
                    initPhoneCountrySelect();
                });
                observer.observe(moTarget, { childList: true, subtree: true });
            }
            $(document).data('envioPhoneFieldBind', 1);
        }

        var $form = $('form.checkout');
        if ($form.length && !$form.data('envioPhoneSubmitBind')) {
            $form.on('submit checkout_place_order', function() {
                syncPhoneToInternational();
            });
            $form.data('envioPhoneSubmitBind', 1);
        }
    }

    $(document).ready(function(){
        initPhoneCountrySelect();
    });

    $(document.body).on('updated_checkout', function(){
        setTimeout(function(){
            initPhoneCountrySelect();
        }, 100);
    });
})(jQuery);
JS;

        $phone_inline_css = <<<'CSS'
.envio-fee-phone-row {
    display: flex;
    gap: 8px;
}
#billing_phone_country_code {
    flex: 0 0 220px;
    max-width: 220px;
}
.envio-fee-phone-row #billing_phone {
    flex: 1 1 auto;
}
CSS;

        wp_add_inline_style('jquery-ui-datepicker', $phone_inline_css);
        wp_add_inline_script('jquery-ui-datepicker', $phone_inline_js);
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

// Admin page HTML con pestañas y changelog externo
function envio_fee_settings_page() {
    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'settings';

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
        
        // Guardar opción de permitir pedidos del mismo día
        $permitir_mismo_dia = !empty($_POST['envio_fee_permitir_mismo_dia']) ? 1 : 0;
        update_option('envio_fee_permitir_mismo_dia', $permitir_mismo_dia);
        
        echo '<div class="updated"><p>'.__('Configuración guardada correctamente.', 'envio-fee').'</p></div>';
    }

    $zones = envio_fee_get_zones();
    ?>
    <div class="wrap">
        <h1><?php _e('Envío Personalizado', 'envio-fee'); ?></h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="?page=envio-fee-settings&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">
                <?php _e('Configuración', 'envio-fee'); ?>
            </a>
            <a href="?page=envio-fee-settings&tab=changelog" class="nav-tab <?php echo $active_tab == 'changelog' ? 'nav-tab-active' : ''; ?>">
                <?php _e('Changelog', 'envio-fee'); ?>
            </a>
        </h2>
        
        <?php if ($active_tab == 'settings'): ?>
            <?php $permitir_mismo_dia = get_option('envio_fee_permitir_mismo_dia', 0); ?>
            <h2><?php _e('Configuración General', 'envio-fee'); ?></h2>
            <form method="post">
                <?php wp_nonce_field('envio_fee_save_action', 'envio_fee_nonce'); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="envio_fee_permitir_mismo_dia"><?php _e('Permitir pedidos del mismo día', 'envio-fee'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="envio_fee_permitir_mismo_dia" id="envio_fee_permitir_mismo_dia" value="1" <?php checked($permitir_mismo_dia, 1); ?>>
                                    <?php _e('Permitir que los clientes seleccionen el día actual como fecha de envío', 'envio-fee'); ?>
                                </label>
                                <p class="description"><?php _e('Si está desactivado, los clientes solo podrán seleccionar fechas a partir de mañana.', 'envio-fee'); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <h2><?php _e('Configuración de Zonas de Envío', 'envio-fee'); ?></h2>
                <style>
                    .envio-fee-zones-table th:nth-child(3),
                    .envio-fee-zones-table td:nth-child(3) { text-align: center; }
                    .envio-fee-zones-table td:nth-child(3) input { text-align: center; }
                </style>
                <table class="widefat envio-fee-zones-table">
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
        <?php else: ?>
            <?php
            // Leer el archivo CHANGELOG.md
            $changelog_path = plugin_dir_path(__FILE__) . 'CHANGELOG.md';
            $changelog = '';
            if (file_exists($changelog_path)) {
                $changelog = file_get_contents($changelog_path);
                // Convertir markdown básico a HTML
                $changelog = nl2br(esc_html($changelog));
            }
            ?>
            <div class="changelog-container" style="max-width: 900px; margin-top: 20px;">
                <h2><?php _e('Historial de Cambios', 'envio-fee'); ?></h2>
                <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); line-height: 1.6;">
                    <?php if ($changelog): ?>
                        <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;">
                            <?php echo $changelog; ?>
                        </div>
                    <?php else: ?>
                        <p><?php _e('No se encontró el archivo CHANGELOG.md en la carpeta del plugin.', 'envio-fee'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

// Checkout fields
add_action('woocommerce_review_order_after_payment', function(){
    $permitir_mismo_dia = get_option('envio_fee_permitir_mismo_dia', 0);
    $hoy = date('Y-m-d');
    $manana = date('Y-m-d', strtotime('+1 day'));
    $fecha_minima = $permitir_mismo_dia ? $hoy : $manana;
    $fecha_default = $permitir_mismo_dia ? $hoy : $manana;
    
    // Formatear fecha default a dd/mm/yyyy
    $fecha_default_formateada = date('d/m/Y', strtotime($fecha_default));
    $fecha_minima_formateada = date('d/m/Y', strtotime($fecha_minima));
    
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
            #fecha_envio_custom {
                max-width: 150px;
                cursor: pointer;
            }
            #fecha_envio_custom.error {
                border-color: #dc3232;
            }
            .ui-datepicker {
                z-index: 9999 !important;
            }
        </style>
        <p class="form-row form-row-wide validate-required">
            <label for="fecha_envio_custom" class="">
                <span class="dashicons dashicons-calendar-alt"></span>
                <?php _e('Fecha de envío', 'envio-fee'); ?>
                <abbr class="required" title="required">*</abbr>
            </label>
            <input type="text" id="fecha_envio_custom" name="fecha_envio_custom" class="input-text update_totals_on_change" 
                   placeholder="dd/mm/yyyy" value="<?php echo esc_attr($fecha_default_formateada); ?>" 
                   pattern="\d{2}/\d{2}/\d{4}" required aria-required="true" 
                   data-min-date="<?php echo esc_attr($fecha_minima); ?>" 
                   data-permitir-mismo-dia="<?php echo esc_attr($permitir_mismo_dia); ?>">
            <input type="hidden" id="fecha_envio_custom_iso" name="fecha_envio_custom_iso" value="<?php echo esc_attr($fecha_default); ?>">
        </p>
        <p class="form-row form-row-wide validate-required">
            <label for="horario_envio_custom" class="">
                <span class="dashicons dashicons-clock"></span>
                <?php _e('Horario de envío o retiro', 'envio-fee'); ?>
                <abbr class="required" title="required">*</abbr>
            </label>
            <select id="horario_envio_custom" name="horario_envio_custom" class="select update_totals_on_change" required aria-required="true">
                <option value=""><?php _e('Selecciona un horario…', 'envio-fee'); ?></option>
                <option value="9am - 12 pm"><?php _e('9am - 12 pm', 'envio-fee'); ?></option>
                <option value="1pm - 4 pm"><?php _e('1pm - 4 pm', 'envio-fee'); ?></option>
            </select>
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
            // Función para convertir dd/mm/yyyy a yyyy-mm-dd
            function convertirFechaDDMMYYYY(fechaStr) {
                var partes = fechaStr.split('/');
                if (partes.length !== 3) return null;
                var dia = parseInt(partes[0], 10);
                var mes = parseInt(partes[1], 10);
                var ano = parseInt(partes[2], 10);
                
                // Validar que sean números válidos
                if (isNaN(dia) || isNaN(mes) || isNaN(ano)) return null;
                if (dia < 1 || dia > 31 || mes < 1 || mes > 12 || ano < 2000) return null;
                
                // Crear fecha y validar
                var fecha = new Date(ano, mes - 1, dia);
                if (fecha.getDate() !== dia || fecha.getMonth() !== (mes - 1) || fecha.getFullYear() !== ano) {
                    return null; // Fecha inválida
                }
                
                // Formatear a yyyy-mm-dd
                var yyyy = fecha.getFullYear();
                var mm = String(fecha.getMonth() + 1).padStart(2, '0');
                var dd = String(fecha.getDate()).padStart(2, '0');
                return yyyy + '-' + mm + '-' + dd;
            }
            
            // Función para validar fecha
            function validarFecha(fechaStr, permitirMismoDia) {
                var fechaISO = convertirFechaDDMMYYYY(fechaStr);
                if (!fechaISO) return false;
                
                var hoy = new Date();
                hoy.setHours(0, 0, 0, 0);
                var fechaSeleccionada = new Date(fechaISO);
                fechaSeleccionada.setHours(0, 0, 0, 0);
                
                if (permitirMismoDia) {
                    return fechaSeleccionada >= hoy;
                } else {
                    var manana = new Date(hoy);
                    manana.setDate(manana.getDate() + 1);
                    return fechaSeleccionada >= manana;
                }
            }
            
            // Variable global para permitir mismo día
            var permitirMismoDia = $('#fecha_envio_custom').data('permitir-mismo-dia') == 1;
            
            // Función para inicializar datepicker
            function inicializarDatepicker() {
                var $fechaInput = $('#fecha_envio_custom');
                if ($fechaInput.length === 0) {
                    return;
                }
                
                // Verificar que jQuery UI datepicker esté disponible
                if (typeof $.fn.datepicker === 'undefined') {
                    console.log('jQuery UI Datepicker no está disponible, reintentando...');
                    // Reintentar después de un breve delay
                    setTimeout(inicializarDatepicker, 500);
                    return;
                }
                
                // Si ya está inicializado, no hacer nada
                if ($fechaInput.hasClass('hasDatepicker')) {
                    return;
                }
                
                var fechaMinima = $fechaInput.data('min-date');
                
                if (!fechaMinima) {
                    console.log('Fecha mínima no encontrada');
                    return;
                }
                
                // Convertir fecha mínima a objeto Date
                var fechaMinimaParts = fechaMinima.split('-');
                var fechaMinimaDate = new Date(parseInt(fechaMinimaParts[0]), parseInt(fechaMinimaParts[1]) - 1, parseInt(fechaMinimaParts[2]));
                
                // Actualizar variable permitirMismoDia
                permitirMismoDia = $fechaInput.data('permitir-mismo-dia') == 1;
                
                // Inicializar datepicker
                $fechaInput.datepicker({
                    dateFormat: 'dd/mm/yy',
                    minDate: fechaMinimaDate,
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    onSelect: function(dateText, inst) {
                        var fechaISO = convertirFechaDDMMYYYY(dateText);
                        if (fechaISO && validarFecha(dateText, permitirMismoDia)) {
                            $('#fecha_envio_custom_iso').val(fechaISO);
                            $(this).removeClass('error');
                            $(this)[0].setCustomValidity('');
                            pingTotals();
                        } else {
                            $(this).addClass('error');
                            var mensaje = permitirMismoDia 
                                ? '<?php _e('La fecha debe ser hoy o una fecha futura', 'envio-fee'); ?>'
                                : '<?php _e('La fecha debe ser a partir de mañana', 'envio-fee'); ?>';
                            $(this)[0].setCustomValidity(mensaje);
                        }
                    }
                });
            }
            
            // Manejar input de fecha manual
            $('#fecha_envio_custom').on('input blur', function(){
                var $input = $(this);
                var fechaStr = $input.val().trim();
                
                // Formatear automáticamente mientras escribe
                if (fechaStr.length > 0 && fechaStr.length < 10) {
                    // Remover caracteres no numéricos excepto /
                    fechaStr = fechaStr.replace(/[^\d/]/g, '');
                    // Agregar / automáticamente
                    if (fechaStr.length === 2 && !fechaStr.includes('/')) {
                        fechaStr = fechaStr + '/';
                    } else if (fechaStr.length === 5 && fechaStr.split('/').length === 2) {
                        fechaStr = fechaStr + '/';
                    }
                    $input.val(fechaStr);
                }
                
                // Validar cuando tiene formato completo
                if (fechaStr.length === 10) {
                    var fechaISO = convertirFechaDDMMYYYY(fechaStr);
                    if (fechaISO && validarFecha(fechaStr, permitirMismoDia)) {
                        $('#fecha_envio_custom_iso').val(fechaISO);
                        $input.removeClass('error');
                        $input[0].setCustomValidity('');
                        pingTotals();
                    } else {
                        $input.addClass('error');
                        if (!fechaISO) {
                            $input[0].setCustomValidity('<?php _e('Formato de fecha inválido. Use dd/mm/yyyy', 'envio-fee'); ?>');
                        } else {
                            var mensaje = permitirMismoDia 
                                ? '<?php _e('La fecha debe ser hoy o una fecha futura', 'envio-fee'); ?>'
                                : '<?php _e('La fecha debe ser a partir de mañana', 'envio-fee'); ?>';
                            $input[0].setCustomValidity(mensaje);
                        }
                    }
                } else if (fechaStr.length > 0) {
                    $input[0].setCustomValidity('<?php _e('Formato incompleto. Use dd/mm/yyyy', 'envio-fee'); ?>');
                } else {
                    $input[0].setCustomValidity('');
                }
            });
            
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
            $(document).on('change input', '#custom_shipping_type, #custom_shipping_zone, #direccion_delivery_custom, #fecha_envio_custom, #horario_envio_custom', function(){
                toggleDeliveryFields();
                pingTotals();
            });
            $(document).ready(function(){
                toggleDeliveryFields();
                
                // Inicializar datepicker después de que todo esté listo
                inicializarDatepicker();
                
                // Validar fecha inicial
                $('#fecha_envio_custom').trigger('blur');
                pingTotals();
            });
            
            // También intentar inicializar cuando se actualiza el checkout (por si se carga dinámicamente)
            $(document.body).on('updated_checkout', function(){
                setTimeout(function(){
                    if ($('#fecha_envio_custom').length > 0 && !$('#fecha_envio_custom').hasClass('hasDatepicker')) {
                        inicializarDatepicker();
                    }
                }, 100);
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

// Validation (función reutilizable por distintos hooks)
function envio_fee_validate_checkout_fields() {
    $tipo = sanitize_text_field($_POST['custom_shipping_type'] ?? '');

    // Validar tipo de envío (obligatorio siempre)
    if (empty($tipo)) {
        wc_add_notice(__('Por favor selecciona el tipo de envío.', 'envio-fee'), 'error');
    }

    // Validar fecha de envío (obligatoria siempre)
    $fecha_iso = !empty($_POST['fecha_envio_custom_iso']) ? sanitize_text_field($_POST['fecha_envio_custom_iso']) : '';
    $fecha_input = !empty($_POST['fecha_envio_custom']) ? sanitize_text_field($_POST['fecha_envio_custom']) : '';
    
    if (empty($fecha_iso) && empty($fecha_input)) {
        wc_add_notice(__('Por favor selecciona la fecha de envío.', 'envio-fee'), 'error');
    } else {
        // Usar fecha ISO si está disponible, sino convertir desde dd/mm/yyyy
        if (!empty($fecha_iso)) {
            $fecha = $fecha_iso;
        } else {
            // Convertir dd/mm/yyyy a yyyy-mm-dd
            $partes = explode('/', $fecha_input);
            if (count($partes) === 3) {
                $fecha = sprintf('%04d-%02d-%02d', $partes[2], $partes[1], $partes[0]);
            } else {
                wc_add_notice(__('Formato de fecha inválido. Use dd/mm/yyyy', 'envio-fee'), 'error');
                return;
            }
        }
        
        // Validar formato de fecha
        $timestamp = strtotime($fecha);
        if ($timestamp === false) {
            wc_add_notice(__('Fecha de envío inválida.', 'envio-fee'), 'error');
            return;
        }
        
        // Validar según configuración
        $permitir_mismo_dia = get_option('envio_fee_permitir_mismo_dia', 0);
        $hoy = date('Y-m-d');
        $manana = date('Y-m-d', strtotime('+1 day'));
        
        if ($permitir_mismo_dia) {
            if ($fecha < $hoy) {
                wc_add_notice(__('La fecha de envío no puede ser anterior a hoy.', 'envio-fee'), 'error');
            }
        } else {
            if ($fecha <= $hoy) {
                wc_add_notice(__('La fecha de envío debe ser a partir de mañana. No se puede seleccionar el día actual.', 'envio-fee'), 'error');
            }
        }
    }

    // Validar horario de envío o retiro (obligatorio siempre)
    if (empty($_POST['horario_envio_custom'])) {
        wc_add_notice(__('Por favor selecciona el horario de envío o retiro.', 'envio-fee'), 'error');
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
}

// Ejecutar validación en los puntos estándar del checkout
add_action('woocommerce_checkout_process', 'envio_fee_validate_checkout_fields', 10);
add_action('woocommerce_after_checkout_validation', function( $data, $errors ) {
    // Solo añadir errores si aún no existen errores fatales previos
    if ( is_object( $errors ) && count( $errors->get_error_messages() ) === 0 ) {
        envio_fee_validate_checkout_fields();
    }
}, 10, 2);

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

    // Usar fecha ISO si está disponible, sino convertir desde dd/mm/yyyy
    if (!empty($_POST['fecha_envio_custom_iso'])) {
        $fecha_envio = sanitize_text_field($_POST['fecha_envio_custom_iso']);
    } elseif (!empty($_POST['fecha_envio_custom'])) {
        $fecha_input = sanitize_text_field($_POST['fecha_envio_custom']);
        $partes = explode('/', $fecha_input);
        if (count($partes) === 3) {
            $fecha_envio = sprintf('%04d-%02d-%02d', $partes[2], $partes[1], $partes[0]);
        } else {
            $fecha_envio = '';
        }
    } else {
        $fecha_envio = '';
    }
    $horario_envio = sanitize_text_field($_POST['horario_envio_custom'] ?? '');
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
    
    // Construir texto con fecha y horario
    $partes_direccion_2 = array();
    if (!empty($fecha_formateada)) {
        $partes_direccion_2[] = $fecha_formateada;
    }
    if (!empty($horario_envio)) {
        $partes_direccion_2[] = sprintf(__('Horario: %s', 'envio-fee'), $horario_envio);
    }
    $texto_fecha_horario = implode(' | ', $partes_direccion_2);
    
    // Agregar fecha y horario a dirección 2 (combinar con dirección 2 de facturación si existe)
    if (!empty($texto_fecha_horario)) {
        $direccion_2_actual = $order->get_shipping_address_2();
        if (!empty($direccion_2_actual)) {
            // Si ya hay dirección 2, combinar con fecha y horario
            $order->set_shipping_address_2($direccion_2_actual . ' | ' . $texto_fecha_horario);
        } else {
            // Si no hay dirección 2, solo poner fecha y horario
            $order->set_shipping_address_2($texto_fecha_horario);
        }
    }
    
    // Sobrescribir dirección 1 según el tipo de envío
    if ($tipo_envio === 'delivery' && !empty($direccion_delivery)) {
        $order->set_shipping_address_1($direccion_delivery);
    } elseif ($tipo_envio === 'retiro') {
        $order->set_shipping_address_1('Retiro en tienda');
    }
    
    // Sincronizar facturación con envío (dirección oculta en checkout)
    $order->set_billing_address_1($order->get_shipping_address_1());
    $order->set_billing_address_2($order->get_shipping_address_2());
    
    // Mantener meta data para compatibilidad
    $order->update_meta_data('_fecha_envio_custom', $fecha_envio);
    $order->update_meta_data('_horario_envio_custom', $horario_envio);
    $order->update_meta_data('_custom_shipping_type', $tipo_envio);
    $order->update_meta_data('_custom_shipping_zone', sanitize_text_field($_POST['custom_shipping_zone'] ?? ''));
    $order->update_meta_data('_direccion_delivery_custom', $direccion_delivery);
}, 10, 2);
