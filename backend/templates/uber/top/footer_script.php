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
	
		$(".label-i").on('click',function(e) {
			var lang_id = $(this).data('id');
			var from = $(this).data('value');
			$.ajax({
				type: "POST",
				url: 'language_popup.php',
				data: 'lang_id=' + lang_id + '&from='+from,
				success: function (dataHtml)
				{
					$("#lang_popup").html(dataHtml);
					$("#myModalHorizontal").modal('show');
				},
				error: function(dataHtml){
					
				}
			});
			e.stopPropagation();
			return false;
		});
    });
	
	function updateLanguage(){
		var formdata = $("#_languages_form").serialize();
		$.ajax({
			type: "POST",
			url: 'language_save.php',
			data: formdata,
			success: function (dataHtml)
			{
				location.reload();
			},
			error: function(dataHtml){
				
			}
		});
	}
	
</script>
<!-- Modal -->
<div class="modal fade" id="myModalHorizontal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Change Language Label</h4>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body" id="lang_popup">
                
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateLanguage();">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->