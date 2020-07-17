<script type="text/javascript" src="assets/js/amazingcarousel.js"></script>
<script type="text/javascript" src="assets/js/initcarousel.js"></script>
<!-- css -->
<link rel="stylesheet" type="text/css" href="assets/css/animate.css">
<link rel="stylesheet" type="text/css" href="assets/css/gallery.css"/>
<!-- js -->
<script type="text/javascript" src="assets/js/jquery-1.11.0.js"></script>
<script type="text/javascript" src="assets/js//waypoints.min.js"></script>
<script type="text/javascript">//<![CDATA[ 
        $(function(){
            function onScrollInit( items, trigger ) {
                items.each( function() {
                var osElement = $(this),
                    osAnimationClass = osElement.attr('data-os-animation'),
                    osAnimationDelay = osElement.attr('data-os-animation-delay');
                  
                    osElement.css({
                        '-webkit-animation-delay':  osAnimationDelay,
                        '-moz-animation-delay':     osAnimationDelay,
                        'animation-delay':          osAnimationDelay
                    });
                    var osTrigger = ( trigger ) ? trigger : osElement;
                    
                    osTrigger.waypoint(function() {
                        osElement.addClass('animated').addClass(osAnimationClass);
                        },{
                            triggerOnce: true,
                            offset: '100%'
                    });
                });
            }
            onScrollInit( $('.os-animation') );
            onScrollInit( $('.staggered-animation'), $('.staggered-animation-container') );
});//]]>  
    </script>
<!-- -->

<div class="home-hero-page">
  <div class="home-hero-page-left" style="background: rgba(0, 0, 0, 0) url('<?=$tconfig["tsite_upload_page_images"].$homepage_banner["vImage"];?>') no-repeat scroll center top / cover">
    <div class="home-hero-page-left-text"> <span> <a href="sign-up"><em><?php  echo $langage_lbl['LBL_HOME_SIGN_UP'];?></em></a>
      <p><?php  echo $langage_lbl['LBL_HOME_DRIVER_COMPANY_TXT'];?></p>
      </span> </div>
  </div>
  <div class="home-hero-page-right">
    <div class="home-hero-page-right-text"> <span>
      <p>
        <?=$langage_lbl['LBL_HOME_RIDING_TXT']; ?>
      </p>
      <a href="sign-up-rider"><em>
      <?=$langage_lbl['LBL_HOME_SIGN_UP']; ?>
      </em></a> </span> </div>
  </div>
</div>
<!-- End: Second Section -->
<!-- Third Section -->
<div class="tap-app-ride">
  <div class="tap-app-ride-inner">
    <h2><?php  echo $meta1['page_title'];?></h2>
    <?php  echo $meta1['page_desc'];?>
    <div style="clear:both;"></div>
  </div>
</div>
<!-- End: Third Section -->
<div class="home-body-mid-part">
  <div class="home-body-mid-part-inner">
    <ul>
      <li>
        <div class="home-body-mid-img"><img src="assets/img/home-box1.jpg" alt="" /></div>
        <h3>Ever Ready, Anytime, Anywhere, Everyday</h3>
        <p>Wherever you want to go, irrespective of time, date and place, we are always ready. No waiting charges, no reservations, just our professionalism and courteous service.</p>
        <p>At your service 24/7, we take pride in what we do and it reflects in our driving. Building a relationship with clients that goes beyond professionalism.</p>
      </li>
      <li>
        <div class="home-body-mid-img"><img src="assets/img/home-box2.jpg" alt="" /></div>
        <h3>Luxury At Your Doorstep At Your Choice</h3>
        <p>Budgeted or luxury, we will have the ride of your choice. Go shopping in a mini and enjoy a limousine ride to your office party. Be spoilt for choice!</p>
        <p>Make your travelling experience a memorable one, we bring you the best on-demand cabbing experience. Pure comfort, luxury and style - the ultimate dream ride!</p>
      </li>
      <li>
        <div class="home-body-mid-img"><img src="assets/img/home-box3.jpg" alt="" /></div>
        <h3>Just For You, Just Like You</h3>
        <p>Ordinary people just like you and me are driving you places. Any city, big or small, any profession high paying or manual, whatever you do, wherever you are, can drive for us. From homemakers to students, from engineers to teachers, is all part of our driving family!</p>
      </li>
    </ul>
    <div style="clear:both;"></div>
  </div>
</div>
<!-- -->
<div class="home-mobile-app">
  <div class="home-mobile-app-inner">
    <div class="home-mobile-app-left os-animation" data-os-animation="fadeInLeft" data-os-animation-delay="0.2s">
       <img src="assets/img/mobile-img.png" alt=""></div>
       <div class="home-mobile-app-right os-animation" data-os-animation="fadeInRight" data-os-animation-delay="0.2s">
       <h3>Lorem Ipsum content<b>Ipsum content goes</b></h3>
       <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic.</p>
       <span><a href="#"><em>more info</em></a></span>
      </div>
      
    
    <div style="clear:both;"></div>
  </div>
</div>
<!-- -->
<div class="home-page-map">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d117506.98606137399!2d72.5797426!3d23.020345749999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m3!3e6!4m0!4m0!5e0!3m2!1sen!2sin!4v1484713395098" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
</div>
<!-- -->
<div class="get-fare-estimation">
<div class="get-fare-estimation-inner">
<div class="get-fare-estimation-left">
<h3>Get a Fare Estimation</h3>
<span>
<b>
<input name="" type="text" placeholder="Add Pickup Location" class="trip-start" />
<input name="" type="text" placeholder="Add Destination Location" class="trip-end" />
</b>
<a href="#"><em>calculate</em></a>
</span>
</div>
<div class="get-fare-estimation-right">
<div class="get-fare-estimation-right-inner">
<span>uberX<b>&#x20B9; 6,197-8,271</b></span>
<span>uberGO<b>&#x20B9; 6,197-8,271</b></span>
<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since.</p>
<strong><a href="#"><em>Sign up to Ride</em></a></strong>
</div>
</div>
<div style="clear:both;"></div>
</div>
</div>
<!-- -->
<div class="taxi-app">
<div class="taxi-app1">
<div class="taxi-app-inner">
<div class="taxi-app-right-part os-animation" data-os-animation="fadeInRight" data-os-animation-delay="0.2s">
<h3>Lorem Ipsum content</h3>
<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever, when an unknown printer took a galley of type and scrambled it to make a type specimen book. </p>
<span><a href="#"><em>reason to drive</em></a></span>
</div>
<div style="clear:both;"></div>
</div>
</div>
</div>
<!-- -->
<div class="gallery-part">
  <div class="gallery-page">
  <h2>Meet Our Drivers</h2>
  <em>Lorem Ipsum content goes here and your lorem text here</em>
    <div id="box-2" class="box"> <b><img id="image-1" src="assets/img/promotions-img1.jpg"/></b><span class="caption full-caption">
      <h3><strong>Lorem Ipsum content</strong>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
        <p>when an unknown printer took a galley of type and scrambled it to make a type.</p>
      </h3>
      </span></div>
    <div id="box-2" class="box"> <b><img id="image-1" src="assets/img/promotions-img2.jpg"/></b><span class="caption full-caption">
     <h3><strong>Lorem Ipsum content</strong>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
        <p>when an unknown printer took a galley of type and scrambled it to make a type.</p>
      </h3>
      </span></div>
    <div id="box-2" class="box"> <b><img id="image-1" src="assets/img/promotions-img3.jpg"/></b><span class="caption full-caption">
      <h3><strong>Lorem Ipsum content</strong>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
        <p>when an unknown printer took a galley of type and scrambled it to make a type.</p>
      </h3>
      </span></div>
    <div id="box-2" class="box"> <b><img id="image-1" src="assets/img/promotions-img1.jpg"/></b><span class="caption full-caption">
      <h3><strong>Lorem Ipsum content</strong>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
        <p>when an unknown printer took a galley of type and scrambled it to make a type.</p>
      </h3>
      </span></div>
  </div>
</div>
<!-- -->
<div class="home-map"></div>
<!-- -->
