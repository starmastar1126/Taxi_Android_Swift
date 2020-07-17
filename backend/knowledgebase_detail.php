<?php 
	include_once("common.php");
	//error_reporting(E_ALL);
	global $generalobj;
	$script="About Us";
	$id = isset($_REQUEST['id'])?$_REQUEST['id']:"";
	$meta = $generalobj->getStaticPage(1,$_SESSION['sess_lang']);
	 $prevlink = isset($_REQUEST['prevlink'])?$_REQUEST['prevlink']:'';
	 $id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
	$sql1 = "select * from helps where  iHelpsId ='".$id."' and eStatus != 'Inactive'"; 
	$db_hepls = $obj->MySQLSelect($sql1);
			
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>
<?=$meta['meta_title'];?>
</title>
<meta name="keywords" value="<?=$meta['meta_keyword'];?>"/>
<meta name="description" value="<?=$meta['meta_desc'];?>"/>
<!-- Default Top Script and css -->
<?php  include_once("top/top_script.php");?>
<!-- End: Default Top Script and css-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript">
   $(document).ready(function()
    {   
        function fill(Value)
        {  
          $('#search').val(Value); 
          $('#display').hide();
        }
        
        $("#search").keyup(function() {  
          var vTitle = $('#search').val(); 
          if (vTitle == "") {   
            $("#display").html("").hide();
          }
        else { 
          $.ajax({  
            type: "POST", 
            url: "knowledgebase_search.php", 
            data: {search:vTitle},  
            success: function(html) { 
              $("#display").html(html).show();
            }
          });
        }
        });
    });
</script>
</head>
<body>
<div id="main-uber-page">
  <!-- Left Menu -->
  <?php  include_once("top/left_menu.php");?>
  <!-- End: Left Menu-->
  <!-- home page -->
  <!-- Top Menu -->
  <?php  include_once("top/header_topbar.php");?>
  <!-- End: Top Menu-->
  <!-- contact page-->
  <div class="page-contant custom-error-page">
    <div class="breadcrumbs">
      <div class="breadcrumbs-inner-a search-a"> <span><a href="knowledgebase.php">Home ><?php  echo $prevlink;?> Panel</a>>Article Detail</span>
       <form action="knowledgebase_search_list.php" method="GET">
         <em><input type="submit" value="Search" style="background:none;"/></em>
           <b><input type="text" id="search" name="search" placeholder="Search"/></b> 
         </form>  
        </div>
    </div>
     <div id="display" style="display:none"></div>
    <div class="page-contant-inner">
      <h2 class="header-page trip-detail">Help</h2>
      <!-- trips detail page -->
      <div class="static-page custom-error-page">
        <div class="custom-error-left-part">
          <ul>
            <li><a href="knowledgebase.php?type=Admin"><img src="assets/img/administrator-panel-icon.png" alt="">Administrator Panel</a></li>
            <li><a href="knowledgebase.php?type=Front"><img src="assets/img/front-panel-icon.png" alt="">Front Panel</a></li>
            <li><a href="knowledgebase.php?type=RiderApp"><img src="assets/img/rider-application-icon.png" alt="">Rider Application</a></li>
            <li><a href="knowledgebase.php?type=DriverApp"><img src="assets/img/driver-application-icon.png" alt="">Driver Application</a></li>
            <li><a href="knowledgebase.php?type=General"><img src="assets/img/driver-application-icon.png" alt="">General</a></li>
          </ul>
        </div>
        <?php  
		 if(!empty($db_hepls)){ ?>
        <div class="custom-error-right-part" id="hlstitle">
          <h3><?php  echo $db_hepls[0]['vTitle']; ?></h3>
          <div class="custom-error-right-part-box">
            <p><?php  echo nl2br($db_hepls[0]['tDescription']); ?></p>
          </div>
        </div>
        <?php   } ?>
      </div>
      <div style="clear:both;"></div>
    </div>
  </div>
  <!-- home page end-->
  <!-- footer part -->
  <?php  include_once('footer/footer_home.php');?>
  <!-- End:contact page-->
  <div style="clear:both;"></div>
</div>
<!-- footer part end -->
<!-- Footer Script -->
<?php  include_once('top/footer_script.php');?>
<!-- End: Footer Script -->
</body>
<script>
	function searchdata(keywords,id){
		
		var request = $.ajax({
				type: "POST",
				url: 'change_searchdata.php',
				data: {keywords: keywords,id:id},
				success: function (dataHtml)
				{
					$('#hlstitle').html(dataHtml);
				}
			});
		}
</script>
</html>
