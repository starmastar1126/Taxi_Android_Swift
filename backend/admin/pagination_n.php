<?php  if($endRecord > 0) { ?>
<span class="pagin_left">Showing <b><?php  if($total_results > 0) {echo ($start+1); } else { echo 0; } ?></b> to <b><?php  echo ($start+$endRecord); ?></b> of <b><?php  echo $total_results; ?></b> entries</span>
<?php  } ?>
<div class="pagination">
<ul>
<?php  if ($total_pages > 1) {
    echo paginate($reload, $show_page, $total_pages);
} ?>
</ul></div>