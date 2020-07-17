<?php 
$con1 = mysqli_connect("localhost", "root", "root");
$con2 = mysqli_connect("localhost", "root", "root",true);

$source_db_name = "uberappufx2017";
$destination_db_name = "uberappufx2017Test";

$selectdb1 = mysqli_select_db($source_db_name, $con1);
$selectdb2 = mysqli_select_db($destination_db_name, $con2);

if(!$selectdb1){
  die("$source_db_name selection error.");
}
if(!$selectdb2){
  die("$destination_db_name selection error");
}

$sql = mysqli_query("UPDATE $source_db_name.configurations,$destination_db_name.configurations SET $destination_db_name.configurations.eType = $source_db_name.configurations.eType, $destination_db_name.configurations.eStatus = $source_db_name.configurations.eStatus, $destination_db_name.configurations.tHelp = $source_db_name.configurations.tHelp, $destination_db_name.configurations.eInputType = $source_db_name.configurations.eInputType, $destination_db_name.configurations.tSelectVal = $source_db_name.configurations.tSelectVal WHERE $destination_db_name.configurations.vName = $source_db_name.configurations.vName");
printf ("Updated Records : %d\n", mysqli_affected_rows());
echo"<br/>";
if($sql) {
	echo "Record Updated Successfully.";
} else {
	echo "There is some problem to update data.";
}
?>
