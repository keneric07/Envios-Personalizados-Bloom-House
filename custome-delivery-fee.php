<?php
/**
 * Plugin Name: Delivery Express - Envíos Programados y Personalizados
 * Description: Permite a tus clientes elegir fecha y horario de entrega, configurar zonas de envío con precios personalizados, y gestionar retiros en tienda. Mejora la experiencia de compra con entregas a medida.
 * Version: 3.17.8
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

// Enqueue Dashicons, jQuery UI Datepicker y JS para selector manual de teléfono en frontend
add_action('wp_enqueue_scripts', function(){
    if (is_checkout()) {
        wp_enqueue_style('dashicons');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style(
            'jquery-ui-datepicker',
            'https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css',
            array(),
            '1.13.2'
        );

        // JS inline para crear un selector manual de país/código telefónico junto al campo de teléfono
        $inline_js = <<<'JS'
(function($){
    function buildPhoneCountrySelectHtml(selectedCode) {
        // Lista manual de países y códigos
        var options = [
            { code: '1', label: 'United States (+1)' },
            { code: '44', label: 'United Kingdom (+44)' },
            { code: '93', label: 'Afghanistan (+93)' },
            { code: '355', label: 'Albania (+355)' },
            { code: '213', label: 'Algeria (+213)' },
            { code: '1', label: 'American Samoa (+1)' },
            { code: '376', label: 'Andorra (+376)' },
            { code: '244', label: 'Angola (+244)' },
            { code: '1', label: 'Anguilla (+1)' },
            { code: '1', label: 'Antigua & Barbuda (+1)' },
            { code: '54', label: 'Argentina (+54)' },
            { code: '374', label: 'Armenia (+374)' },
            { code: '297', label: 'Aruba (+297)' },
            { code: '247', label: 'Ascension Island (+247)' },
            { code: '61', label: 'Australia (+61)' },
            { code: '43', label: 'Austria (+43)' },
            { code: '994', label: 'Azerbaijan (+994)' },
            { code: '1', label: 'Bahamas (+1)' },
            { code: '973', label: 'Bahrain (+973)' },
            { code: '880', label: 'Bangladesh (+880)' },
            { code: '1', label: 'Barbados (+1)' },
            { code: '375', label: 'Belarus (+375)' },
            { code: '32', label: 'Belgium (+32)' },
            { code: '501', label: 'Belize (+501)' },
            { code: '229', label: 'Benin (+229)' },
            { code: '1', label: 'Bermuda (+1)' },
            { code: '975', label: 'Bhutan (+975)' },
            { code: '591', label: 'Bolivia (+591)' },
            { code: '387', label: 'Bosnia & Herzegovina (+387)' },
            { code: '267', label: 'Botswana (+267)' },
            { code: '55', label: 'Brazil (+55)' },
            { code: '246', label: 'British Indian Ocean Territory (+246)' },
            { code: '1', label: 'British Virgin Islands (+1)' },
            { code: '673', label: 'Brunei (+673)' },
            { code: '359', label: 'Bulgaria (+359)' },
            { code: '226', label: 'Burkina Faso (+226)' },
            { code: '257', label: 'Burundi (+257)' },
            { code: '855', label: 'Cambodia (+855)' },
            { code: '237', label: 'Cameroon (+237)' },
            { code: '1', label: 'Canada (+1)' },
            { code: '238', label: 'Cape Verde (+238)' },
            { code: '599', label: 'Caribbean Netherlands (+599)' },
            { code: '1', label: 'Cayman Islands (+1)' },
            { code: '236', label: 'Central African Republic (+236)' },
            { code: '235', label: 'Chad (+235)' },
            { code: '56', label: 'Chile (+56)' },
            { code: '86', label: 'China (+86)' },
            { code: '61', label: 'Christmas Island (+61)' },
            { code: '61', label: 'Cocos (Keeling) Islands (+61)' },
            { code: '57', label: 'Colombia (+57)' },
            { code: '269', label: 'Comoros (+269)' },
            { code: '242', label: 'Congo - Brazzaville (+242)' },
            { code: '243', label: 'Congo - Kinshasa (+243)' },
            { code: '682', label: 'Cook Islands (+682)' },
            { code: '506', label: 'Costa Rica (+506)' },
            { code: '225', label: 'Côte d’Ivoire (+225)' },
            { code: '385', label: 'Croatia (+385)' },
            { code: '53', label: 'Cuba (+53)' },
            { code: '599', label: 'Curaçao (+599)' },
            { code: '357', label: 'Cyprus (+357)' },
            { code: '420', label: 'Czech Republic (+420)' },
            { code: '45', label: 'Denmark (+45)' },
            { code: '253', label: 'Djibouti (+253)' },
            { code: '1', label: 'Dominica (+1)' },
            { code: '1', label: 'Dominican Republic (+1)' },
            { code: '593', label: 'Ecuador (+593)' },
            { code: '20', label: 'Egypt (+20)' },
            { code: '503', label: 'El Salvador (+503)' },
            { code: '240', label: 'Equatorial Guinea (+240)' },
            { code: '291', label: 'Eritrea (+291)' },
            { code: '372', label: 'Estonia (+372)' },
            { code: '268', label: 'Eswatini (+268)' },
            { code: '251', label: 'Ethiopia (+251)' },
            { code: '500', label: 'Falkland Islands (+500)' },
            { code: '298', label: 'Faroe Islands (+298)' },
            { code: '679', label: 'Fiji (+679)' },
            { code: '358', label: 'Finland (+358)' },
            { code: '33', label: 'France (+33)' },
            { code: '594', label: 'French Guiana (+594)' },
            { code: '689', label: 'French Polynesia (+689)' },
            { code: '241', label: 'Gabon (+241)' },
            { code: '220', label: 'Gambia (+220)' },
            { code: '995', label: 'Georgia (+995)' },
            { code: '49', label: 'Germany (+49)' },
            { code: '233', label: 'Ghana (+233)' },
            { code: '350', label: 'Gibraltar (+350)' },
            { code: '30', label: 'Greece (+30)' },
            { code: '299', label: 'Greenland (+299)' },
            { code: '1', label: 'Grenada (+1)' },
            { code: '590', label: 'Guadeloupe (+590)' },
            { code: '1', label: 'Guam (+1)' },
            { code: '502', label: 'Guatemala (+502)' },
            { code: '44', label: 'Guernsey (+44)' },
            { code: '224', label: 'Guinea (+224)' },
            { code: '245', label: 'Guinea-Bissau (+245)' },
            { code: '592', label: 'Guyana (+592)' },
            { code: '509', label: 'Haiti (+509)' },
            { code: '504', label: 'Honduras (+504)' },
            { code: '852', label: 'Hong Kong (+852)' },
            { code: '36', label: 'Hungary (+36)' },
            { code: '354', label: 'Iceland (+354)' },
            { code: '91', label: 'India (+91)' },
            { code: '62', label: 'Indonesia (+62)' },
            { code: '98', label: 'Iran (+98)' },
            { code: '964', label: 'Iraq (+964)' },
            { code: '353', label: 'Ireland (+353)' },
            { code: '44', label: 'Isle of Man (+44)' },
            { code: '972', label: 'Israel (+972)' },
            { code: '39', label: 'Italy (+39)' },
            { code: '1', label: 'Jamaica (+1)' },
            { code: '81', label: 'Japan (+81)' },
            { code: '44', label: 'Jersey (+44)' },
            { code: '962', label: 'Jordan (+962)' },
            { code: '7', label: 'Kazakhstan (+7)' },
            { code: '254', label: 'Kenya (+254)' },
            { code: '686', label: 'Kiribati (+686)' },
            { code: '383', label: 'Kosovo (+383)' },
            { code: '965', label: 'Kuwait (+965)' },
            { code: '996', label: 'Kyrgyzstan (+996)' },
            { code: '856', label: 'Laos (+856)' },
            { code: '371', label: 'Latvia (+371)' },
            { code: '961', label: 'Lebanon (+961)' },
            { code: '266', label: 'Lesotho (+266)' },
            { code: '231', label: 'Liberia (+231)' },
            { code: '218', label: 'Libya (+218)' },
            { code: '423', label: 'Liechtenstein (+423)' },
            { code: '370', label: 'Lithuania (+370)' },
            { code: '352', label: 'Luxembourg (+352)' },
            { code: '853', label: 'Macau (+853)' },
            { code: '261', label: 'Madagascar (+261)' },
            { code: '265', label: 'Malawi (+265)' },
            { code: '60', label: 'Malaysia (+60)' },
            { code: '960', label: 'Maldives (+960)' },
            { code: '223', label: 'Mali (+223)' },
            { code: '356', label: 'Malta (+356)' },
            { code: '692', label: 'Marshall Islands (+692)' },
            { code: '596', label: 'Martinique (+596)' },
            { code: '222', label: 'Mauritania (+222)' },
            { code: '230', label: 'Mauritius (+230)' },
            { code: '262', label: 'Mayotte (+262)' },
            { code: '52', label: 'Mexico (+52)' },
            { code: '691', label: 'Micronesia (+691)' },
            { code: '373', label: 'Moldova (+373)' },
            { code: '377', label: 'Monaco (+377)' },
            { code: '976', label: 'Mongolia (+976)' },
            { code: '382', label: 'Montenegro (+382)' },
            { code: '1', label: 'Montserrat (+1)' },
            { code: '212', label: 'Morocco (+212)' },
            { code: '258', label: 'Mozambique (+258)' },
            { code: '95', label: 'Myanmar (Burma) (+95)' },
            { code: '264', label: 'Namibia (+264)' },
            { code: '674', label: 'Nauru (+674)' },
            { code: '977', label: 'Nepal (+977)' },
            { code: '31', label: 'Netherlands (+31)' },
            { code: '687', label: 'New Caledonia (+687)' },
            { code: '64', label: 'New Zealand (+64)' },
            { code: '505', label: 'Nicaragua (+505)' },
            { code: '227', label: 'Niger (+227)' },
            { code: '234', label: 'Nigeria (+234)' },
            { code: '683', label: 'Niue (+683)' },
            { code: '672', label: 'Norfolk Island (+672)' },
            { code: '850', label: 'North Korea (+850)' },
            { code: '389', label: 'North Macedonia (+389)' },
            { code: '1', label: 'Northern Mariana Islands (+1)' },
            { code: '47', label: 'Norway (+47)' },
            { code: '968', label: 'Oman (+968)' },
            { code: '92', label: 'Pakistan (+92)' },
            { code: '680', label: 'Palau (+680)' },
            { code: '970', label: 'Palestine (+970)' },
            { code: '507', label: 'Panama (+507)' },
            { code: '675', label: 'Papua New Guinea (+675)' },
            { code: '595', label: 'Paraguay (+595)' },
            { code: '51', label: 'Peru (+51)' },
            { code: '63', label: 'Philippines (+63)' },
            { code: '48', label: 'Poland (+48)' },
            { code: '351', label: 'Portugal (+351)' },
            { code: '1', label: 'Puerto Rico (+1)' },
            { code: '974', label: 'Qatar (+974)' },
            { code: '262', label: 'Réunion (+262)' },
            { code: '40', label: 'Romania (+40)' },
            { code: '7', label: 'Russia (+7)' },
            { code: '250', label: 'Rwanda (+250)' },
            { code: '685', label: 'Samoa (+685)' },
            { code: '378', label: 'San Marino (+378)' },
            { code: '239', label: 'São Tomé & Príncipe (+239)' },
            { code: '966', label: 'Saudi Arabia (+966)' },
            { code: '221', label: 'Senegal (+221)' },
            { code: '381', label: 'Serbia (+381)' },
            { code: '248', label: 'Seychelles (+248)' },
            { code: '232', label: 'Sierra Leone (+232)' },
            { code: '65', label: 'Singapore (+65)' },
            { code: '1', label: 'Sint Maarten (+1)' },
            { code: '421', label: 'Slovakia (+421)' },
            { code: '386', label: 'Slovenia (+386)' },
            { code: '677', label: 'Solomon Islands (+677)' },
            { code: '252', label: 'Somalia (+252)' },
            { code: '27', label: 'South Africa (+27)' },
            { code: '82', label: 'South Korea (+82)' },
            { code: '211', label: 'South Sudan (+211)' },
            { code: '34', label: 'Spain (+34)' },
            { code: '94', label: 'Sri Lanka (+94)' },
            { code: '590', label: 'St Barthélemy (+590)' },
            { code: '290', label: 'St Helena (+290)' },
            { code: '1', label: 'St Kitts & Nevis (+1)' },
            { code: '1', label: 'St Lucia (+1)' },
            { code: '590', label: 'St Martin (+590)' },
            { code: '508', label: 'St Pierre & Miquelon (+508)' },
            { code: '1', label: 'St Vincent & Grenadines (+1)' },
            { code: '249', label: 'Sudan (+249)' },
            { code: '597', label: 'Suriname (+597)' },
            { code: '47', label: 'Svalbard & Jan Mayen (+47)' },
            { code: '46', label: 'Sweden (+46)' },
            { code: '41', label: 'Switzerland (+41)' },
            { code: '963', label: 'Syria (+963)' },
            { code: '886', label: 'Taiwan (+886)' },
            { code: '992', label: 'Tajikistan (+992)' },
            { code: '255', label: 'Tanzania (+255)' },
            { code: '66', label: 'Thailand (+66)' },
            { code: '670', label: 'Timor-Leste (+670)' },
            { code: '228', label: 'Togo (+228)' },
            { code: '690', label: 'Tokelau (+690)' },
            { code: '676', label: 'Tonga (+676)' },
            { code: '1', label: 'Trinidad & Tobago (+1)' },
            { code: '216', label: 'Tunisia (+216)' },
            { code: '90', label: 'Turkey (+90)' },
            { code: '993', label: 'Turkmenistan (+993)' },
            { code: '1', label: 'Turks & Caicos Islands (+1)' },
            { code: '688', label: 'Tuvalu (+688)' },
            { code: '256', label: 'Uganda (+256)' },
            { code: '380', label: 'Ukraine (+380)' },
            { code: '971', label: 'United Arab Emirates (+971)' },
            { code: '44', label: 'United Kingdom (+44)' },
            { code: '1', label: 'United States (+1)' },
            { code: '598', label: 'Uruguay (+598)' },
            { code: '1', label: 'US Virgin Islands (+1)' },
            { code: '998', label: 'Uzbekistan (+998)' },
            { code: '678', label: 'Vanuatu (+678)' },
            { code: '39', label: 'Vatican City (+39)' },
            { code: '58', label: 'Venezuela (+58)' },
            { code: '84', label: 'Vietnam (+84)' },
            { code: '681', label: 'Wallis & Futuna (+681)' },
            { code: '212', label: 'Western Sahara (+212)' },
            { code: '967', label: 'Yemen (+967)' },
            { code: '260', label: 'Zambia (+260)' },
            { code: '263', label: 'Zimbabwe (+263)' },
            { code: '358', label: 'Åland Islands (+358)' }
        ];

        // Encontrar país seleccionado para mostrar en el botón
        var current = options.find(function(opt){ return opt.code === selectedCode; }) || options.find(function(opt){ return opt.code === '507'; });
        var currentLabel = current ? current.label : 'Seleccionar país';
        var currentCode = current ? current.code : '';

        var html = '';
        html += '<div class="envio-fee-phone-dropdown" id="envio_fee_phone_dropdown">';
        html += '  <button type="button" class="envio-fee-phone-toggle">' + currentLabel + '</button>';
        html += '  <div class="envio-fee-phone-menu" style="display:none;">';
        html += '    <input type="text" class="envio-fee-phone-country-search" placeholder="Buscar país..." autocomplete="off" />';
        html += '    <ul class="envio-fee-phone-list">';
        options.forEach(function(opt){
            html += '      <li class="envio-fee-phone-item" data-code="' + opt.code + '">' + opt.label + '</li>';
        });
        html += '    </ul>';
        html += '  </div>';
        html += '</div>';
        // Input oculto que se manda en el formulario
        html += '<input type="hidden" id="billing_phone_country" name="billing_phone_country" value="' + currentCode + '" />';
        return html;
    }

    function initManualPhoneSelector() {
        var input = document.querySelector('#billing_phone');
        if (!input) {
            return;
        }

        // Evitar re-inicializar múltiples veces el mismo input
        if (input.dataset.envioFeePhoneInitialized === '1') {
            return;
        }

        // Crear contenedor para poner el dropdown personalizado y el input uno al lado del otro
        var wrapper = document.createElement('div');
        wrapper.className = 'envio-fee-phone-wrapper';

        // Insertar wrapper antes del input y mover el input dentro
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        // Panamá (+507) como código por defecto
        var defaultCode = '507';
        var dropdownHtml = buildPhoneCountrySelectHtml(defaultCode);

        // Insertar dropdown personalizado antes del input
        wrapper.insertAdjacentHTML('afterbegin', dropdownHtml);

        var dropdown = wrapper.querySelector('.envio-fee-phone-dropdown');
        var toggle = dropdown ? dropdown.querySelector('.envio-fee-phone-toggle') : null;
        var menu = dropdown ? dropdown.querySelector('.envio-fee-phone-menu') : null;
        var search = dropdown ? dropdown.querySelector('.envio-fee-phone-country-search') : null;
        var items = dropdown ? dropdown.querySelectorAll('.envio-fee-phone-item') : [];
        var countryInput = wrapper.querySelector('#billing_phone_country');

        // Crear/obtener campo oculto para el número completo
        var form = input.form || document.querySelector('form.checkout');
        var hidden = document.querySelector('#billing_phone_full');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.id = 'billing_phone_full';
            hidden.name = 'billing_phone_full';
            if (form) {
                form.appendChild(hidden);
            } else if (input.parentNode) {
                input.parentNode.appendChild(hidden);
            }
        }

        function syncFullNumber() {
            var code = countryInput ? countryInput.value : '';
            var number = input.value.trim();

            if (!code && !number) {
                hidden.value = '';
                return;
            }

            if (number) {
                // Formato deseado: +507 64253333
                var cleanNumber = number.replace(/\s+/g, '');
                hidden.value = '+' + code + ' ' + cleanNumber;
            } else {
                hidden.value = '';
            }
        }

        // Gestionar selección de país en el dropdown personalizado
        if (toggle && menu && items.length) {
            toggle.addEventListener('click', function(e){
                e.preventDefault();
                var isOpen = menu.style.display === 'block';
                menu.style.display = isOpen ? 'none' : 'block';
            });

            items.forEach(function(item){
                item.addEventListener('click', function(e){
                    e.preventDefault();
                    var code = item.getAttribute('data-code') || '';
                    var label = item.textContent || item.innerText || '';
                    if (countryInput) {
                        countryInput.value = code;
                    }
                    if (toggle && label) {
                        toggle.textContent = label;
                    }
                    menu.style.display = 'none';
                    syncFullNumber();
                });
            });

            // Cerrar al hacer click fuera
            document.addEventListener('click', function(e){
                if (!dropdown.contains(e.target)) {
                    menu.style.display = 'none';
                }
            });
        }

        // Antes de enviar cualquier submit del formulario de checkout,
        // aseguramos que el campo visible billing_phone contenga el número completo
        if (form) {
            form.addEventListener('submit', function(){
                syncFullNumber();
                if (hidden && hidden.value) {
                    input.value = hidden.value;
                }
            });
        }

        // Sincronizar número al escribir/salir del campo
        input.addEventListener('input', syncFullNumber);
        input.addEventListener('blur', syncFullNumber);

        // Buscador dentro del menú: filtra las opciones por texto
        if (search) {
            search.addEventListener('input', function(){
                var term = search.value.trim().toLowerCase();
                if (!term) {
                    // Mostrar todas las opciones si no hay término
                    items.forEach(function(item){
                        item.style.display = '';
                    });
                    return;
                }
                items.forEach(function(item){
                    var text = (item.textContent || item.innerText || '').toLowerCase();
                    item.style.display = text.indexOf(term) !== -1 ? '' : 'none';
                });
            });
        }

        // Marcar como inicializado y sincronizar valor inicial si existe
        input.dataset.envioFeePhoneInitialized = '1';
        syncFullNumber();
    }

    // Inicializar al cargar el DOM en el checkout
    $(document).ready(function(){
        if ($('form.checkout').length) {
            // Pequeño delay para asegurarnos de que WooCommerce haya renderizado los campos
            setTimeout(initManualPhoneSelector, 200);

            // Asegurar sincronización justo antes de enviar el checkout
            $('form.checkout').on('checkout_place_order', function(){
                initManualPhoneSelector();
            });
        }
    });

    // Re-inicializar cada vez que WooCommerce refresca el checkout vía AJAX
    $(document.body).on('updated_checkout', function(){
        setTimeout(initManualPhoneSelector, 200);
    });
})(jQuery);
JS;

        // Asociar nuestro JS inline al script de datepicker (ya encolado)
        wp_add_inline_script('jquery-ui-datepicker', $inline_js);
    }
});

// Usar el número completo de intl-tel-input (si existe) como teléfono de facturación al guardar el checkout
add_filter('woocommerce_checkout_posted_data', function($data){
    if (!empty($data['billing_phone_full'])) {
        $data['billing_phone'] = wc_clean($data['billing_phone_full']);
    }
    return $data;
}, 10, 1);

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
            /* Wrapper del selector de país + teléfono */
            .envio-fee-phone-wrapper {
                display: flex;
                align-items: stretch;
                gap: 8px;
            }
            .envio-fee-phone-wrapper .input-text {
                flex: 1 1 auto;
            }
            /* Botón estilo <select> */
            .envio-fee-phone-dropdown {
                position: relative;
                flex: 0 0 230px;
                max-width: 230px;
            }
            .envio-fee-phone-toggle {
                width: 100%;
                padding: 6px 32px 6px 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                background-color: #fff;
                font-size: 14px;
                text-align: left;
                cursor: pointer;
                position: relative;
                color: #333;
                box-sizing: border-box;
            }
            .envio-fee-phone-toggle:after {
                content: "";
                position: absolute;
                right: 10px;
                top: 50%;
                margin-top: -2px;
                border-width: 5px 4px 0 4px;
                border-style: solid;
                border-color: #555 transparent transparent transparent;
            }
            /* Menú desplegable */
            .envio-fee-phone-menu {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                z-index: 9999;
                margin-top: 2px;
                background-color: #fff;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.15);
                box-sizing: border-box;
            }
            .envio-fee-phone-country-search {
                width: 100%;
                box-sizing: border-box;
                padding: 6px 8px;
                border: none;
                border-bottom: 1px solid #eee;
                font-size: 13px;
                outline: none;
            }
            .envio-fee-phone-list {
                max-height: 180px;
                overflow-y: auto;
                padding: 0;
                margin: 0;
                list-style: none;
            }
            .envio-fee-phone-item {
                padding: 6px 10px;
                font-size: 13px;
                cursor: pointer;
            }
            .envio-fee-phone-item:hover {
                background-color: #f4f4f4;
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
    
    // Sobrescribir teléfono de facturación con el número completo (+código país + número) si existe
    if (!empty($_POST['billing_phone_full'])) {
        $billing_phone_full = sanitize_text_field(wp_unslash($_POST['billing_phone_full']));
        if (!empty($billing_phone_full)) {
            $order->set_billing_phone($billing_phone_full);
        }
    }
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
