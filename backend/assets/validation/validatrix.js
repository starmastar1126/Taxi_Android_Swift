/*
Brought to you by Carlos Maldonado - @choquo
This script is open source
License: MIT
*/
function validatrix(){
	//Messages
	var warnings = {
		text : '*This text field is required',
		textarea: '*This textarea is required',
		select: '*Select an option',
		radio: '*Select a radio option',
		password : '*This text field is required',
		checkbox: '*Please agree to the Terms & Condition',
		termscheckbox: '*You must have to agree to the terms & conditions',
		minpassword: 'password must be atleast 6 character long'
	};
	//Init
	var validate = true;
	//Remove old warnings
	$(".required-label").remove();
	$("input").removeClass('required-ignore-multi');
	$(".required").each(function(){
		var myself = $(this);

		//text
		if( myself.prop("type").toLowerCase() === 'text' && myself.val()==='' ){
			myself.after('<div class="required-label">'+warnings.text+'</div>').addClass('required-active');
			validate = false;
		}
		if( myself.prop("type").toLowerCase() === 'password' && myself.val()===''){
			myself.after('<div class="required-label">'+warnings.text+'</div>').addClass('required-active');
			validate = false;
		}
		if( myself.prop("type").toLowerCase() === 'password' && myself.val().length<6){
			myself.after('<div class="required-label">'+warnings.minpassword+'</div>').addClass('required-active');
			validate = false;
		}
		//textarea
		if( myself.prop("type").toLowerCase() === 'textarea' && myself.val()==='' ){
			myself.after('<div class="required-label">'+warnings.textarea+'</div>').addClass('required-active');
			validate = false;
		}

		//select return (select-one or select-multiple)
		if( myself.prop("type").toLowerCase() === 'select-one' && $(myself,"option:selected").val()==='' ){
			myself.after('<div class="required-label">'+warnings.select+'</div>').addClass('required-active');
			validate = false;
		}

		//select return (select-one or select-multiple)
		if( myself.prop("type").toLowerCase() === 'select-multiple' && $(myself,"option:selected").val()==='' ){
			myself.after('<div class="required-label">'+warnings.select+'</div>').addClass('required-active');
			validate = false;
		}

		//radio
		if( myself.prop("type").toLowerCase() === 'radio' ){
			//find radio family
			var radio_family_name = $(myself).attr('name');
			//Format as: <label> <input> label_string </label> [INSERT WARNING HERE]
			if( $("input[name="+radio_family_name+"]").is(":checked") ){

			}else{
    			if( $("input[name="+radio_family_name+"]").hasClass("required-ignore-multi") ){}else{
						if($("input[name="+radio_family_name+"]").hasClass("gender")){
								$("input[name="+radio_family_name+"]").last().parent().after('<div class="required-label">'+warnings.gender+'</div>');
						}
						else{
						$("input[name="+radio_family_name+"]").last().parent().after('<div class="required-label">'+warnings.radio+'</div>');
						}
    				$("input[name="+radio_family_name+"]").addClass("required-ignore-multi");
    			}
				validate = false;
			}
		}

		//checkbox
		if( myself.prop("type").toLowerCase() === 'checkbox' ){
			//find radio family
			var checkbox_family_name = $(myself).attr('name');
			//Format as: <label> <input> label_string </label> [INSERT WARNING HERE]
			if( $("input[name="+checkbox_family_name+"]").is(":checked") ){

			}else{
				if(myself.hasClass('termscheckbox')){
					if( $("input[name="+checkbox_family_name+"]").hasClass("required-ignore-multi") ){}else{
						if($("input[name="+checkbox_family_name+"]").hasClass("terms-checkbox")){
							$("intermscheckboxput[name="+checkbox_family_name+"]").last().parent().after('<div style="margin:-19px 0 20px" class="required-label">'+warnings.termscheckbox+'</div>');
						}else{
							$("input[name="+checkbox_family_name+"]").last().parent().after('<div class="required-label">'+warnings.termscheckbox+'</div>');
						}
    				$("input[name="+checkbox_family_name+"]").addClass("required-ignore-multi");
    			}
				}
				else{
					if( $("input[name="+checkbox_family_name+"]").hasClass("required-ignore-multi") ){}else{
						if($("input[name="+checkbox_family_name+"]").hasClass("terms-checkbox")){
							$("intermscheckboxput[name="+checkbox_family_name+"]").last().parent().after('<div style="margin:-19px 0 20px" class="required-label">'+warnings.termscheckbox+'</div>');
						}else{
							$("input[name="+checkbox_family_name+"]").last().parent().after('<div class="required-label">'+warnings.checkbox+'</div>');
						}
    				$("input[name="+checkbox_family_name+"]").addClass("required-ignore-multi");
    			}
				}
				validate = false;
			}
		}

	});

	if( validate ){
		return true;
	}else{
		return false;
	}
}

$(function(){
	//Remove warnings on focus or click
	 $("input[type=radio].required, input[type=checkbox].required").on("focus click", function(){
		$(this).parent().parent().find('.required-label').remove();

	});

	$(".required").on("focus click",function(){
		$(this).removeClass('required-active');
		if (!$(this).is(':radio') && !$(this).is(':checkbox')) { $(this).next().remove(); }  
	});
});
