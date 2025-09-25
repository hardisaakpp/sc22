
       <!-- /.content -->
       <div class="clearfix"></div>
        <!-- Footer -->
        <footer class="site-footer">
            <div class="footer-inner bg-white" style="padding:6px 0; min-height:unset;">
                <div class="row" style="display:flex;align-items:center;flex-wrap:nowrap;">
                    <div class="col-sm-6" style="flex:1 1 50%;max-width:50%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <img src="images/favicon.png" alt="Icono" style="width: 24px; height:24px; vertical-align:middle;">
                    </div>
                    <div class="col-sm-6 text-right" style="flex:1 1 50%;max-width:50%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        By <a href="mailto:sistemas@sunsetcorpholding.com">Carlos O[SIS-SUN]</a>
                    </div>
                </div>
            </div>
        </footer>
        <!-- /.site-footer -->
    </div>
    <!-- /.right-panel -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>

<script src="assets/js/main.js"></script>
<script src="assets/js/lib/chosen/chosen.jquery.min.js"></script>
 <!--  Chart js -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.js"></script>
    <!--Flot Chart-->
    <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot-spline@0.0.1/js/jquery.flot.spline.min.js"></script>
    <!-- local -->
    <script>
        // Override Flot to prevent dimension errors when no charts are present
        // Wait for jQuery to be available
        (function() {
            function initCharts() {
                if (typeof $ === 'undefined' || typeof $.fn === 'undefined') {
                    console.log('jQuery not ready, retrying...');
                    setTimeout(initCharts, 100);
                    return;
                }
                
                $(document).ready(function() {
                    // Check if there are any chart containers before initializing
                    if ($('[id*="flot"], [class*="flot"], [id*="chart"], [class*="chart"]').length === 0) {
                        // No chart elements found, skip widgets.js initialization
                        console.log('No chart elements found, skipping chart initialization');
                    } else {
                        // Load widgets.js only if chart elements exist
                        $.getScript('assets/js/widgets.js').fail(function() {
                            console.log('widgets.js not found or failed to load');
                        });
                    }
                });
            }
            
            // Start initialization
            initCharts();
        })();
    </script>

    
<script src="assets/js/lib/chosen/chosen.jquery.min.js"></script>
<script src="assets/js/lib/data-table/datatables.min.js"></script>
<script src="assets/js/lib/data-table/dataTables.bootstrap.min.js"></script>
<script src="assets/js/lib/data-table/dataTables.buttons.min.js"></script>
<script src="assets/js/lib/data-table/buttons.bootstrap.min.js"></script>
<script src="assets/js/lib/data-table/jszip.min.js"></script>
<script src="assets/js/lib/data-table/vfs_fonts.js"></script>
<script src="assets/js/lib/data-table/buttons.html5.min.js"></script>
<script src="assets/js/lib/data-table/buttons.print.min.js"></script>
<script src="assets/js/lib/data-table/buttons.colVis.min.js"></script>
<script src="assets/js/init/datatables-init.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


<script type="text/javascript">

$(window).on('load',function() {
      setTimeout(function () {
    $(".loader-page").css({visibility:"hidden",opacity:"0", display:"none"})
  }, 2000);
});

// Ensure jQuery is available before running
(function() {
    function initializeApp() {
        if (typeof $ === 'undefined' || typeof $.fn === 'undefined') {
            console.log('jQuery not ready for main initialization, retrying...');
            setTimeout(initializeApp, 100);
            return;
        }
        
        $(document).ready(function() {
            // Initialize DataTables if elements exist
            if ($('#bootstrap-data-table-export').length) {
                $('#bootstrap-data-table-export').DataTable();
            }
            if ($('#example').length) {
                $('#example').DataTable({
                    paging: false,
                    ordering: false,
                    info: false,
                });
            }
            
            // Initialize select2 if elements exist
            if ($('.js-example-basic-single').length) {
                $('.js-example-basic-single').select2();
            }
            
            // Define indexesLX function if not already defined
            if (typeof indexesLX === 'undefined') {
                window.indexesLX = function() {
                    try {
                        // Column toggle functionality for tables
                        $('#n1').click(function() {
                            var n11 = 6;
                            $("td:nth-child(" + n11 + "),th:nth-child(" + n11 + ")").toggle();
                        });
                        $('#n2').click(function() {
                            $('td:nth-child(8),th:nth-child(8)').toggle();
                        });
                        $('#n3').click(function() {
                            $('td:nth-child(10),th:nth-child(10)').toggle();
                        });
                    } catch(x) {
                        // Handle any errors silently
                        console.log('indexesLX error:', x.message);
                    }
                };
            }
            
            // Only call indexesLX if there are elements that need it
            if ($('#n1, #n2, #n3').length > 0) {
                indexesLX();
            }
            
            // Fix para espacios extra después de cargar DataTables
            setTimeout(function() {
                // Limpiar espacios extra en DataTables
                $('.dataTables_wrapper').css('margin-bottom', '0');
                $('.dataTables_info, .dataTables_paginate').css('margin-bottom', '10px');
                
                // Asegurar que el footer esté en la posición correcta
                $('.site-footer').css('margin-top', 'auto');
                
                // Limpiar cualquier altura extra en el body
                $('body').css('padding-bottom', '0');
                $('#right-panel').css('padding-bottom', '0');
                
                // Debug: mostrar en consola si hay elementos con altura excesiva
                if (window.location.search.includes('debug=height')) {
                    $('*').each(function() {
                        var height = $(this).outerHeight();
                        if (height > $(window).height() * 0.8) {
                            console.log('Elemento con altura excesiva:', this, 'Altura:', height + 'px');
                        }
                    });
                }
            }, 500);
        });
    }
    
    // Start initialization
    initializeApp();
})();





// Ensure jQuery is available for additional functionality
(function() {
    function initAdditionalFeatures() {
        if (typeof $ === 'undefined' || typeof $.fn === 'undefined') {
            setTimeout(initAdditionalFeatures, 100);
            return;
        }
        
        $(document).ready(function() {
            // Checkbox functionality
            $('input[type="checkbox"]').on('change', function(){
                this.value ^= 1;
            });
            
            // AJAX loading indicators
            $("#loading").ajaxStart(function () {
                $(this).show();
            });

            $("#loading").ajaxStop(function () {
                $(this).hide();
            });
        });
    }
    
    initAdditionalFeatures();
})();

</script>
  

</body>
</html>
