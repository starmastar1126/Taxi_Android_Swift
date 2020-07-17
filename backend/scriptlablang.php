<?php 
$con = mysqli_connect('localhost','root','root');
$db_con = mysqli_select_db($con,'uberridedelivery');

$sql = "SELECT * FROM language_label WHERE vValue = ' '";
$db_lang1 = mysqli_query($con,$sql);
$count = mysqli_num_rows($db_lang1);
$cnt = 0;
if($count > 0)
{
	while ( $row = mysqli_fetch_array($db_lang1))
	{
		#echo "<br> id = ".$row['LanguageLabelId'];
		$sql1 = mysqli_query($con,"SELECT * FROM language_label WHERE vLabel = '".$row['vLabel']."' AND vCode = 'EN'");
		$db_lang_v = mysqli_fetch_array($sql1);

		$sql2 = "UPDATE language_label SET vValue = '".$db_lang_v['vValue']."' WHERE LanguageLabelId = ".$row['LanguageLabelId'];
		$db_lang_1 = mysqli_query($con,$sql2);
		$count1 = mysqli_affected_rows($con);
		// $count1 = $obj->GetAffectedRows();
		if($count1 == 1)
		{
			$cnt++;
		}
	}
}
#echo "cnt = ".$cnt;
?>