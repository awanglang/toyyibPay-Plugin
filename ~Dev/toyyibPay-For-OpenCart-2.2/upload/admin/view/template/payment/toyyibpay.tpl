<?php
echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-toyyibpay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
<?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
<?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
<?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-toyyibpay" class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-3 control-label" for="input-api-key"><span data-toggle="tooltip" title="<?php echo $help_api_key; ?>"><?php echo $toyyibpay_api_key; ?></span></label>
                        <div class="col-sm-9">
                            <input type="text" name="toyyibpay_api_key_value" value="<?php echo $toyyibpay_api_key_value; ?>" placeholder="<?php echo $toyyibpay_api_key; ?>" id="input-mid" class="form-control" />
							<?php if ($error_api_key) { ?>
								<div class="text-danger"><?php echo $error_api_key; ?></div>
                            <?php } ?>
                        </div>
                    </div>
					
					<div class="form-group required">
                        <label class="col-sm-3 control-label" for="input-api-environment"><span data-toggle="tooltip" title="<?php echo $help_api_environment; ?>"><?php echo $toyyibpay_api_environment; ?></span></label>
                        <div class="col-sm-9">
                            <select name="toyyibpay_api_environment_value" id="input-api-environment" class="form-control">
                                <!--<option value="">[ Select ] <?php /* echo $toyyibpay_api_environment_value; */ ?></option>-->
								<option value="1" <?php if($toyyibpay_api_environment_value=='1'){ ?> selected="selected" <?php } ?> >Production Mode</option>
								<option value="2" <?php if($toyyibpay_api_environment_value=='2'){ ?> selected="selected" <?php } ?> >Sandbox Mode</option>
                            </select>
                            <?php if ($error_api_environment) { ?>
                                <div class="text-danger"><?php echo $error_api_environment; ?></div>
                            <?php } ?>
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="input-category-code"><span data-toggle="tooltip" title="<?php echo $help_category_code; ?>"><?php echo $toyyibpay_category_code; ?></span></label>
                        <div class="col-sm-9">
                            <input type="text" name="toyyibpay_category_code_value" value="<?php echo $toyyibpay_category_code_value; ?>" placeholder="<?php echo $toyyibpay_category_code; ?>" id="input-category-code" class="form-control" />
                        </div>
                    </div>
					
					<div class="form-group">
						<label class="col-sm-3 control-label" for="input-bill-name"><span data-toggle="tooltip" title="<?php echo $help_bill_name; ?>"><?php echo $toyyibpay_bill_name; ?></span></label>
                        <div class="col-sm-9">
                            <input type="text" name="toyyibpay_bill_name_value" value="<?php echo $toyyibpay_bill_name_value; ?>" placeholder="<?php echo $toyyibpay_bill_name; ?>" id="input-bill-name" class="form-control" />
                        </div>
                    </div>
					
					<div class="form-group required">
                        <label class="col-sm-3 control-label" for="input-payment-channel"><span data-toggle="tooltip" title="<?php echo $help_payment_channel; ?>"><?php echo $toyyibpay_payment_channel; ?></span></label>
                        <div class="col-sm-9">
                            <select name="toyyibpay_payment_channel_value" id="input-payment-channel" class="form-control">
							<option value="0" <?php if($toyyibpay_payment_channel_value=='0'){ ?> selected="selected" <?php } ?> >FPX</option>
							<option value="1" <?php if($toyyibpay_payment_channel_value=='1'){ ?> selected="selected" <?php } ?> >Credit Card</option>
							<option value="2" <?php if($toyyibpay_payment_channel_value=='2'){ ?> selected="selected" <?php } ?> >FPX & Credit Card</option>
                            </select>
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-3 control-label" for="input-payment-charge"><span data-toggle="tooltip" title="<?php echo $help_payment_charge; ?>"><?php echo $toyyibpay_payment_charge; ?></span></label>
                        <div class="col-sm-9">
                            <select name="toyyibpay_payment_charge_value" id="input-payment-charge" class="form-control">
                                <?php
                                $arrCharge = array();
								$arrCharge[''] = 'All charge on transaction amount';
								$arrCharge['0'] = 'Only FPX charge on customer';
								$arrCharge['1'] = 'Only Crefit card charge on customer';
								$arrCharge['2'] = 'All charge on customer';
                                foreach ($arrCharge as $key => $value) {  ?>
                                    <?php if ($key == $toyyibpay_payment_charge_value) { ?>
                                        <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-3 control-label" for="input-extra-email"><span data-toggle="tooltip" title="<?php echo $help_extra_email; ?>"><?php echo $toyyibpay_extra_email; ?></span></label>
                        <div class="col-sm-9">
							<textarea name="toyyibpay_extra_email_value" id="input-extra-email" class="form-control" rows="4" cols="100"><?php echo $toyyibpay_extra_email_value; ?></textarea>	
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="input-log-file"><span data-toggle="tooltip" title="<?php echo $help_log_file; ?>"><?php echo $toyyibpay_log_file; ?></span></label>
                        <div class="col-sm-9">
							<input type="hidden" name="toyyibpay_log_api_value" value="<?php echo $toyyibpay_log_api_value; ?>" />
							<input type="checkbox" name="toyyibpay_log_file_value" id="input-log-file" value="1" <?php if($toyyibpay_log_file_value=='1'){ ?> checked <?php } ?> />
							Enabled						
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="input-minlimit"><span data-toggle="tooltip" title="<?php echo $help_minlimit; ?>"><?php echo $entry_minlimit; ?></span></label>
                        <div class="col-sm-9">
                            <input type="text" name="toyyibpay_minlimit" value="<?php echo $toyyibpay_minlimit; ?>" placeholder="<?php echo $entry_minlimit; ?>" id="input-minlimit" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $entry_completed_status; ?></label>
                        <div class="col-sm-9">
                            <select name="toyyibpay_completed_status_id" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $toyyibpay_completed_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $entry_pending_status; ?></label>
                        <div class="col-sm-9">
                            <select name="toyyibpay_pending_status_id" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $toyyibpay_pending_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $entry_failed_status; ?></label>
                        <div class="col-sm-9">
                            <select name="toyyibpay_failed_status_id" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $toyyibpay_failed_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                        <div class="col-sm-9">
                            <select name="toyyibpay_geo_zone_id" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                    <?php if ($geo_zone['geo_zone_id'] == $toyyibpay_geo_zone_id) { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                    <?php } else { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-9">
                            <select name="toyyibpay_status" id="input-status" class="form-control">
                                <?php if ($toyyibpay_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-9">
                            <input type="text" name="toyyibpay_sort_order" value="<?php echo $toyyibpay_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>