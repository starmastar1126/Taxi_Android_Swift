$(document).ready(function(){
	$("#login-form").validate({
		submitHandler : function(e) {
		    $(form).submit();
			
		},
		rules : {
			email : {
				required : true,
				email: true
			},
			password : {
				required : true
			}
		},
		errorPlacement : function(error, element) {
			$(element).removeClass('has-success').addClass('has-error');
		},
		highlight : function(element) {
			$(element).removeClass('has-success').addClass('has-error');
		},
		unhighlight: function(element, errorClass, validClass) {
			 $(element).removeClass('has-error').addClass('has-success');
		}
	});
	
	
});