<?php 
	include_once("common.php");
	
	//error_reporting(E_ALL);
	global $generalobj;

	if (isset($_POST['search']))
	{
         $vTitle = $_POST['search'];
         
         $sql = "SELECT * FROM helps WHERE vTitle LIKE '%".$vTitle."%' ";
	     $db_data = $obj->MySQLSelect($sql);
	    
	    if(count($db_data) > 0)
			{	
					for($i=0;$i<=count($db_data);$i++)
					{ 
						// $str_replace =  $db_data[$i]['vTitle'];
						// $vTitle =  str_replace(" ", "-", $str_replace);
						//&&help_cat_id=".$db_data[$i]['iHelpscategoryId']."
						echo "<h4><a href='knowledgebase_detail.php?id=".$db_data[$i]['iHelpsId']."'>".$db_data[$i]['vTitle']."<br/></a></h4>";
                    }
	        }    
            else
            { 
            	echo 'Result Not Found';
            }
      }
?>
