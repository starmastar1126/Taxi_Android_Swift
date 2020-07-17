<!-- Custome JS -->
<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/bootbox.min.js"></script>
<script src="assets/js/magic.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".custom-select-new").each(function(){
            $(this).wrap("<em class='select-wrapper'></em>");
            $(this).after("<em class='holder'></em>");
        });
        $(".custom-select-new").change(function(){
            var selectedOption = $(this).find(":selected").text();
            $(this).next(".holder").text(selectedOption);
        }).trigger('change');
    })
</script>