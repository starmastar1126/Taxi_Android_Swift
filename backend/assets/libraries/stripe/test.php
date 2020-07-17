<?php  

require_once('config.php'); ?>

<form action="charge.php" method="post">
 <input type="hidden" name="vFirstName" value="Test123">
  <script src="https://checkout.stripe.com/checkout.js" 
        class="stripe-button"
        data-key="<?php  echo $stripe['publishable_key']; ?>"
        data-amount="5000" 
        data-email="mehuls.esw@gmail.com">
    </script>
</form>
