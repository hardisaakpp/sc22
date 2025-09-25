<?php
include_once "header.php";

// Inicializar variables
$almacenes = array();

// Consulta para obtener los almacenes usando PDO (consistente con el resto del proyecto)
try {
    $sqlAlmacenes = "SELECT [cod_almacen], [nombre] FROM [STORECONTROL].[dbo].[Almacen] WHERE fk_emp='MT' AND hit_cod_local > 0 ORDER BY [nombre]";

    // Verificar conexión a la base de datos (usando $db de header.php)
    if (!isset($db) || !$db) {
        throw new Exception("No hay conexión a la base de datos.");
    }

    // Ejecutar la consulta usando PDO
    $stmtAlmacenes = $db->prepare($sqlAlmacenes);
    $stmtAlmacenes->execute();

    // Obtener los resultados
    $almacenes = $stmtAlmacenes->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error en consulta de almacenes: " . $e->getMessage());
}
?>
<div class="content">
    <!---------------------------------------------->
    <!----------------- Content -------------------->
    <!---------------------------------------------->

    <div class="container-fluid">
        <div class="row justify-content-center align-items-center min-vh-75">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-lg border-0 rounded-lg">
                    <!-- Header del Card -->
                    <div class="card-header bg-gradient-primary text-white border-0">
                        <div class="text-center py-2">
                            <div class="display-4 mb-2">
                                <i class="fa fa-cube"></i>
                            </div>
                            <h4 class="card-title mb-0 font-weight-light">Gestión de Artículos</h4>
                            <small class="opacity-75">Verificar productos en el sistema</small>
                        </div>
                    </div>

                    <!-- Body del Card -->
                    <div class="card-body p-5">
                        <form method="POST" action="" id="articuloForm">
                            <!-- Dropdown Almacén -->
                            <div class="form-group mb-4">
                                <label for="almacen" class="form-label text-muted font-weight-bold mb-2">
                                    <i class="fa fa-building mr-2 text-primary"></i>Almacén
                                </label>
                                <select class="form-control form-control-lg custom-select" id="almacen" name="almacen" required>
                                    <option value="" disabled selected>-- Selecciona un almacén --</option>
                                    <?php
                                    // Cargar almacenes desde la base de datos
                                    if (!empty($almacenes)) {
                                        foreach ($almacenes as $almacen) {
                                            $nombreCompleto = htmlspecialchars($almacen['nombre'], ENT_QUOTES, 'UTF-8');
                                            $nombreCorto = strlen($nombreCompleto) > 25 ? substr($nombreCompleto, 0, 25) . '...' : $nombreCompleto;
                                            echo '<option value="' . htmlspecialchars($almacen['cod_almacen'], ENT_QUOTES, 'UTF-8') . '" ';
                                            echo 'title="🏢 ' . $nombreCompleto . '" data-full-name="' . $nombreCompleto . '">';
                                            echo '🏢 ' . $nombreCorto;
                                            echo '</option>';
                                        }
                                    } else {
                                        echo '<option value="" disabled>No hay almacenes disponibles</option>';
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor selecciona un almacén.
                                </div>
                            </div>

                            <!-- Input Artículo -->
                            <div class="form-group mb-4">
                                <label for="articulo" class="form-label text-muted font-weight-bold mb-2">
                                    <i class="fa fa-tag mr-2 text-success"></i>Artículo
                                </label>
                                <input type="text" class="form-control form-control-lg" id="articulo" name="articulo"
                                    placeholder="Ingresa un artículo" required maxlength="255">
                                <div class="invalid-feedback">
                                    Por favor ingresa el codigo del artículo o el codigo de barras.
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fa fa-info-circle mr-1"></i>código del artículo o código de barras del producto
                                </small>
                            </div>

                            <!-- Botón -->
                            <div class="form-group text-center mt-5">
                                <button type="button" class="btn btn-primary btn-lg btn-block custom-btn" onclick="mostrarModal()">
                                    <i class="fa fa-external-link mr-2"></i>Mostrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar información -->
    <div class="modal fade" id="articuloModal" tabindex="-1" role="dialog" aria-labelledby="articuloModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header bg-gradient-primary text-white" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="articuloModalLabel">
                        <i class="fa fa-cube mr-2"></i>Información del Artículo
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="cerrarModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div id="modalContent">
                        <!-- El contenido se cargará aquí dinámicamente -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="cerrarModal()">
                        <i class="fa fa-times mr-2"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos formulario */
        .min-vh-75 {
            min-height: 75vh;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card {
            transition: all 0.3s ease;
            border-radius: 15px !important;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            font-size: 15px !important;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background-color: white;
        }

        .custom-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            font-size: 15px !important;
        }

        .custom-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .form-label {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .opacity-75 {
            opacity: 0.75;
        }

        .text-primary {
            color: #667eea !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        /* Reducir tamaño de fuente del texto de ayuda */
        .form-text.text-muted {
            font-size: 0.75rem !important;
            color: #b8b8b8 !important;
        }

        .custom-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            padding-right: 2.5rem;
            font-size: 15px !important;
        }
        
        /* Mejorar la legibilidad del dropdown */
        .custom-select option {
            padding: 12px 15px;
            line-height: 1.6;
            color: #495057;
            min-height: 40px;
            display: flex;
            align-items: center;
            font-size: 12px !important;
        }
        
        .custom-select option:hover,
        .custom-select option:focus {
            background-color: #f8f9fa;
        }
        
        /* Asegurar que el select tenga altura suficiente */
        .custom-select {
            min-height: 48px;
            line-height: 1.6;
            vertical-align: middle;
        }
        
        /* Estilos específicos para diferentes navegadores */
        .custom-select::-webkit-scrollbar {
            width: 8px;
        }
        
        .custom-select::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .custom-select::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .custom-select::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
        
        /* Estilos para el tooltip nativo del browser */
        .custom-select option[title] {
            position: relative;
        }
        
        /* Indicador visual cuando el dropdown está activo */
        .custom-select.dropdown-active {
            border-color: #667eea !important;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
            transform: scale(1.02);
        }

        /* Estilos responsivos para pantallas pequeñas */
        @media (max-width: 768px) {
            .card-body {
                padding: 2rem !important;
            }
            
            /* Centrar el subtítulo en pantallas pequeñas */
            .card-header .text-center {
                text-align: center !important;
            }
            
            .card-header .text-center small.opacity-75 {
                text-align: center !important;
                display: block !important;
                width: 100% !important;
                margin: 0 auto !important;
            }
            
            /* Ajustes específicos para el dropdown de almacén en móviles */
            .custom-select {
                font-size: 15px !important;
                padding: 0.75rem 2.25rem 0.75rem 0.75rem;
                max-width: 100%;
                min-height: 50px;
                line-height: 1.5;
            }
            
            /* Truncar texto largo en opciones del select */
            .custom-select option {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
                padding: 12px 15px;
                min-height: 42px;
                line-height: 1.5;
                display: flex;
                align-items: center;
                font-size: 12px !important;
            }
            
            /* Ajustar el contenedor del formulario */
            .col-md-8 {
                padding: 0 15px;
            }
        }
        
        @media (max-width: 576px) {
            /* Para pantallas extra pequeñas */
            .card-body {
                padding: 1.5rem !important;
            }
            
            /* Centrar el subtítulo en pantallas extra pequeñas */
            .card-header .text-center {
                text-align: center !important;
            }
            
            .card-header .text-center small.opacity-75 {
                text-align: center !important;
                display: block !important;
                width: 100% !important;
                margin: 0 auto !important;
            }
            
            .custom-select {
                font-size: 15px !important;
                padding: 0.75rem 2rem 0.75rem 0.75rem;
                min-height: 48px;
                line-height: 1.4;
            }
            
            .custom-select option {
                font-size: 12px !important;
                padding: 10px 12px;
                min-height: 38px;
                line-height: 1.4;
                display: flex;
                align-items: center;
            }
            
            .form-label {
                font-size: 0.9rem;
            }
            
            /* Reducir el espaciado en pantallas muy pequeñas */
            .form-group {
                margin-bottom: 1.5rem !important;
            }
            
            .display-4 {
                font-size: 2rem !important;
            }
            
            .card-title {
                font-size: 1.25rem !important;
            }
        }

        /* Estilos para el modal */
        .modal-content {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            border-bottom: none;
            display: flex !important; /* Override del style.css que pone display: block */
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
        }

        /* Asegurar que el modal sea visible */
        #articuloModal.show {
            display: block !important;
            z-index: 1060 !important;
        }

        #articuloModal .modal-dialog {
            z-index: 1061 !important;
        }

        /* Asegurar backdrop */
        .modal-backdrop.show {
            opacity: 0.5 !important;
            z-index: 1040 !important;
        }
    </style>

    <!-- JavaScript para validación -->
    <script>
        // Validación de formulario - interceptar el submit y ejecutar mostrarModal
        document.getElementById('articuloForm').addEventListener('submit', function(event) {
            // Prevenir el comportamiento por defecto del submit
            event.preventDefault();
            event.stopPropagation();
            
            // Ejecutar la misma funcionalidad que el botón "Mostrar"
            mostrarModal();
        });

        // Animación de entrada (verificar si jQuery está disponible)
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof $ !== 'undefined') {
                $('.card').hide().fadeIn(800);
            } else {
                // Alternativa sin jQuery
                var card = document.querySelector('.card');
                if (card) {
                    card.style.opacity = '0';
                    card.style.transition = 'opacity 0.8s ease-in-out';
                    setTimeout(function() {
                        card.style.opacity = '1';
                    }, 100);
                }
            }
            
            // Mejorar la experiencia del dropdown en móviles
            setupMobileDropdown();
            
            // Configurar listener para la tecla Enter en los campos del formulario
            setupEnterKeyListener();
        });

        // Función para mejorar el dropdown en dispositivos móviles
        function setupMobileDropdown() {
            var selectElement = document.getElementById('almacen');
            
            if (selectElement) {
                // Detectar si es un dispositivo móvil
                var isMobile = window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                
                if (isMobile) {
                    // En móviles, mejorar la accesibilidad del select
                    selectElement.addEventListener('focus', function() {
                        this.style.fontSize = '16px'; // Prevenir zoom en iOS
                    });
                    
                    // Mostrar el nombre completo en el tooltip cuando se selecciona
                    selectElement.addEventListener('change', function() {
                        var selectedOption = this.options[this.selectedIndex];
                        if (selectedOption && selectedOption.getAttribute('data-full-name')) {
                            var fullName = selectedOption.getAttribute('data-full-name');
                            this.title = '🏢 ' + fullName;
                        }
                    });
                }
                
                // Agregar indicador visual cuando el dropdown está abierto
                selectElement.addEventListener('mousedown', function() {
                    this.classList.add('dropdown-active');
                });
                
                selectElement.addEventListener('blur', function() {
                    this.classList.remove('dropdown-active');
                });
            }
        }

        // Función para configurar el listener de la tecla Enter
        function setupEnterKeyListener() {
            var almacenSelect = document.getElementById('almacen');
            var articuloInput = document.getElementById('articulo');
            
            // Agregar listener para Enter en el campo de artículo
            if (articuloInput) {
                articuloInput.addEventListener('keypress', function(event) {
                    if (event.key === 'Enter' || event.keyCode === 13) {
                        event.preventDefault();
                        mostrarModal();
                    }
                });
            }
            
            // Agregar listener para Enter en el select de almacén
            if (almacenSelect) {
                almacenSelect.addEventListener('keypress', function(event) {
                    if (event.key === 'Enter' || event.keyCode === 13) {
                        event.preventDefault();
                        mostrarModal();
                    }
                });
            }
        }

        // Función para mostrar el modal
        function mostrarModal() {
            console.log('mostrarModal() llamada');
            
            var almacen = document.getElementById('almacen').value;
            var articulo = document.getElementById('articulo').value.trim();
            
            console.log('Almacén:', almacen, 'Artículo:', articulo);

            // Validar campos
            if (!almacen) {
                console.log('Error: No se seleccionó almacén');
                document.getElementById('almacen').classList.add('is-invalid');
                alert('Por favor selecciona un almacén');
                return;
            } else {
                document.getElementById('almacen').classList.remove('is-invalid');
            }

            if (!articulo) {
                console.log('Error: No se ingresó artículo');
                document.getElementById('articulo').classList.add('is-invalid');
                alert('Por favor ingresa el código del artículo o el codigo de barras');
                return;
            } else {
                document.getElementById('articulo').classList.remove('is-invalid');
            }

            // Mostrar loading
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-2">Buscando información del artículo...</p>
                </div>
            `;

            // Mostrar modal - usar jQuery ya que está disponible en footer2.php
            console.log('Intentando mostrar modal...');
            console.log('jQuery disponible:', typeof $ !== 'undefined');
            console.log('Bootstrap modal disponible:', typeof $ !== 'undefined' && $.fn && $.fn.modal);
            
            try {
                if (typeof $ !== 'undefined' && $.fn.modal) {
                    console.log('Usando jQuery para mostrar modal');
                    
                    // Forzar que el modal sea visible
                    var modalElement = $('#articuloModal');
                    modalElement.css({
                        'display': 'block',
                        'z-index': '1060'
                    });
                    modalElement.addClass('show');
                    
                    // Agregar backdrop manualmente si no existe
                    if (!$('.modal-backdrop').length) {
                        $('body').append('<div class="modal-backdrop fade show" style="z-index: 1040;"></div>');
                    }
                    $('body').addClass('modal-open');
                    
                    // También intentar el método normal de Bootstrap
                    modalElement.modal('show');
                } else {
                    console.log('Usando fallback manual para mostrar modal');
                    // Fallback: mostrar modal manualmente
                    var modal = document.getElementById('articuloModal');
                    modal.style.display = 'block';
                    modal.style.zIndex = '1060';
                    modal.classList.add('show');
                    document.body.classList.add('modal-open');
                    
                    // Crear backdrop
                    var backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    backdrop.style.zIndex = '1040';
                    backdrop.id = 'modalBackdrop';
                    document.body.appendChild(backdrop);
                }
                console.log('Modal mostrado exitosamente');
            } catch (error) {
                console.error('Error al mostrar modal:', error);
                alert('Error al mostrar el modal. Verifique la consola para más detalles.');
            }

            // Llamar AJAX
            buscarArticulo(almacen, articulo);
        }

        // Función para buscar el artículo en la base de datos
        function buscarArticulo(almacen, articulo) {
            console.log('Iniciando búsqueda AJAX...');
            console.log('URL:', 'articuloConsultaAjax.php');
            console.log('Datos:', 'almacen=' + almacen + '&articulo=' + articulo);
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'articuloConsultaAjax.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                console.log('Estado AJAX:', xhr.readyState, 'Status:', xhr.status);
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log('Respuesta del servidor:', xhr.responseText);
                        try {
                            var response = JSON.parse(xhr.responseText);
                            console.log('Respuesta parseada:', response);
                            mostrarResultados(response, almacen, articulo);
                        } catch (e) {
                            console.error('Error al parsear JSON:', e);
                            mostrarError('Error al procesar la respuesta del servidor');
                        }
                    } else {
                        console.error('Error HTTP:', xhr.status);
                        mostrarError('Error al conectar con el servidor');
                    }
                }
            };
            
            var datos = 'almacen=' + encodeURIComponent(almacen) + '&articulo=' + encodeURIComponent(articulo);
            xhr.send(datos);
        }

        // Función para mostrar los resultados
        function mostrarResultados(data, almacen, articulo) {
            var almacenSelect = document.getElementById('almacen');
            var almacenTexto = almacenSelect.options[almacenSelect.selectedIndex].text;
            
            if (data.success && data.articulo) {
                var art = data.articulo;
                
                // Construir jerarquía
                var jerarquia = '';
                if (art.arbol_nivel1 || art.arbol_nivel2 || art.arbol_nivel3) {
                    var niveles = [art.arbol_nivel1, art.arbol_nivel2, art.arbol_nivel3].filter(nivel => nivel && nivel !== 'N/A');
                    jerarquia = niveles.length > 0 ? niveles.join(' > ') : 'N/A';
                } else {
                    jerarquia = 'N/A';
                }
                
                var modalContent = `
                    <div class="row">
                        <div class="col-md-12">
                            <!-- DETALLE PRODUCTO -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fa fa-cube mr-2"></i>DETALLE PRODUCTO
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Código:</strong></div>
                                        <div class="col-sm-8">${art.ItemCode || 'N/A'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Descripción:</strong></div>
                                        <div class="col-sm-8">${art.ItemName || 'N/A'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Código Barras:</strong></div>
                                        <div class="col-sm-8">${art.CodeBars || 'N/A'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Marca:</strong></div>
                                        <div class="col-sm-8">${art.marca_producto || 'N/A'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Categoría:</strong></div>
                                        <div class="col-sm-8">${art.categoria || 'N/A'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Jerarquía:</strong></div>
                                        <div class="col-sm-8">${jerarquia}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- VENTAS -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fa fa-chart-line mr-2"></i>VENTAS
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Promedio 30 días:</strong></div>
                                        <div class="col-sm-8">${art.VentaPromedio || '0'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Última Venta:</strong></div>
                                        <div class="col-sm-8">${art.VentaUltima || '0'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Total 30 días:</strong></div>
                                        <div class="col-sm-8">${art.CantidadTotalTreintaDias || '0'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Total 90 días:</strong></div>
                                        <div class="col-sm-8">${art.CantidadTotalNoventaDias || '0'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Venta acumulada 90 días:</strong></div>
                                        <div class="col-sm-8">${art.venta_90dias || '0'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Días última fecha ingreso:</strong></div>
                                        <div class="col-sm-8">${art.dias_ultima_fecha_ingreso || 'N/A'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Días última venta:</strong></div>
                                        <div class="col-sm-8">${art.DiasUltimaFechaIngreso || 'N/A'}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- STOCK -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fa fa-boxes mr-2"></i>STOCK
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Stock en tienda:</strong></div>
                                        <div class="col-sm-8">${art.total_Tienda || '0'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Stock en bodega:</strong></div>
                                        <div class="col-sm-8">${art.total_Bodega || '0'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Stock en tránsito:</strong></div>
                                        <div class="col-sm-8">${art.total_Transitoria_Tienda || '0'}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Almacén:</strong></div>
                                        <div class="col-sm-8">${art.WhsCode || almacen} - ${art.WhsName || almacenTexto}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                var modalContent = `
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle mr-2"></i>
                        <strong>Artículo no encontrado</strong><br>
                        No se encontró información para: <strong>${articulo}</strong>
                    </div>
                `;
            }
            
            document.getElementById('modalContent').innerHTML = modalContent;
        }

        // Función para mostrar errores
        function mostrarError(mensaje) {
            var modalContent = `
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-circle mr-2"></i>
                    <strong>Error</strong><br>
                    ${mensaje}
                </div>
            `;
            document.getElementById('modalContent').innerHTML = modalContent;
        }

        // Función para cerrar el modal
        function cerrarModal() {
            try {
                if (typeof $ !== 'undefined' && $.fn.modal) {
                    console.log('Cerrando modal con jQuery');
                    $('#articuloModal').modal('hide');
                    
                    // Limpiar manualmente por si acaso
                    setTimeout(function() {
                        $('#articuloModal').removeClass('show').css('display', 'none');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                    }, 300);
                } else {
                    console.log('Cerrando modal manualmente');
                    // Cerrar modal manualmente
                    var modal = document.getElementById('articuloModal');
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                    
                    // Remover backdrop si existe
                    var backdrop = document.getElementById('modalBackdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                    
                    // Remover todos los backdrops
                    var allBackdrops = document.querySelectorAll('.modal-backdrop');
                    allBackdrops.forEach(function(backdrop) {
                        backdrop.remove();
                    });
                }
            } catch (error) {
                console.error('Error al cerrar modal:', error);
            }
        }
    </script>


    <!---------------------------------------------->
    <!--------------Fin Content -------------------->
    <!---------------------------------------------->
</div>
<?php include_once "footer2.php"; ?>