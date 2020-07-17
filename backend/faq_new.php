<?php 
	include_once("common.php");
	error_reporting(E_ALL);
	global $generalobj;
	
	$meta = $generalobj->getStaticPage(1,$_SESSION['sess_lang']);
	 //echo "<pre>";print_r($_);exit;
	 $ssql='';
	 $iFaqcategoryId=isset($_REQUEST['id'])?$_REQUEST['id']:'1';
	 $Type=isset($_REQUEST['type'])?$_REQUEST['type']:'General';
	 if($iFaqcategoryId!="")
	 {
		 $ssql=$ssql."AND iFaqcategoryId='".$iFaqcategoryId."'";
	 }
	 
	
    $sql = "SELECT iFaqId,iFaqcategoryId,vTitle_".$_SESSION['sess_lang']." as Que ,tAnswer_".$_SESSION['sess_lang']." as Ans FROM faqs WHERE eStatus='Active' $ssql ORDER BY iFaqId";
	$db_faqs = $obj->MySQLSelect($sql);
	//echo "<pre>";print_r($db_faqs);exit;
	
	$sql = "SELECT * FROM faq_categories WHERE vCode='".$_SESSION['sess_lang']."'";
	$db_faq_categories = $obj->MySQLSelect($sql);
	//echo "<pre>";print_r($db_faq_categories);exit;
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$COMPANY_NAME?> | <?=$langage_lbl['LBL_FAQs']; ?></title>
    <!-- Default Top Script and css -->
    <?php  include_once("top/top_script.php");?>
    <?php  include_once("top/validation.php");?>
    <!-- End: Default Top Script and css-->
	<script type="text/javascript" src="assets/js/script.js"></script>
</head>
<body>
    <!-- home page -->
    <div id="main-uber-page">
    <!-- Left Menu -->
    <?php  include_once("top/left_menu.php");?>
    <!-- End: Left Menu-->
        <!-- Top Menu -->
        <?php  include_once("top/header_topbar.php");?>
        <!-- End: Top Menu-->
        <!-- contact page-->
        <div class="page-contant">
            <div class="page-contant-inner">
                <h2 class="header-page"><?=$langage_lbl['LBL_FAQs']; ?><</h2>
                <!-- contact page -->
                  <div class="faq-page">
                  <div class="faq-top-part">
				 <ul>
                
					<?php  
						if(count($db_faq_categories)>0)
						{
							for($i=0;$i<count($db_faq_categories);$i++)
							
							{ ?>
								<li <?php  if(trim($Type)==trim($db_faq_categories[$i]['vTitle'])){?>class="Active" <?php }?>>
									<a href="javascript:void(0);" onClick="getFaqs('<?php  echo $db_faq_categories[$i]['vTitle'];?>',<?=$db_faq_categories[$i]['iUniqueId'];?>)"><?php echo $db_faq_categories[$i]['vTitle'];?></a>
								</li>
							<?php 
							}
						}
					?>
				</ul>
                </div>
                <div class="faq-bottom-part" id='cssmenu'>
				  <ul>
								<?php  
									for($i=0;$i<count($db_faqs);$i++)
										
									//echo "<pre>";print_r($db_faqs);exit;
								{?>
											<li class='has-sub'>
												<a href="#" class="faq-q">
													<span>
													<b><?=$langage_lbl['LBL_Q']; ?></b>
													<h3><?=$db_faqs[$i]['Que'];?></h3>
													</span>
													</a>
													<ul class="faq-ans"  style="display:none">
														<li id="faq_<?=$db_faqs[$i]['iFaqId']?>">
															<span>  <?=$db_faqs[$i]['Ans'];?></span>
														</li>
													</ul>
											</li>
											
								 <?php }?>
						 </ul>
						 </div>
					</div>
                <div style="clear:both;"></div>
            </div>
			<form name="faq" id="faq" action="">
	 
			   <input type="hidden" name="id" id="iUniqueId"  value="">
			   <input type="hidden" name="type" id="CatName"  value="">
			</form>
        </div>
    <!-- footer part -->
    <?php  include_once('footer/footer_home.php');?>
    <!-- footer part end -->
            <!-- End:contact page-->
            <div style="clear:both;"></div>
    </div>
    <!-- home page end-->
    <!-- Footer Script -->
    <?php  include_once('top/footer_script.php');?>
    
    <script type="text/javascript">
		
		function FacdeQuestion(id)
			{
				
				
					if($("#faq_"+id).is( ":visible" )){
						$("#faq_"+id).slideToggle("slow");
						
						}else{
						$("#faq_"+id).slideToggle("slow");
						
				}
			}
	
	 function getFaqs(cat,id)
		{
			//alert(cat+" "+id);
			$("#iUniqueId").val(id);
			$("#CatName").val(cat);
			document.faq.submit();
		}
		
		
    </script>
    <!-- End: Footer Script -->
</body>
</html>
