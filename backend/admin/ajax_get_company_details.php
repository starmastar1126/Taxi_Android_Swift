<?php 
include_once("../common.php");

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

  $iCompanyId = isset($_POST['companyid'])?$_POST['companyid']:'';
  
  if($iCompanyId != "" || $iCompanyId != "0"){
	  $sql="select cmp.vCompany,cmp.vImage,cmp.vCaddress,cmp.vEmail,cmp.vCadress2,cmp.vCity,cmp.vState,cmp.vZip,cmp.vCode,cmp.vPhone,cmp.eStatus,cmp.vVat,cmp.vCountry,cmp.tRegistrationDate, (select count(dv.iDriverVehicleId) from driver_vehicle dv where dv.iCompanyId=cmp.iCompanyId and dv.eStatus != 'Deleted') as TotalVehicle,(select count(rd.iDriverId) from register_driver rd where rd.iCompanyId=cmp.iCompanyId and rd.eStatus='active') as ActiveDrivers,(select count(rd.iDriverId) from register_driver rd where rd.iCompanyId=cmp.iCompanyId and rd.eStatus='inactive') as InactiveDrivers from company cmp
			where cmp.iCompanyId='$iCompanyId'";
			
	 $data_company_detail = $obj->MySQLSelect($sql);
	 
	 $sql="set @a=0,@b=0,@c=0;SELECT tr.iTripId AS TotalTrips, tr.iActive,(case when tr.iActive='Finished' then @a:=@a+1 when tr.iActive='Canceled' then @b:=@b+1 when tr.iActive='Active' then @c:=@c+1 END) as tot,@a as Finished,@b as Canceled,@c as Active
FROM company cmp
LEFT JOIN register_driver rd ON cmp.iCompanyId = rd.iCompanyId
LEFT JOIN trips tr ON rd.iDriverId = tr.iDriverId
WHERE cmp.iCompanyId =  '10'
		  ";
		   $data_company_trips = $obj->MySQLSelect($sql);
		   
		   echo "<pre>";print_r($data_company_trips);exit;
  }

?>