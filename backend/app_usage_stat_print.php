<?php 
include_once('common.php');
$sql = "select iMemberId, count(iMemberId) as totlogin, eMemberType, vIP from member_log where dDateTime >= '".$today." 00:00:00' and dDateTime <= '".$today." 23:59:59' group by iMemberId order by count(iMemberId)";
$db_rec = $obj->MySQLSelect($sql);

for($i=0;$i<count($db_rec);$i++){
	$tot_login = $tot_login+$db_rec[$i]['totlogin'];
}

function getusername($id,$type){
	global $obj;
	if($type == "Passenger"){
		$sql = "SELECT concat(vName,' ',vLastName) as name, vEmail, vCountry from register_user where iUserId='".$id."'";
		$db_user = $obj->MySQLSelect($sql);
		if($db_user[0]['vEmail'] == "rider@gmail.com"){
			return $db_user[0]['name']." (".$db_user[0]['vEmail'].") <font color='red'>[Demo User]</font>";			
		}else{
			return $db_user[0]['name']." (".$db_user[0]['vEmail'].")";
		}
	}

	if($type == "Driver"){
		$sql = "SELECT concat(vName,' ',vLastName) as name, vEmail, vCountry from register_driver where iDriverId='".$id."'";
		$db_user = $obj->MySQLSelect($sql);
		if($db_user[0]['vEmail'] == "driver@gmail.com"){
			return $db_user[0]['name']." (".$db_user[0]['vEmail'].") <font color='red'>[Demo User]</font>";			
		}else{
			return $db_user[0]['name']." (".$db_user[0]['vEmail'].")";
		}

	}
}

function getvisitortype($id){
	global $obj, $today;
	$sql = "SELECT count(iMemberLogId) as tot from member_log where iMemberId='".$id."' and dDateTime < '".$today." 00:00:00'";
	$db_memlog = $obj->MySQLSelect($sql);
	if($db_memlog[0]['tot'] > 0){
		return "Returning";
	}else{
		return "New";		
	}
}

function gettotaltrips($id,$type){
	global $obj;
	if($type == "Passenger"){
		$sql = "SELECT count(iTripId) as tot from trips where iUserId='".$id."'";
	}
	if($type == "Driver"){
		$sql = "SELECT count(iTripId) as tot from trips where iDriverId='".$id."'";
	}
	$db_memtrips = $obj->MySQLSelect($sql);
	return $db_memtrips[0]['tot'];
}

function gettodaytrips($id,$type,$today){
	global $obj;
	if($type == "Passenger"){
		$sql = "SELECT count(iTripId) as tot from trips where iUserId='".$id."' AND tTripRequestDate >= '".$today." 00:00:00' and tTripRequestDate <= '".$today." 23:59:59' ";
	}
	if($type == "Driver"){
		$sql = "SELECT count(iTripId) as tot from trips where iDriverId='".$id."' AND tTripRequestDate >= '".$today." 00:00:00' and tTripRequestDate <= '".$today." 23:59:59' ";
	}
	$db_memtrips = $obj->MySQLSelect($sql);
	return $db_memtrips[0]['tot'];
}
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<h2>Total Logins/Usage Today: <?=$tot_login?></h2>
<table style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;width: 100%;">
<tr>
	<th style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;">Name</th>
	<th style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;">Type</th>
	<th style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;">IP</th>
	<th style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;">Login count of Today</th>
	<th style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;">Visitor Type</th>
	<th style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;">Trips Taken Till Now</th>	
  <th style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;">Trips Taken Today</th>	
</tr>
<?php for($i=0;$i<count($db_rec);$i++){?>
<tr>
	<td style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;"><?php echo getusername($db_rec[$i]['iMemberId'],$db_rec[$i]['eMemberType']);?></td>
	<td style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;"><?=$db_rec[$i]['eMemberType'];?></td>
	<td style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;"><?=$db_rec[$i]['vIP'];?></td>
	<td style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;"><?=$db_rec[$i]['totlogin'];?></td>
	<td style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;"><?=getvisitortype($db_rec[$i]['iMemberId'])?></td>
	<td style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;"><?php echo gettotaltrips($db_rec[$i]['iMemberId'],$db_rec[$i]['eMemberType']);?></td>
  <td style="border: 1px solid #ddd;text-align: left;border-collapse: collapse;padding: 10px;"><?php echo gettodaytrips($db_rec[$i]['iMemberId'],$db_rec[$i]['eMemberType'],$today);?></td>
</tr>
<?php }?>
</table>
</body>
</html>

