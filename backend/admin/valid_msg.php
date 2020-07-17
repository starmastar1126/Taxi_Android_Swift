<?php  if(isset($_SESSION['success']) && $_SESSION['success'] == 1) { ?>
<div class="alert alert-success alert-dismissable marginbottom-10 msg-test-001">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
    <?php  echo $_SESSION['var_msg']; unset($_SESSION['var_msg']); unset($_SESSION['success']); ?>
</div>
<?php  }elseif (isset($_SESSION['success']) && $_SESSION['success'] == 2) { ?>
<div class="alert alert-danger alert-dismissable marginbottom-10 msg-test-001">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
    "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
    <?php  unset($_SESSION['success']); ?>
</div>
<?php  }else if(isset($_SESSION['success']) && $_SESSION['success'] == 3) { ?>
<div class="alert alert-danger alert-dismissable marginbottom-10 msg-test-001">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
    <?php  echo $_SESSION['var_msg']; unset($_SESSION['var_msg']); unset($_SESSION['success']); ?>
</div>
<?php  } ?>