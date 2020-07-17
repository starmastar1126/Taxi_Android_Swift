<?php  /* ?>-------------------ERROR MODELS START------------------<?php  */ ?>
<!-- Select Error -->
<div data-backdrop="static" data-keyboard="false" class="modal fade" id="is_not_check_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4></h4></div>
            <div class="modal-body"><p>Please Check At Least One Record</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">OK</button></div>
        </div>
    </div>
</div>
<!-- Active Modal -->
<div data-backdrop="static" data-keyboard="false" class="modal fade" id="is_actall_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4>Active Record(s) ?</h4></div>
            <div class="modal-body"><p>Are you sure to activate selected record(s)?</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Not Now</button><a class="btn btn-success btn-ok action_modal_submit" >Yes</a></div>
        </div>
    </div>
</div>
<!-- Inactive Modal -->
<div data-backdrop="static" data-keyboard="false" class="modal fade" id="is_inactall_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4>Inactive Record(s) ?</h4></div>
            <div class="modal-body"><p>Are you sure to inactivate selected record(s)?</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Not Now</button><a class="btn btn-success btn-ok action_modal_submit" >Yes</a></div>
        </div>
    </div>
</div>
<!-- DeleteAll Modal -->
<div data-backdrop="static" data-keyboard="false" class="modal fade" id="is_dltall_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4>Delete Record(s) ?</h4></div>
            <div class="modal-body"><p>Are you sure to delete selected record(s)?</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Not Now</button><a class="btn btn-success btn-ok action_modal_submit" >Yes</a></div>
        </div>
    </div>
</div>

<!-- Delete Single Modal -->
<div data-backdrop="static" data-keyboard="false" class="modal fade" id="is_dltSngl_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4>Delete Record(s) ?</h4></div>
            <div class="modal-body"><p>Are you sure to delete this record?</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Not Now</button><a class="btn btn-success btn-ok action_modal_submit" >Yes</a></div>
        </div>
    </div>
</div>


<div data-backdrop="static" data-keyboard="false" class="modal fade" id="is_dltSngl_modal_cd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4>Delete Record(s) ?</h4></div>
            <div class="modal-body"><p> This company contains drivers under it. If you delete company then all drivers will be assigned to default company. Confirm to delete company?</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Not Now</button><a class="btn btn-success btn-ok action_modal_submit" >Yes</a></div>
        </div>
    </div>
</div>
<!-- Reset Trip Status Modal -->
<div data-backdrop="static" data-keyboard="false" class="modal fade" id="is_resetTrip_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4>Reset Record(s) ?</h4></div>
            <div class="modal-body"><p>Are you sure? You want to reset selected account?</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Not Now</button><a class="btn btn-success btn-ok action_modal_submit">Yes</a></div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="show_export_types_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4>Export to file : </h4></div>
            <div class="modal-body">
                <form name="show_export_modal_form" id="show_export_modal_form" action="" method="post" class="form-horizontal form-box remove-margin" enctype="multipart/form-data">
                    <div class="form-box-content export-popup">
                        <b>Select File type : </b>
                        <span><input type="radio" name="exportType" value="XLS" checked>Excel/CSV</span>
                        <span><input type="radio" name="exportType" value="PDF">PDF</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><a id="export_modal_submit" class="btn btn-primary">Download</a></div>
        </div>
    </div>
</div>

<!-- Export Excel Modal -->
<div class="modal fade" id="show_export_types_modal_excel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4>Export to file : </h4></div>
            <div class="modal-body">
                <form name="show_export_modal_form_excel" id="show_export_modal_form_excel" action="" method="post" class="form-horizontal form-box remove-margin" enctype="multipart/form-data">
                    <div class="form-box-content export-popup">
                        <b>Select File type : </b>
                        <span><input type="radio" name="exportType" value="XLS" checked>Excel/CSV</span>
                        <!-- <span><input type="radio" name="exportType" value="PDF">PDF</span> -->
                    </div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><a id="export_modal_submit_excel" class="btn btn-primary">Download</a></div>
        </div>
    </div>
</div>
<!-- Pay To Driver Modal -->
<div data-backdrop="static" data-keyboard="false" class="modal fade" id="is_payTo_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4>Pay to Driver(s) ?</h4></div>
            <div class="modal-body"><p>Are you sure you want to Pay To Driver(s)?</p></div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Not Now</button><a class="btn btn-success btn-ok action_modal_submit" >Yes</a></div>
        </div>
    </div>
</div>