<?php 
include_once('../common.php');
if($_REQUEST['iCompanyId'] != '')
{
  $iCompanyId = $_REQUEST['iCompanyId'];
}
 
$startDate=$_REQUEST['startDate'];
$iCompanyId = $_REQUEST['iCompanyId'];
$endDate=$_REQUEST['endDate'];
$iDriverId = $_REQUEST['iDriverId'];
$iUserId = $_REQUEST['iUserId'];
$eDriverPaymentStatus = $_REQUEST['eDriverPaymentStatus'];
$vTripPaymentMode = $_REQUEST['vTripPaymentMode'];

if($startDate!=''){
    $ssql.=" AND Date(tEndDate) >='".$startDate."'";
}
if($endDate!=''){
    $ssql.=" AND Date(tEndDate) <='".$endDate."'";
}

if($iCompanyId!=''){
    if($iDriverId!=''){
  $ssql.=" AND tr.iDriverId = '".$iDriverId."' AND rd.iCompanyId = '".$iCompanyId."'";
}else{
  $sql = "select iDriverId from register_driver WHERE iCompanyId = '".$iCompanyId."' ";
      $db_driver2 = $obj->MySQLSelect($sql);
  if(count($db_driver2)>0)
    {
        for($i=0;$i<count($db_driver2);$i++)
        {
             $id.=$db_driver2[$i]['iDriverId'].",";
        }
        $id=rtrim($id,",");
      $ssql.=" AND tr.iDriverId IN($id)";
    }else{
        $ssql.=" AND tr.iDriverId = ''";
    }
}
}else{
if($iDriverId!=''){
      $ssql.=" AND tr.iDriverId = '".$iDriverId."'";
  }
}


if($iUserId!=''){
    $ssql.=" AND tr.iUserId = '".$iUserId."'";
}

if($vTripPaymentMode!=''){
 $ssql.=" AND tr.vTripPaymentMode = '".$vTripPaymentMode."'";
    /*if($vTripPaymentMode == 'Mbirr'){
  $ssql.=" AND tr.vTripPaymentMode = 'Cash' AND eMBirr = 'Yes'";
}else{
  $ssql.=" AND tr.vTripPaymentMode = '".$vTripPaymentMode."'";
}*/  
}

if($eDriverPaymentStatus!=''){
    $ssql.=" AND tr.eDriverPaymentStatus = '".$eDriverPaymentStatus."'";
}

$sql = "SELECT tr.*,c.vCompany,rd.vName,rd.vLastName,rd.vCode,rd.vPhone,rd.vCountry,rd.vCurrencyDriver FROM trips AS tr LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId  WHERE 1 ".$ssql." ORDER BY tr.iTripId DESC";  
$db_trip = $obj->MySQLSelect($sql);

for($i=0;$i<count($db_trip);$i++)
{
	$sql = "SELECT vCountry FROM country WHERE vCountryCode='".$db_trip[$i]['vCountry']."'";
	$db_country= $obj->MySQLSelect($sql);
	$db_trip[$i]['vCountry']=$db_country[0]['vCountry'];
  if($startDate == ""){
    $startDate = date('d-m-Y',strtotime($db_trip[count($db_trip)-1]['tTripRequestDate']));
  }
  if($endDate == ""){
    $endDate = date('d-m-Y',strtotime($db_trip[0]['tTripRequestDate'])); 
  }
  
}
//echo "<pre>";print_r($db_trip);exit;
?>  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <title>Details of Invoice#</title>
       
    </head>  
    <body <?php  if($_REQUEST['iCompanyId'] != '' || $_REQUEST['iDriverId'] != ''){ ?> onload="window.print();" <?php  } ?>>
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">  
            <tr>    
				<td>
                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">      
                        <tr>        
                            <td width="10%"  valign="top" >
                               <!--<img src="../assets/img/logo.png" />-->
                            </td>        
                            <td width="70%" align="center" style="padding-right;FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif;">
                            </td>        
                            <td width="20%">&nbsp;<h1><?=$COMPANY_NAME?></h1></td>      
                        </tr>
                        <tr><td colspan="3" style="border-bottom: 1px solid #000000;" height="20">&nbsp;</td></tr>   
                        <!--<tr><td colspan="3">&nbsp;</td></tr>-->   
                        <tr>        
                            <td width="100%" colspan="3" align="center" style="text-align: center;padding-right;FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif;">
                            <h2><u>STATEMENT</u></h2></td>        
                        </tr>
                        <tr><td colspan="3" height="25"><strong>To,</strong></td></tr>      
                        <tr>        
                            <td colspan="3" align="right" valign="top" style="FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif;">          
                                <table  width="100%" cellpadding="0" cellspacing="0" >            
                                    <tr>              
                                        <td  width="50%" class="" align="left">
                                            <!--<table width="100%" style="border-top: 1px solid #000000; border-left: 1px solid #000000;" cellpadding="0" cellspacing="0">-->
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                  <!--<td height="30" style="padding-left:4px;FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Merchant Detail</td>-->
                                                 </tr>
                                                <tr>
                                                 <td> 
                                                                           
                        														
                        														<?php 
                        															if($iDriverId !=''){
                        															echo $generalobjAdmin->clearName($db_trip[0]['vName']." ".$db_trip[0]['vLastName']);
																					echo ",<br/>"; 
                        															}
                        															?>
                        															
                                                                                <?=$db_trip[0]['vCompany'];?>
                        														<?php  if($db_trip[0]['vCountry'] != '') { ?>,<br />	                         
                                                                                <strong><?php  echo $db_trip[0]['vCountry']; }?></strong>                                              
                                                                            </td>
                                                  </tr>
                                               </table>                                        
                                        </td> 
                                        <td width="50%"  style=" FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif;" align="right" valign="top">
                                            <table border="0" width="50%" style="border-top: 0px solid #000000; border-left: 0px solid #000000;" cellpadding="0" cellspacing="0">
                                                <tr></tr>
                                                <tr>
                      													<td><strong>Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>	 <?php  echo $date=date('d-M-Y');?><br/>
                      														<strong>Currency:&nbsp;&nbsp;</strong>	<?php  echo $db_trip[0]['vCurrencyDriver'];?><br/>
                      														<strong>Mobile No:</strong>	<?php  echo "(".$db_trip[0]['vCode'].")";  echo"  - ".$db_trip[0]['vPhone'];?>
                      												</td></tr>
                                             </table>                                        
                                        
                                        </td>            
                                    </tr>            
                                    
                                    <tr>              
                                        <td colspan="2" align="left">
                                        <strong>Billed Period</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        Date From&nbsp;&nbsp;&nbsp;
                                        <?=$startDate;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        Date To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?=$endDate;?> 
                                        </td>            
                                    </tr> 
                                    <tr>              
                                        <td colspan="2" align="right"> 
                                        </td>            
                                    </tr>  
                                    <tr>              
                                        <td colspan="2" align="right"> 
                                        </td>            
                                    </tr>         
                                </table>
							</td>      
                        </tr>   
                        
						   
                        <tr>        
                            <td colspan="3" height="25" style="border-bottom: 1px solid #000000;">&nbsp;</td>      
                        </tr>  
						           <tr>
                          <td colspan="3" style="border-left: 1px solid #000000;">
							  <table border="0" width="100%" style=="border-top: 1px solid #000000; border-left: 1px solid #000000;" align="left" cellpadding="3" cellspacing="0">          
                                    <tr>      
                                        <th width="15%" height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']?> No</th>
                                        <th width="15%" height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']?> Name</th>
                                        <!--<th height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?php  echo $langage_lbl_admin['LBL_RIDER_TXT_ADMIN']?> Name</th>-->
                                        <th width="15%" height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']?> Date</th>
                                                                                <!--<th>Address</th>-->
                                        <th width="15%" height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Total Fare</th>
                                        <th width="15%" height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Plateform Fees</th>
                                        <th width="15%" height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Promo Code Discount</th>
										<th width="15%" height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Wallet Debit</th>
                                        <th width="10%" height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']?> pay Amount</th>
                                        <!--<th height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']?> Status</th>
                                        <th height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Payment method</th>
                                        <th height="35" align="center" style="FONT-WEIGHT: bold; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']?> Payment Status</th>-->          
                                    </tr>
                                    <?php 
                                      if(count($db_trip) > 0){
                                        $tot_fare = 0.00;
                                        $tot_site_commission = 0.00;
                                        $tot_promo_discount = 0.00;
                                        $tot_driver_refund = 0.00;
										$tot_wallentPayment = 0.00;
                                        
                                        for($i=0;$i<count($db_trip);$i++)
                                        {
                                          $sq="select concat(vName,' ',vLastName) as drivername from register_driver where iDriverId='".$db_trip[$i]['iDriverId']."'";
                                          $name=$obj->MySQLSelect($sq);
                                          
                                          $db_trip[$i]["drivername"]=$name[0]["drivername"];
                                          $totalfare = $db_trip[$i]['fTripGenerateFare'];
                                          $site_commission = $db_trip[$i]['fCommision'];
                                          $promocodediscount = $db_trip[$i]['fDiscount'];
										  $wallentPayment = $db_trip[$i]['fWalletDebit'];
                                          $driver_payment = $totalfare+$promocodediscount-$site_commission;
                                          
                                          $tot_fare = $tot_fare+$totalfare;
                                          $tot_site_commission = $tot_site_commission+$site_commission;
                                          $tot_promo_discount = $tot_promo_discount+$promocodediscount;
										  $tot_wallentPayment = $tot_wallentPayment+$wallentPayment;
                                          $tot_driver_refund = $tot_driver_refund+$driver_payment;
                                         
                                             $paymentmode = $db_trip[$i]['vTripPaymentMode'];
                                             
                                             $sq="select concat(vName,' ',vLastName) as passanger from register_user where iUserId='".$db_trip[$i]['iUserId']."'";
                                          $name2=$obj->MySQLSelect($sq);
                                           
                                          $db_trip[$i]["passanger"]=$name2[0]["passanger"];
                                        ?>
                                    <tr>      
                                       <td valign="top" style="text-align:center;FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?=$db_trip[$i]['vRideNo'];?></td>      
                                        <td valign="top" style="text-align:center;FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?=$generalobjAdmin->clearName($db_trip[$i]['drivername']);?></td> 
                                        <!--<td valign="top" style="text-align:center;FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?=$generalobjAdmin->clearName($db_trip[$i]['passanger']);?></td>--> 
                                        <td valign="top" style="text-align:center;FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?= date('d-m-Y',strtotime($db_trip[$i]['tTripRequestDate']));?></td> 
                                        <td valign="top" style="text-align:center;FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?php  if($db_trip[$i]['fTripGenerateFare'] != "" && $db_trip[$i]['fTripGenerateFare'] != 0) { echo $generalobj->trip_currency($db_trip[$i]['fTripGenerateFare']); }else { echo '-'; }?></td> 
                                        <td valign="top" style="text-align:center;FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?php  if($db_trip[$i]['fCommision'] != "" && $db_trip[$i]['fCommision'] != 0) { echo $generalobj->trip_currency($db_trip[$i]['fCommision']); }else { echo '-'; } ?></td> 
                                        <td valign="top" style="text-align:center;FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?php  if($db_trip[$i]['fDiscount'] != "" && $db_trip[$i]['fDiscount'] != 0) { echo $generalobj->trip_currency($db_trip[$i]['fDiscount']); }else { echo '-'; }?></td>
										<td valign="top" style="text-align:center;FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?php  if($db_trip[$i]['fWalletDebit'] != "" && $db_trip[$i]['fWalletDebit'] != 0) { echo $generalobj->trip_currency($db_trip[$i]['fWalletDebit']); }else { echo '-'; }?></td>
                                        <td valign="top" style="text-align:center;FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?php  if($driver_payment != "" && $driver_payment != 0) { echo $generalobj->trip_currency($driver_payment); }else { echo '-'; }?></td> 
                                        <!--<td valign="top" style="FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?=$db_trip[$i]['iActive'];?></td>  
                                        <td valign="top" style="FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?=$paymentmode;?></td>  
                                        <td valign="top" style="FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;" height="25"><font class=gmattersmallest><?=$db_trip[$i]['eDriverPaymentStatus'];?></td>-->    

                                    </tr>
                                    <?php  } ?>
                                    <tr>                                            
                                        <td colspan="7" width="10%" align="right" style="FONT-WEIGHT: bold; FONT-SIZE: 16px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Total Fare</td>
                                        <td width="10%" align="right" style="FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?=$generalobj->trip_currency($tot_fare);?></td> 
                                    </tr>	

                                     <tr>                                            
                                        <td colspan="7" width="10%" align="right" style="FONT-WEIGHT: bold; FONT-SIZE: 16px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Total Platform Fees</td>
                                        <td width="10%" align="right" style="FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?=$generalobj->trip_currency($tot_site_commission);?></td> 
                                    </tr>

                                    <tr>                                            
                                        <td colspan="7" width="10%" align="right" style="FONT-WEIGHT: bold; FONT-SIZE: 16px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Total Promo Discount</td>
                                        <td width="10%" align="right" style="FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?=$generalobj->trip_currency($tot_promo_discount);?></td> 
                                    </tr>

									<tr>                                            
                                        <td colspan="7" width="10%" align="right" style="FONT-WEIGHT: bold; FONT-SIZE: 16px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Total Wallet Debit</td>
                                        <td width="10%" align="right" style="FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?=$generalobj->trip_currency($tot_wallentPayment);?></td> 
                                    </tr>									


                                    <tr>                                            
                                        <td colspan="7" width="10%" align="right"  style="FONT-WEIGHT: bold; FONT-SIZE: 16px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;">Total Driver Payment</td>
                                        <td width="10%" align="right" style="FONT-WEIGHT: normal; FONT-SIZE: 17px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><?=$generalobj->trip_currency($tot_driver_refund);?></td> 
                                    </tr>
                                    <?php }?>		
                                </table>
                          </td>
                        </tr>
                        
                        <tr>
                          <td colspan="3" style="border-left: none;">
							               <table border="0" width="100%" style=="border-top: 1px solid #000000; border-left: 1px solid #000000;" align="left" cellpadding="3" cellspacing="0">
                               <tr>                                            
                                        <td colspan="6" width="10%" align="right" style="border-left:none;"></td>
                                        <td width="10%"></td> 
                                    </tr>
									                  <tr>                                            
                                        <td colspan="6" width="10%" align="right" style="border-left:none;"></td>
                                        <td width="10%"></td> 
                                    </tr>  
                                    <tr>                                            
                                        <td colspan="6" width="10%" align="right" style="border-left:none;"></td>
                                        <td width="10%"></td> 
                                    </tr>  
                             </table>
                          </td>
                         </tr>     
                                   
                    </table>
					</td>  
            </tr>            
        </table>
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style=="border-top: 1px solid #000000; border-left: 1px solid #000000;">  
            <tr>    
    				<td colspan="11" >
            </td>
            </tr>
            <tr> 
            <td></td>
            <td style="text-align:center;FONT-WEIGHT: normal; FONT-SIZE: 34px; COLOR: #000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif;border-top: 1px solid #000000; border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;">Balance Owing
            </td>   
    				<td colspan="9" style="text-align:center;FONT-WEIGHT: normal; FONT-SIZE: 34px; COLOR: #FF0000; FONT-FAMILY: tahoma, Verdana, Arial, Helvetica, sans-serif;border-top: 1px solid #000000;border-top: 1px solid #000000; border-left: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;"><?=$generalobj->trip_currency($tot_site_commission);?>
            </td></tr>
        </table>
    </body>
</html>
<?php  if($_REQUEST['iMerchantPurchPlanId'] != ''){  exit; } ?>