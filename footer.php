
       <!-- /.content -->
       <div class="clearfix"></div>
        <!-- Footer -->
        <footer class="site-footer">
            <div class="footer-inner bg-white">
                <div class="row">
                    
                    <div class="col-sm-6">
                    .
                    </div>
                    
                    <div class="col-sm-6 text-right">
                        By <a href="mailto:sistemas@sunsetcorpholding.com">Alex [SIS-SUN]</a>
                    </div>
                </div>
            </div>
        </footer>
        <!-- /.site-footer -->
    
    



<!-- Right Panel -->






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
    <script src="assets/js/widgets.js"></script>

    
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
    $(".loader-page").css({visibility:"hidden",opacity:"80"})
  }, 2000);
});

$(document).ready(function() {
  $('#bootstrap-data-table-export').DataTable();
  $('#example').DataTable({
        paging: false,
        ordering: false,
        info: false,
    });
    $('.js-example-basic-single').select2();
    indexesLX();
} );





$('input[type="checkbox"]').on('change', function(){
    this.value ^= 1;
});
    
$("#loading").ajaxStart(function () {
    $(this).show();
 });

 $("#loading").ajaxStop(function () {
   $(this).hide();
 });

</script>
  

</body>
</html>
