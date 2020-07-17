<?php 
 ob_start();
	include_once('../common.php');
	if(!isset($generalobjAdmin))
	{
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
  
	$success=(isset($_REQUEST['success']))?$_REQUEST['success']:-1;
	$sql = "SELECT * FROM currency WHERE eStatus = 'Active' order by iDispOrder";
	$db_currency = $obj->MySQLSelect($sql);
	$vName="SELECT vName FROM currency WHERE eStatus = 'Active' order by iDispOrder";
	$db_vName=$obj->MySQLSelect($vName);
	for($i=0;$i<count($db_vName);$i++)
	{
		$db_name[$i]=$db_vName[$i]["vName"];
	}
	$script 	= 'Currency';
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
<meta charset="UTF-8" />
<title>
<?=$SITE_NAME;?>
| Currency</title>
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
<?php  include_once('global_files.php');?>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53">
<!-- MAIN WRAPPER -->
<div id="wrap">
  <?php  include_once('header.php'); ?>
  <?php  include_once('left_menu.php'); ?>
  <!--PAGE CONTENT -->
  <div id="content">
    <div class="inner">
      <div id="add-hide-show-div">
        <div class="row">
          <div class="col-lg-12">
            <h2>Currency</h2>
            <!-- <input type="button" id="show-add-form" value="ADD A DRIVER" class="add-btn">
								<input type="button" id="cancel-add-form" value="CANCEL" class="cancel-btn"> -->
          </div>
        </div>
        <hr />
      </div>
      <div style="clear:both;"></div>
      <?php  if ($success == 1) {?>
      <div class="alert alert-success alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        Currency Updated successfully. </div>
      <br/>
      <?php }
					else if($success == 2)
					{
					?>
      <div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you. </div>
      <br/>
      <?php 
						}
					?>
      <div class="table-list">
        <div class="row">
          <div class="col-lg-12">
            <div class="table-responsive">
              <form action="currency_action.php" method="post" id="formId">
                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                  <thead>
                    <tr>
                      <th>Currency</th>
                      <th>Ratio</th>
                      <th>Threshold Amount <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='<?= htmlspecialchars('Currency Wise Minimum Payment Drivers can Request from Website Driver Account to Admin.', ENT_QUOTES, 'UTF-8') ?>'></i></th>
                      <th>Symbol</th>
                      <th>Default</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php   foreach ($db_currency as $key => $value) {
															echo '<tr>
																	<td><input class="form-control" type="hidden" name="iCurrencyId[]" value="'.$value['iCurrencyId'].'" />'.$value["vName"].'</td>
																	<td><input class="form-control" name="Ratio[]" id="ratio_'.$value['iCurrencyId'].'" type="text" value='.$value['Ratio'].' required/></td>
																	<td><input class="form-control" name="fThresholdAmount[]" type="text" value='.$value['fThresholdAmount'].' /></td>
																	<td><input  class="form-control" name="vSymbol[]" type="text" value='.$value['vSymbol'].' required/></td>';
																	
																	$eDefault = "";
																	if($value['eDefault'] == "Yes")
																	{
																		$eDefault = " checked ";
																	}
																	else
																	{
																		$eDefault = "";
																	}
															echo '<td><input  class="form-control" name="eDefault" id="eDefault_'.$value['iCurrencyId'].'" type="radio" value="'.$value['iCurrencyId'].'" '.$eDefault.' /></td>';
															
															echo '</tr>';
													}
												?>
                    <tr>
                      <td colspan="5" align="center"><input type="submit" name="btnSubmit" class="btn btn-default" value="Edit currency"></td>
                    </tr>
                  </tbody>
                </table>
              </form>
            </div>
          </div>
          <!--TABLE-END-->
        </div>
      </div>
    </div>
  </div>
  <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<?php 
			include_once('footer.php');
		?>
<script src="../assets/plugins/dataTables/jquery.dataTables.js"></script>
<script src="../assets/plugins/dataTables/dataTables.bootstrap.js"></script>
<!--<script>
			$(document).ready(function () {
				$('#dataTables-example').dataTable();
			});
		</script>-->
    <script type="text/javascript">
      $("form").submit(function(event){
         event.preventDefault();
          var value = $( 'input[name=eDefault]:checked' ).val();
          var ratio = $('#ratio_'+ value).val();
          if(ratio == 1){
            $('#formId').get(0).submit();
          } else {
            alert("Please change euro currency ratio to 1.0000 since your making it as default. Also adjust other currency ratio as per euro.");
            return false;
          }
      });
    </script>
</body>
<!-- END BODY-->
</html>