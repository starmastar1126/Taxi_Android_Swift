	<div class="get-there">
	<div class="get-there-inner">
		<h2><?php  echo $langage_lbl['HOME_FIRST_LBL'];?><b><?php  echo $langage_lbl['HOME_SECOND_LBL'];?></b></h2>
		<?php 
		  if($user==""){
		?>
		<span>
        <!--<a href="sign-up-rider"><?php  echo $langage_lbl['LBL_HEADER_SIGN_UP_TO_RIDE'];?></a>--->
        <a href="sign-up" class="active"><?php  echo $langage_lbl['LBL_HEADER_BECOME_A_DRIVER'];?></a></span>
		<?php 
			}
		?>
	<div style="clear:both;"></div>
	</div>
</div>