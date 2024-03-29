<div class="footer">
    <div class="footer-inner">
        <div class="footer-top-part">
            <div class="footer-box1">
                <div class="lang">
                    <select name="sess_language" id="sess_language" onchange="change_lang(this.value);" class="custom-select-new1">
                    <?php 
                    $sql="select vTitle, vCode, vCurrencyCode, eDefault from language_master where eStatus='Active'";
                    $db_lng_mst=$obj->MySQLSelect($sql);
                    foreach ($db_lng_mst as $key => $value) {
                        echo '
                            <option value="'.$value['vCode'].'"'.($_SESSION['sess_lang']==$value['vCode']?'selected':'').'>'.$value['vTitle'].'</option>
                        ';
                    }
                    ?>
            </select>
                </div>
                <span>
                    <a href="<?php  echo $FB_LINK_FOOTER;?>"><i class="fa fa-facebook"></i></a> 
                    <a href="<?php  echo $TWITTER_LINK_FOOTER;?>"><i class="fa fa-twitter"></i></a>
                    <a href="<?php  echo $LINKEDIN_LINK_FOOTER;?>"><i class="fa fa-linkedin-square"></i></a>
                    <a href="<?php  echo $INSTAGRAM_LINK_FOOTER;?>"><i class="fa fa-instagram"></i></a>
                    </span> 
            </div>
            <div class="footer-box2">
                <ul>
                    <li><a href="how-it-works"><?=$langage_lbl['LBL_HOW_IT_WORKS']; ?></a></li>
                    <li><a href="trust-safty-insurance"><?=$langage_lbl['LBL_SAFETY_AND_INSURANCE']; ?></a></li>
                    <li><a href="terms-condition"><?=$langage_lbl['LBL_TERMS_AND_CONDITION']; ?></a></li>
					<li><a href="faq"><?=$langage_lbl['LBL_FAQs']; ?></a></li>
                    <!-- <li><a href="#">Blogs</a></li> -->
                </ul>
                <ul>
                    <li><a href="about"><?=$langage_lbl['LBL_ABOUT_US_HEADER_TXT']; ?></a></li>
                    <li><a href="contact-us"><?=$langage_lbl['LBL_CONTACT_US_TXT']; ?></a></li>
                    <li><a href="help-center"><?=$langage_lbl['LBL_HELP_CENTER']; ?></a></li>
                    <li><a href="legal"><?=$langage_lbl['LBL_LEGAL']; ?></a></li>
                </ul>
            </div>
            <div class="footer-box3"> 
                <span>
                    <a href="<?=$ANDROID_APP_LINK?>"><img src="assets/img/app-stor-img.png" alt=""></a>
                </span> 
                <span>
                    <a href="<?=$IPHONE_APP_LINK?>"><img src="assets/img/google-play-img.png" alt=""></a>
                </span> 
            </div>
        </div>
        <div class="footer-bottom-part"> 
            <span>&copy; <?= $COPYRIGHT_TEXT ?></span>
            <!--<p><?=$langage_lbl['LBL_WEBSITE_DESIGN_AND_DEVELOPED_BY']; ?>: <a href="http://v3cube.com" target="_blank">v3cube.com</a></p>-->
        </div>
        <div style=" clear:both;"></div>
    </div>
</div>
<script>
function change_lang(lang){
    document.location='common.php?lang='+lang;
}
</script>


<script type="text/javascript">
    $(document).ready(function(){
        $(".custom-select-new1").each(function(){
            var selectedOption = $(this).find(":selected").text();
            $(this).wrap("<em class='select-wrapper'></em>");
            $(this).after("<em class='holder'>"+selectedOption+"</em>");
        });
        $(".custom-select-new1").change(function(){
            var selectedOption = $(this).find(":selected").text();
            $(this).next(".holder").text(selectedOption);
        });
    })
</script>
