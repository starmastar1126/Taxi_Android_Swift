<?php  //General Functions
function paginate($reload, $page, $tpages) {
    $adjacents = 2;
    $prevlabel = "&lsaquo; Prev";
    $nextlabel = "Next &rsaquo;";
    $firstlabel = "&lsaquo;&lsaquo; First";
    $Lastlabel = "Last &rsaquo;&rsaquo;";
    $out = "";
    // previous
    if ($page == 1) {
        $out.= "<span class='disabled-page001'>" . $prevlabel . "</span>\n";
    } elseif ($page == 2) {
        $out.= "<li><a  href=\"" . $reload . "\">" . $prevlabel . "</a>\n</li>";
    } else {
        $out.= "<li><a  href=\"" . $reload . "&amp;page=" . ($page - 1) . "\">" . $prevlabel . "</a>\n</li>";
    }
	if ($page > 3) {
        $out.= "<a style='font-size:11px' href='" . $reload . "'&amp;page='1'>".$firstlabel."</a>\n";
    }
  
    $pmin = ($page > $adjacents) ? ($page - $adjacents) : 1;
    $pmax = ($page < ($tpages - $adjacents)) ? ($page + $adjacents) : $tpages;
    for ($i = $pmin; $i <= $pmax; $i++) {
        if ($i == $page) {
            $out.= "<li  class=\"active\"><a href=''>" . $i . "</a></li>\n";
        } elseif ($i == 1) {
            $out.= "<li><a  href=\"" . $reload . "\">" . $i . "</a>\n</li>";
        } else {
            $out.= "<li><a  href=\"" . $reload . "&amp;page=" . $i . "\">" . $i . "</a>\n</li>";
        }
    }
    
    if ($page < ($tpages - $adjacents)) {
        $out.= "<a style='font-size:11px' href=\"" . $reload . "&amp;page=" . $tpages . "\">" . $Lastlabel . "</a>\n";
    }
    // next
    if ($page < $tpages) {
        $out.= "<li><a  href=\"" . $reload . "&amp;page=" . ($page + 1) . "\">" . $nextlabel . "</a>\n</li>";
    } else {
        $out.= "<span class='disabled-page002'>" . $nextlabel . "</span>\n";
    }
    $out.= "";
    return $out;
}

?>
<script>
	function checkAlls(){
		jQuery("#_list_form input[type=checkbox]").each(function() {
			if($(this).attr('disabled') != 'disabled'){
				this.checked = 'true';
			}
		});
	}
	
	function uncheckAlls(){
		jQuery("#_list_form input[type=checkbox]").each(function() {
			this.checked = '';
		});
	}
	
	function ChangeStatusAll(statusNew) {
		if(statusNew != "") {
		var status = statusNew;
		var checked = $("#_list_form input:checked").length;
		if (checked > 0) {
			if (checked == 1) {
				if ($("#_list_form input:checked").attr("id") == 'setAllCheck') {
					$('#is_not_check_modal').modal('show');
					$("#changeStatus").val('');
					return false;
				}
			}
			$("#statusVal").val(status);
			if(status == 'Active') {
				$('#is_actall_modal').modal('show');
			}else if(status == 'Inactive'){
				$('#is_inactall_modal').modal('show');
			}else {
				$('#is_dltall_modal').modal('show');
			}
			
			$(".action_modal_submit").unbind().click(function () {
				var action = $("#pageForm").attr('action');
				var formValus = $("#_list_form, #pageForm").serialize();
				window.location.href = action+"?"+formValus;
			});
			$("#changeStatus").val('');
		} else {
			$('#is_not_check_modal').modal('show');
			$("#changeStatus").val('');
			return false;
		}
		}else {
			return false;
		}
	}
	
	
	function changeStatus(iAdminId,status) {
//		$('html').addClass('loading');
		var action = $("#pageForm").attr('action');
		var page = $("#page").val();
		if(status == 'Active') {
			status = 'Inactive';
		}else {
			status = 'Active';
		}
		$("#page").val(page);
		$("#iMainId01").val(iAdminId);
		$("#status01").val(status);
		var formValus = $("#pageForm").serialize();
                window.location.href = action+"?"+formValus;
	}
	//make
	/* function changeStatus(iMakeId,status) {
//		$('html').addClass('loading');
		var action = $("#pageForm").attr('action');
		var page = $("#page").val();
		if(status == 'Active') {
			status = 'Inactive';
		}else {
			status = 'Active';
		}
		$("#page").val(page);
		$("#iMainId01").val(iAdminId);
		$("#status01").val(status);
		var formValus = $("#pageForm").serialize();
                window.location.href = action+"?"+formValus;
	} */
	//make
	function changeStatusDelete(iAdminId) {
		$('#is_dltSngl_modal').modal('show');
		$(".action_modal_submit").unbind().click(function () {
//			$('html').addClass('loading');
			var action = $("#pageForm").attr('action');
			var page = $("#pageId").val();
			$("#pageId01").val(page);
			$("#iMainId01").val(iAdminId);
			$("#method").val('delete');
			var formValus = $("#pageForm").serialize();
                window.location.href = action+"?"+formValus;
               
		});
	}
	function changeStatusDeletevehicle(iAdminId,driverid) {
		$('#is_dltSngl_modal').modal('show');
		$(".action_modal_submit").unbind().click(function () {
//			$('html').addClass('loading');
			var action = $("#pageForm").attr('action');
			var page = $("#pageId").val();
			$("#pageId01").val(page);
			$("#iMainId01").val(iAdminId);
			$("#iDriverId").val(driverid);
			$("#method").val('delete');
			var formValus = $("#pageForm").serialize();
                window.location.href = action+"?"+formValus;
               
		});
	}
	
        function changeStatusDeletecd(iAdminId) {
		$('#is_dltSngl_modal_cd').modal('show');
		$(".action_modal_submit").unbind().click(function () {
//			$('html').addClass('loading');
			var action = $("#pageForm").attr('action');
			var page = $("#pageId").val();
			$("#pageId01").val(page);
			$("#iMainId01").val(iAdminId);
			$("#method").val('delete');
			var formValus = $("#pageForm").serialize();
            window.location.href = action+"?"+formValus;
               
		});
	}
        
        
	function resetTripStatus(iAdminId) {
		$('#is_resetTrip_modal').modal('show');
		$(".action_modal_submit").unbind().click(function () {
//			$('html').addClass('loading');
			var action = $("#pageForm").attr('action');
			var page = $("#pageId").val();
			$("#pageId01").val(page);
			$("#iMainId01").val(iAdminId);
			$("#method").val('reset');
			var formValus = $("#pageForm").serialize();
			window.location.href = action+"?"+formValus;
		});
	}
	
	function showExportTypes(section) {
		if(section == 'review'){
			$("#show_export_types_modal_excel").modal('show');
			$("#export_modal_submit_excel").on('click',function () {
				var action = "main_export.php";
				var formValus = $("#_export_form, #pageForm, #show_export_modal_form_excel").serialize();
				//alert(formValus);
				window.location.href = action+'?section='+section+'&'+formValus;
				$("#show_export_types_modal_excel").modal('hide');
				return false;
			});
		} else {
			$("#show_export_types_modal").modal('show');
			$("#export_modal_submit").on('click',function () {
				var action = "main_export.php";
				var formValus = $("#_export_form, #pageForm, #show_export_modal_form").serialize();
				//alert(formValus);
				window.location.href = action+'?section='+section+'&'+formValus;
				$("#show_export_types_modal").modal('hide');
				return false;
			});
		}
		
	}
	
	function Redirect(sortby,order) {
            //$('html').addClass('loading');
            $("#sortby").val(sortby);
            if(order == 0) { order = 1; } else { order = 0; }
            $("#order").val(order);
            $("#page").val('1');
            var action = $("#_list_form").attr('action');
            var formValus = $("#pageForm").serialize();
			//alert(formValus);
            window.location.href = action+"?"+formValus;
	}
	
	function reset_form(formId) {
            $("#"+formId).find("input[type=text],input[type=password],input[type=file], textarea, select").val("");
	}
        
        //function openHoverAction(openId) {
        $('.openHoverAction-class').click(function(e){
           // openHoverAction-class
            //e.preventDefault();
            alert('hiii');
            // hide all span
            var $this = $(this).find('.show-moreOptions');
            $(".openHoverAction-class .show-moreOptions").not($this).hide();

            // here is what I want to do
            $this.toggle();
            
//            if($(".openPops_"+openId).hasClass('active')) {
//                $('.show-moreOptions').removeClass('active');
//            }else {
//                
//            }
//            $(".openPops_"+openId).addClass('active');
        });
        
        
	
	function reportExportTypes(section) {
            var action = "report_export.php";
            var formValus = $("#pageForm").serialize();
            //alert(formValus);
            window.location.href = action+'?section='+section+'&'+formValus;
            return false;
	}
	
	function Paytodriver() {
		var checked = $("#_list_form input:checked").length;
		if (checked > 0) {
			if (checked == 1) {
				if ($("#_list_form input:checked").attr("id") == 'setAllCheck') {
					$('#is_not_check_modal').modal('show');
					$("#changeStatus").val('');
					return false;
				}
			}
			$('#is_payTo_modal').modal('show');
			$(".action_modal_submit").unbind().click(function () {
				$("#ePayDriver").val('Yes');
				var action = $("#pageForm").attr('action');
				var formValus = $("#_list_form, #pageForm").serialize();
				window.location.href = action+"?"+formValus;
			});
		} else {
			$('#is_not_check_modal').modal('show');
			return false;
		}
	}
	
	function changeOrder(iAdminId) {
		$('#is_dltSngl_modal').modal('show');
		$(".action_modal_submit").unbind().click(function () {
			var action = $("#pageForm").attr('action');
			var page = $("#pageId").val();
			$("#pageId01").val(page);
			$("#iMainId01").val(iAdminId);
			$("#method").val('delete');
			var formValus = $("#pageForm").serialize();
			window.location.href = action+"?"+formValus;   
		});
	}
	

	
</script>

