<?php 
include_once('../common.php');

require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();
$script = "Coupon";
if (!isset($generalobjAdmin)) {
     require_once(TPATH_CLASS . "class.general_admin.php");
     $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$id=(isset($_GET['id']))?$_GET['id']:'';

//For Currency
/*$sql="select vSymbol from  currency where eDefault='Yes'";
$db_currency=$obj->MySQLSelect($sql);*/

$iCouponId = isset($_REQUEST['iCouponId']) ? $_REQUEST['iCouponId'] : '';
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$action = ($iCouponId != '') ? 'Edit' : 'Add';

$tbl_name = 'coupon';

// set all variables with either post (when submit) either blank (when insert)
$iCouponId = isset($_REQUEST['iCouponId']) ? $_REQUEST['iCouponId'] : '';
$vCouponCode = isset($_REQUEST['vCouponCode']) ? $_REQUEST['vCouponCode'] : '';
$fDiscount = isset($_REQUEST['fDiscount']) ? $_REQUEST['fDiscount'] : '';
$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : '';
$eValidityType = isset($_REQUEST['eValidityType']) ? $_REQUEST['eValidityType'] : '';
$dActiveDate = isset($_REQUEST['dActiveDate']) ? $_REQUEST['dActiveDate'] : '';
$dExpiryDate = isset($_REQUEST['dExpiryDate']) ? $_REQUEST['dExpiryDate'] : '';
$iUsageLimit = isset($_REQUEST['iUsageLimit']) ? $_REQUEST['iUsageLimit'] : '';
$iUsed = isset($_REQUEST['iUsed']) ? $_REQUEST['iUsed'] : '';
$eStatus = isset($_REQUEST['eStatus']) ? $_REQUEST['eStatus'] : '';
$tDescription = isset($_REQUEST['tDescription']) ? $_REQUEST['tDescription'] : '';
$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
$previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';

if (isset($_POST['submit'])) {

      if(!empty($iCouponId)){
          if(SITE_TYPE=='Demo')
          {
            header("Location:coupon_action.php?iCouponId=" . $iCouponId . '&success=2');
            exit;
          }
          
      }
	  require_once("library/validation.class.php");
    $validobj = new validation();
	$validobj->add_fields($_POST['vCouponCode'], 'req', 'Coupon Code is required');
	$validobj->add_fields($_POST['tDescription'], 'req', 'Description is required');
	$validobj->add_fields($_POST['fDiscount'], 'req', 'Discount is required');
	if($_POST['eValidityType']=="Defined"){
		$validobj->add_fields($_POST['dActiveDate'], 'req', 'Activation Date is required');
		$validobj->add_fields($_POST['dExpiryDate'], 'req', 'Expiry Date is required');
	}
	$validobj->add_fields($_POST['iUsageLimit'], 'req', 'Usage Limit is required');
	$validobj->add_fields($_POST['eStatus'], 'req', 'Status is required');
	$error = $validobj->validate();

if ($error) {
        $success = 3;
        $newError = $error;
    } 
	else 
	{
		$q = "INSERT INTO ";
		$where = '';
		if ($action == 'Edit') {
			$str = " ";
		} else {
			$str = " , eStatus = 'Inactive' ";
		}
	 
		if(SITE_TYPE=='Demo')
		{
			$str = " , eStatus = 'active' ";
		}

		if($eValidityType == 'Permanent'){
			$dActiveDate = '';
			$dExpiryDate= '';
		}else{
			$dActiveDate =$dActiveDate;
			$dExpiryDate =$dExpiryDate;
		}
	 
		if ($iCouponId != '') {
			$q = "UPDATE ";
			$where = " WHERE `iCouponId` = '" . $iCouponId . "'";
		}        
		$query = $q . " `" . $tbl_name . "` SET
		`vCouponCode` = '" . $vCouponCode . "',
		`fDiscount` = '" . $fDiscount . "',
		`eType` = '" . $eType . "',
		`eValidityType` = '" . $eValidityType . "',
		`dActiveDate` = '" . $dActiveDate . "',
		`dExpiryDate` = '" . $dExpiryDate . "',
		`iUsageLimit` = '" . $iUsageLimit . "',		
		`tDescription` = '" . $tDescription . "',
		`eStatus` = '" . $eStatus . "'" . $where;
		$obj->sql_query($query);

		if ($action == "Add") {
			$_SESSION['success'] = '1';
			$_SESSION['var_msg'] = 'Promo Code Insert Successfully.';
		} else {
			$_SESSION['success'] = '1';
			$_SESSION['var_msg'] = 'Promo Code Updated Successfully.';
		}
		header("Location:".$backlink);exit;
  }
}
// for Edit

if ($action == 'Edit') {
     $sql = "SELECT * FROM " . $tbl_name . " WHERE iCouponId = '" . $iCouponId . "'";
     $db_data = $obj->MySQLSelect($sql);
     //$vPass = $generalobj->decrypt($db_data[0]['vPassword']);
     $vLabel = $id;
     if (count($db_data) > 0) {
          foreach ($db_data as $key => $value) {
               $vCouponCode = $value['vCouponCode'];
               $fDiscount = $value['fDiscount'];
               $eType = $value['eType'];
               $eValidityType = $value['eValidityType'];
               $dActiveDate = $value['dActiveDate'];
               $dExpiryDate = $value['dExpiryDate'];
               $iUsageLimit = $value['iUsageLimit'];
               $iUsed = $value['iUsed'];
               $eStatus = $value['eStatus'];
               $tDescription = $value['tDescription'];
              //  $vCurrencyDriver=$value['vCurrencyDriver'];
               
          }
     }
}

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
<meta charset="UTF-8" />
<title>Admin | PromoCode <?= $action; ?> </title>
<meta content="width=device-width, initial-scale=1.0" name="viewport" />

<link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
<?php  include_once('global_files.php'); ?>
<!-- On OFF switch -->
<link href="../assets/css/jquery-ui.css" rel="stylesheet" />
<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />

</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53">
<!-- MAIN WRAPPER -->
<div id="wrap">
  <?php 
               include_once('header.php');
               include_once('left_menu.php');
               ?>
  <!--PAGE CONTENT -->
  <div id="content">
    <div class="inner">
      <div class="row">
        <div class="col-lg-12">
          <h2>
            <?= $action; ?>
            Promo Code
            <?php if(isset($vName)) echo $vName; ?>
          </h2>
          <a href="javascript:void(0);" class="back_link">
          <input type="button" value="Back to Listing" class="add-btn">
          </a> </div>
      </div>
      <hr />
      <div class="body-div coupon-action-part">
        <div class="form-group"> 
        <span style="color:red; font-size:small;" id="coupon_status"></span>
          <?php  if ($success == 3) {?>
          <div class="alert alert-danger alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
			<?php  print_r($error); ?>
           </div>
          <br/>
          <?php  } ?>
          <form name="_coupon_form" id="_coupon_form" method="post" action="" enctype="multipart/form-data" class="">
            <input type="hidden" name="iCouponId" value="<?php  if(isset($db_data[0]['iCouponId'])){echo $db_data[0]['iCouponId'];} ?>">
			<input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
			<input type="hidden" name="backlink" id="backlink" value="admin.php"/>
			<input type="hidden" name="vCouponCodeval" id="vCouponCodeval" value="<?= $vCouponCode; ?>"/>
			
            <div class="row coupon-action-n1">
              <div class="col-lg-12">
                <label>Coupon Code :<span class="red"> *</span></label>
              </div>
              <div class="col-lg-6">
                <input type="text" class="form-control" name="vCouponCode" <?php  if ($action == 'Edit') { echo "readonly" ; }else{?>  <?php }?> id="vCouponCode" value="<?= $vCouponCode; ?>" placeholder="Coupon Code" maxlength="10">
                <?php  if ($action == 'Edit') {}else{?>
				<a style="margin: 0 !important;" class="btn btn-sm btn-info" onClick="randomStringToInput(this)">Generate Coupon Code</a>
				<?php  }?>
				</div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                <label>Description :<span class="red"> *</span></label>
              </div>
              <div class="col-lg-6">
                <textarea name="tDescription" rows="5" cols="40" class="form-control" id="tDescription" placeholder="Description"><?=$tDescription;?></textarea>
              </div>
            </div>
            <div class="row coupon-action-n2">
              <div class="col-lg-12">
                <label>Discount :<span class="red"> *</span></label>
              </div>
              <div class="col-lg-6">
                <input type="text" class="form-control" name="fDiscount" id="fDiscount" value="<?= $fDiscount; ?>" placeholder="Discount">
                <select id="eType" name="eType" class="form-control">
                  <option value="percentage" <?php  if(isset($db_data[0]['eType']) && $db_data[0]['eType'] == "percentage"){ ?> selected <?php  }?> >%</option>
                  <option value="cash" <?php  if(isset($db_data[0]['eType']) && $db_data[0]['eType'] == "cash"){ ?>selected <?php  }?> >Flat Amount<!-- <?=$db_currency[0]['vSymbol']?> --></option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                <label>Validity :<span class="red"> *</span></label>
              </div>
              <div class="col-lg-6">
                <input type="radio" name="eValidityType" onClick="showhidedate(this.value)" value="Permanent"
				<?php  if (isset($db_data[0]['eValidityType']) && $db_data[0]['eValidityType'] == "Permanent"){ ?> checked <?php  } ?> >
                Permanent
                <input class="coup-act1" type="radio" name="eValidityType" onClick="showhidedate(this.value)" value="Defined" <?php  if (isset($db_data[0]['eValidityType']) && $db_data[0]['eValidityType'] == "Defined"){?> checked <?php  }?> >
                Custom</div>
            </div>
            <div class="row" id="date1" style="display:none;">
              <div class="col-lg-12" >
                <label>Activation Date :<span class="red"> *</span></label>
              </div>
              <div class="col-lg-6">
                <input type="text" style="float: left;margin-right: 10px; width:45%; cursor: pointer;background: #fff;" class="form-control" name="dActiveDate"  id="dActiveDate" value="<?= $dActiveDate ?>" placeholder="Activation Date" readonly>
              </div>
            </div>
            <div class="row" id="date2" style="display:none;">	
              <div class="col-lg-12">
                <label>Expiry Date:<span class="red"> *</span></label>
              </div>
              <div class="col-lg-6">
                <input type="text" style="float: left;margin-right: 10px; width:45%;cursor: pointer;background: #fff;" class="form-control" name="dExpiryDate" value="<?= $dExpiryDate ?>"  id="dExpiryDate" placeholder="Expiry Date" readonly>
              </div>
            </div>
            <div class="row coupon-action-n3">
              <div class="col-lg-12">
                <label>Usage Limit :<span class="red" > *</span></label>
              </div>
              <div class="col-lg-6">
                <input type="text" id="iUsageLimit" value="<?= $iUsageLimit ?>"  name="iUsageLimit"  placeholder="Usage Limit" class="form-control" onKeyup="checkuserlimit(this.value);" />
                <div id="iUsageLimitmsg"></div>
              </div>             
              
            </div>
            <div class="row coupon-action-n3">
              <div class="col-lg-12">
                <label>Status<span class="red"> *</span></label>
              </div>
              <div class="col-lg-6">
                <select id="eStatus" name="eStatus" class="form-control ">
                  <option value="Active" <?php  if(isset($db_data[0]['eStatus']) && $db_data[0]['eStatus'] == "Active"){ ?>selected <?php  } ?> >Active</option>
                  <option value="Inactive" <?php  if(isset($db_data[0]['eStatus']) && $db_data[0]['eStatus'] == "Inactive"){?>selected <?php  } ?> >Inactive</option>
                </select>
              </div>
            </div>
            <div class="row coupon-action-n4">
              <div class="col-lg-12">
                <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?= $action; ?> PromoCode">
                 <input type="reset" value="Reset" class="btn btn-default">
<!-- 				<a href="javascript:void(0);" <?php  if ($action == 'Edit') {?> onClick="reset_form('_coupon_form'),reset_CouponCode();" <?php  }else{ ?> onClick="reset_form('_coupon_form');"  <?php }?>  class="btn btn-default">Reset</a> -->
                <a href="coupon.php" class="btn btn-default back_link">Cancel</a>
			  </div>
            </div>
          </form>
        </div>
        <div class="clear"></div>
      </div>
    </div>
  </div>
  <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<?php 
          include_once('footer.php');
          ?>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script>
			function validate_coupon(username)
	        {
				var request = $.ajax({
				type: "POST",
				url: 'ajax_validate_coupon.php',
				data: 'vCouponCode=' +username,
				success: function (data)
				{
					if(data==0)
					{
						$('#coupon_status').html('<i class="icon icon-remove alert-danger alert"> 	Coupon Code Already Exist</i>');
						$('input[type="submit"]').attr('disabled','disabled');
						return false;
					}
					else if(data==1)
					{
						$('#coupon_status').html('<i class="icon icon-ok alert-success alert"> Valid</i>');
						$('vCouponCode[type="submit"]').removeAttr('disabled');
					}
					else if(data==2)
					{
						$('#coupon_status').html('<i class="icon icon-remove alert-danger alert"> Please Enter Coupon Code</i>');
						$('vCouponCode[type="submit"]').removeAttr('disabled');
					}
				}
	            });
	        }
		  </script>

<?php  if ($action == 'Edit') { ?>
<script>
	window.onload = function () {
		showhidedate('<?php  echo $eValidityType; ?>');
	};
</script>
<?php }else{ ?>
<script>
	window.onload = function () {     
		$('input:radio[name=eValidityType][value=Permanent]').attr('checked', true);
	};
</script>
<?php  } ?>
<script type='text/javascript' src='../assets/js/jquery-ui.min.js'></script>

<script type="text/javascript">	 
			  var adt = $("#dActiveDate").val();			
              if(adt == '0000-00-00')
			  {
                   $("#dActiveDate").datepicker({					  
					             minDate: 0,                //for avoid previous dates
                       numberOfMonths: 1,
                       dateFormat: "yy-mm-dd",
                       onSelect: function (selected) {
                           var dt = new Date(selected);
                          //dt.setDate(dt.getDate() + 1);
                          dt.setDate(dt.getDate());
                           $("#dExpiryDate").datepicker("option", "minDate", dt);
                       }
                   }).val('');
				   
                   $("#dExpiryDate").datepicker({	
                       minDate: 0,
                       numberOfMonths: 1,
                       dateFormat: "yy-mm-dd",
                       onSelect: function (selected) {
                           var dt = new Date(selected);
                          //dt.setDate(dt.getDate() - 1);
                          dt.setDate(dt.getDate());
                           $("#dActiveDate").datepicker("option", "maxDate", dt);
                       }
                   }).val('');
            }
            else
			{ 		        
			   $("#dActiveDate").datepicker({					  
				   minDate: 0,                //for avoid previous dates
				   numberOfMonths: 1,
				   dateFormat: "yy-mm-dd",
				   onSelect: function (selected) {
					   var dt = new Date(selected);
					  // dt.setDate(dt.getDate() + 1);
             dt.setDate(dt.getDate());
					   $("#dExpiryDate").datepicker("option", "minDate", dt);
				   }
			   });
			   
			   $("#dExpiryDate").datepicker({
           minDate: 0,			   
				   numberOfMonths: 1,
				   dateFormat: "yy-mm-dd",
				   onSelect: function (selected) {
					   var dt = new Date(selected);
					   //dt.setDate(dt.getDate() - 1);
             dt.setDate(dt.getDate());
					   $("#dActiveDate").datepicker("option", "maxDate", dt);
				   }
			   });
			}
            function showhidedate(val){
              if(val == "Defined"){
                 document.getElementById("date1").style.display='';				 
                 document.getElementById("date2").style.display='';
                 document.getElementById("dActiveDate").lang='*';
                 document.getElementById("dExpiryDate").lang='*';
				}
                 else
                 {
                 document.getElementById("date1").style.display='none';
                 document.getElementById("date2").style.display='none';
        				 document.getElementById("dActiveDate").required = false;
        				 document.getElementById("dExpiryDate").required = false;
				
                 document.getElementById("dActiveDate").lang='';
                 document.getElementById("dExpiryDate").lang='';           

                   }
            }
           
function randomStringToInput(clicked_element)
{
    var self = $(clicked_element);
    var random_string = generateRandomString(6);
    $('input[name=vCouponCode]').val(random_string);
    
}
function generateRandomString(string_length)
{
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    var string = '';
    for(var i = 0; i <= string_length; i++)
    {
        var rand = Math.round(Math.random() * (characters.length - 1));
        var character = characters.substr(rand, 1);
        string = string + character;
    }
    return string;
}

$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){
		referrer =  document.referrer;	
	}else { 
		referrer = $("#previousLink").val();
	}
	if(referrer == "") {
		referrer = "coupon.php";
	}else {
		$("#backlink").val(referrer);
	}
	$(".back_link").attr('href',referrer);
});

 function checkuserlimit(userlimit)
{
	if(userlimit != ""){
	    if (userlimit == 0)
	    {		
	        $('#iUsageLimitmsg').html('<i class="icon icon-remove alert-danger alert">You Can Not Enter Zero Number</i>');
	        $('input[type="submit"]').attr('disabled', 'disabled');
	    } else if(userlimit <= 0) {
          $('#iUsageLimitmsg').html('<i class="icon icon-remove alert-danger alert">You Can Not Enter Negative Number</i>');
          $('input[type="submit"]').attr('disabled', 'disabled');
      } else {
	     $('#iUsageLimitmsg').html('');
	    $('input[type="submit"]').removeAttr('disabled');
	    } 
	} else{
		 $('#iUsageLimitmsg').html('');
	} 
                    
}

function reset_CouponCode(){
var vCouponCodeval = $('#vCouponCodeval').val();
$('#vCouponCode').val(vCouponCodeval);
}

</script>
<?php  if ($action != 'Edit'){?>
<script>
	randomStringToInput(document.getElementById("vCouponCode"));
</script>
<?php  }?>
</body>
</html>