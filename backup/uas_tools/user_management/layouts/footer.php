
<!-- /.content-wrapper-->
<!--    <footer class="sticky-footer">-->
<!--      <div class="container">-->
<!--        <div class="text-center">-->
<!--          <small>Copyright © <a href=""></a></small>-->
<!--        </div>-->
<!--      </div>-->
<!--    </footer>-->
    <!-- Scroll to Top Button-->
<!--    <a class="scroll-to-top rounded" href="#page-top">-->
<!--      <i class="fa fa-angle-up"></i>-->
<!--    </a>-->

    <!-- Bootstrap core JavaScript-->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="assets/js/sb-admin.min.js"></script>

    <!-- Custom scripts for this page-->
    <!-- Toggle between fixed and static navbar-->
    <script>
    $('#toggleNavPosition').click(function() {
      $('body').toggleClass('fixed-nav');
      $('nav').toggleClass('fixed-top static-top');
    });

    </script>
    <!-- Toggle between dark and light navbar-->
    <script>
    $('#toggleNavColor').click(function() {
      $('nav').toggleClass('navbar-dark navbar-light');
      $('nav').toggleClass('bg-dark bg-light');
      $('body').toggleClass('bg-dark bg-light');
    });
    </script>

    <script>
        $(document).ready(function(){
            $(".dropdown").hover(function(){
                var dropdownMenu = $(this).children(".dropdown-menu");
                if(dropdownMenu.is(":visible")){
                    dropdownMenu.parent().toggleClass("open");
                }
            });
        });
    </script>

    <script>
        //$(document).ready(function(){
        var url_regards = "/web/testing/regards.php";
          window.open(url_regards, "iframe_a");
        //};
    </script>

  </div>
</body>

</html>
