<html>
<head></head>
<style>
.text_design{font-size:12px; font-weight:bold; font-family:verdana;}
.border_table{border:1px solid #dddddd;}
.no-cursor{cursor:text;}
.table-bordered a{text-decoration:none; color:#ffa523;}
.lolo{margin:0 0 10px; padding:0px; float:left; width:100%; text-align:center; font-size:20px;}
.lolo1{margin:30px 0 10px; padding:0px; float:left; width:100%; text-align:center; font-size:20px;}
.table-bordered1{ margin-bottom:30px;}
</style>
<body>
<?php 
	error_reporting(0);
	include_once('common.php');	
	include_once(TPATH_CLASS.'class.general.php');	
	include_once(TPATH_CLASS.'configuration.php');
	include_once(TPATH_CLASS .'Imagecrop.class.php');
	include_once(TPATH_CLASS .'twilio/Services/Twilio.php');
	include_once('generalFunctions.php');
	global $tconfig;
 
	$thumb 		= new thumbnail;
	$generalobj = new General();
	
	$ToDate = date('Y-m-d');
	$sql1 = "SELECT doc.ex_date,doc.doc_usertype,dm.doc_name as documentname,cmp.vEmail as cmpEmail,cmp.iCompanyId as cmpid,rd.iDriverId as driverid,rd.vEmail,cmp.vCompany as Cmpname, CONCAT(rd.vName,' ',rd.vLastName) as drivername FROM document_list as doc Left Join company as cmp  ON doc.doc_userid = cmp.iCompanyId 
	Left Join register_driver as rd  ON doc.doc_userid = rd.iDriverId 
	Left Join document_master as dm  ON doc.doc_masterid = dm.doc_masterid 
	WHERE doc.ex_date LIKE '%$ToDate%'";
	$data_ex_doc = $obj->MySQLSelect($sql1);
	//echo "<pre>"; print_r($data_ex_doc); exit;
	
	$array_cmp_detail = array();
	$array_driver_detail = array();
	for($i=0;$i< count($data_ex_doc);$i++){
	
		if($data_ex_doc[$i]['doc_usertype'] == "company"){
			
			if(!empty($data_ex_doc[$i]['Cmpname'])){
				$cmplink = $tconfig["tsite_url"].'admin/company_document_action.php?id='.$data_ex_doc[$i]['cmpid'].'&action=edit';
				$array_cmp_detail[] = array('vEmail'=> $data_ex_doc[$i]['cmpEmail'],
												'Companyname'=>$data_ex_doc[$i]['Cmpname'],
												'documentname'=>$data_ex_doc[$i]['documentname'],
												'DocumentLink'=> "<a href='".$cmplink."' target='_blank'>Document Link</a>"
												);				
				
			}		
			
		}
		if($data_ex_doc[$i]['doc_usertype'] == "driver"){
		
			if(!empty($data_ex_doc[$i]['doc_usertype'])){
			
			$link = $tconfig["tsite_url"].'admin/driver_document_action.php?id='.$data_ex_doc[$i]['driverid'].'&action=edit&user_type=driver';
				$array_driver_detail[] = array('vEmail'=> $data_ex_doc[$i]['vEmail'],
												'Drivername'=>$data_ex_doc[$i]['drivername'],
												'Documentname'=>$data_ex_doc[$i]['documentname'],
												'DriverLink'=> "<a href='".$link."' target='_blank'>Document Link</a>"
												);
			}	
			
			
		}
	
		
	}	
	//Driver Message Text
	
	if((count($array_driver_detail) > 0 )|| (count($array_cmp_detail) > 0)){
		if(count($array_driver_detail) > 0){
		
			$driver_message = "";
			$driver_message .= '<table border="1" class="table table-bordered table-bordered1" width="100%" align="center" cellspacing="0" cellpadding="10">
			<tr class="cron-header-name"><b class="lolo">'.$langage_lbl['LBL_EXPIRY_DRIVER_NAME_TXT'].'</b></tr>
			<tr>
			<th>Email</th>
			<th>Name</th>
			<th>Document Name</th>
			<th>Link</th>
			</tr>';
				
			for($j=0; $j<count($array_driver_detail); $j++){
				
				
				$driver_message .='<tr><td>'.$array_driver_detail[$j]['vEmail'].'</td>';
				$driver_message .='<td>'.$array_driver_detail[$j]['Drivername'].'</td>';
				$driver_message .='<td>'.$array_driver_detail[$j]['Documentname'].'</td>';
				$driver_message .='<td>'.$array_driver_detail[$j]['DriverLink'].'</td></tr>';
			}
			$driver_message .='</tabel>';	
			
		}
		
		
		//company message	
		
		if(count($array_cmp_detail) > 0){
		
			$driver_message .= '<table border="1" class="table table-bordered table-bordered1" width="100%" align="center" cellspacing="0" cellpadding="10">
			<tr class="cron-header-name"><b class="lolo1">'.$langage_lbl['LBL_EXPIRY_COMPANY_LIST_TXT'].'</b></tr>
			<tr>
			<th>Email</th>
			<th>Name</th>
			<th>Document Name</th>
			<th>Link</th>	
			</tr>';
			
			for($j=0; $j<count($array_cmp_detail); $j++){
				
				
				$driver_message .='<tr><td>'.$array_cmp_detail[$j]['vEmail'].'</td>';
				$driver_message .='<td>'.$array_cmp_detail[$j]['Companyname'].'</td>';
				$driver_message .='<td>'.$array_driver_detail[$j]['Documentname'].'</td>';
				$driver_message .='<td>'.$array_cmp_detail[$j]['DocumentLink'].'</td></tr>';
			}
			$driver_message .='</tabel>';	
		}
		

		// send Email
		$message = array();
		$message['details'] =$driver_message;
		$mail = $generalobj->send_email_user('CRON_EXPIRY_DOCUMENT_EMAIL',$message);
	}	 
		 
		 
?>

	