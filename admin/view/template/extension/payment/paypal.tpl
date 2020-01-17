<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form_payment" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
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
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_payment" class="form-horizontal">
					<ul class="nav nav-tabs">
						<li class="nav-tab active"><a href="#tab_general" data-toggle="tab"><?php echo $text_general; ?></a></li>
						<li class="nav-tab"><a href="#tab_order_status" data-toggle="tab"><?php echo $text_order_status; ?></a></li>
						<li class="nav-tab"><a href="#tab_checkout_express" data-toggle="tab"><?php echo $text_checkout_express; ?></a></li>
						<li class="nav-tab hidden"><a href="#tab_checkout_card" data-toggle="tab"><?php echo $text_checkout_card; ?></a></li>
					</ul>
		  
					<div class="tab-content">
						<div class="tab-pane active" id="tab_general">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_environment"><?php echo $entry_environment; ?></label>
								<div class="col-sm-10">
									<select name="paypal_environment" id="input_environment" class="form-control">
										<?php if ($environment == 'production') { ?>
										<option value="production" selected="selected"><?php echo $text_production; ?></option>
										<option value="sandbox"><?php echo $text_sandbox; ?></option>
										<?php } else { ?>
										<option value="production"><?php echo $text_production; ?></option>
										<option value="sandbox" selected="selected"><?php echo $text_sandbox; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_connect"><?php echo $entry_connect; ?></label>
								<div class="col-sm-10">
									<div id="section_connect" <?php if ($client_id && $secret && $merchant_id) { ?> class="hidden"<?php } ?>>
										<style type="text/css">
											a[data-paypal-button="PPLtBlue"]::before {
												content: "";
												background: url(https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-200px.png) 11px no-repeat #fff;
												background-size: 80px;
												display: table-cell;
												width: 100px;
												height: 42px;
												float: left;
												margin-right: 16px;
												border-bottom-left-radius: 5px;
												border-top-left-radius: 5px;
												margin-top: -12px;
											}
										</style>
										<a id="button_connect_ppcp" href="<?php echo $configure_url[$environment]['ppcp']; ?>" target="_blank" data-paypal-button="PPLtBlue" data-paypal-onboard-complete="onBoardedCallback"><?php echo $button_connect; ?></a><br />
										<p><?php echo $help_checkout_express; ?></p>
									</div>
									<?php if ($client_id && $secret && $merchant_id) { ?>
									<div id="section_disconnect">
										<p class="alert alert-info"><?php echo $text_connect; ?></p>
										<a id="button_disconnect" class="btn btn-danger"><?php echo $button_disconnect; ?></a>
									</div>
									<?php } ?>
									<input type="hidden" name="paypal_client_id" value="<?php echo $client_id; ?>" id="input_client_id" />
									<input type="hidden" name="paypal_secret" value="<?php echo $secret; ?>" id="input_secret" />
									<input type="hidden" name="paypal_merchant_id" value="<?php echo $merchant_id; ?>" id="input_merchant_id" />
									<input type="hidden" name="paypal_webhook_id" value="<?php echo $webhook_id; ?>" id="input_webhook_id" />
								</div>
							</div>
							<?php if ($client_id && $secret && $merchant_id) { ?>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_checkout_express_status"><span data-toggle="tooltip" title="<?php echo $help_checkout_express_status; ?>"><?php echo $entry_checkout_express_status; ?></span></label>
								<div class="col-sm-10">
									<select name="paypal_setting[checkout][express][status]" id="input_checkout_express_status" class="form-control">
										<?php if ($setting['checkout']['express']['status']) { ?>
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
								<label class="col-sm-2 control-label" for="input_checkout_card_status"><?php echo $entry_checkout_card_status; ?></label>
								<div class="col-sm-10">
									<select name="paypal_setting[checkout][card][status]" id="input_checkout_card_status" class="form-control hidden">
										<?php if ($setting['checkout']['card']['status']) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select>
									<div class="alert alert-warning alert-dismissible hidden"><i class="fa fa-exclamation-circle"></i> <?php echo $help_checkout_card_status; ?>
										<button type="button" class="close" data-dismiss="alert">&times;</button>
									</div>
								</div>
							</div>
							<?php } ?>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_debug"><?php echo $entry_debug; ?></label>
								<div class="col-sm-10">
									<select name="paypal_debug" id="input_debug" class="form-control">
										<?php if ($debug) { ?>
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
								<label class="col-sm-2 control-label" for="input_transaction_method"><?php echo $entry_transaction_method; ?></label>
								<div class="col-sm-10">
									<select name="paypal_transaction_method" id="input_transaction_method" class="form-control">
										<?php if ($transaction_method == 'authorize') { ?>
										<option value="authorize" selected="selected"><?php echo $text_authorization; ?></option>
										<option value="capture"><?php echo $text_sale; ?></option>
										<?php } else { ?>
										<option value="authorize"><?php echo $text_authorization; ?></option>
										<option value="capture" selected="selected"><?php echo $text_sale; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
								<div class="col-sm-10">
									<input type="text" name="paypal_total" value="<?php echo $total; ?>" placeholder="<?php echo $entry_total; ?>" id="input_total" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_geo_zone"><?php echo $entry_geo_zone; ?></label>
								<div class="col-sm-10">
									<select name="paypal_geo_zone_id" id="input_geo_zone" class="form-control">
										<option value="0"><?php echo $text_all_zones; ?></option>
										<?php foreach ($geo_zones as $geo_zone) { ?>
										<?php if ($geo_zone['geo_zone_id'] == $geo_zone_id) { ?>
										<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
										<?php } else { ?>
										<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_status"><?php echo $entry_status; ?></label>
								<div class="col-sm-10">
									<select name="paypal_status" id="input_status" class="form-control">
										<?php if ($status) { ?>
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
								<label class="col-sm-2 control-label" for="input_sort_order"><?php echo $entry_sort_order; ?></label>
								<div class="col-sm-10">
									<input type="text" name="paypal_sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input_sort_order" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_smart_button"><?php echo $entry_smart_button; ?></label>
								<div class="col-sm-2">
									<a href="<?php echo $configure_smart_button_url; ?>" target="_blank" class="btn btn-primary"><?php echo $button_smart_button; ?></a>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab_order_status">
							<?php foreach ($setting['order_status'] as $paypal_order_status) { ?>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_order_status_<?php echo $paypal_order_status['code']; ?>"><?php echo ${$paypal_order_status['name']}; ?></label>
								<div class="col-sm-10">
									<select name="paypal_setting[order_status][<?php echo $paypal_order_status['code']; ?>][id]" id="input_<?php echo $paypal_order_status['code']; ?>_status" class="form-control">
										<?php foreach ($order_statuses as $order_status) { ?>
										<?php if ($order_status['order_status_id'] == $paypal_order_status['id']) { ?>
										<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
										<?php } else { ?>
										<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<?php } ?>
						</div>
						<div class="tab-pane" id="tab_checkout_express">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_checkout_express_button_align"><?php echo $entry_button_align; ?></label>
								<div class="col-sm-10">
									<select name="paypal_setting[checkout][express][button_align]" id="input_checkout_express_button_align" class="form-control">
										<?php foreach ($setting['button_align'] as $button_align) { ?>
										<?php if ($button_align['code'] == $setting['checkout']['express']['button_align']) { ?>
										<option value="<?php echo $button_align['code']; ?>" selected="selected"><?php echo ${$button_align['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $button_align['code']; ?>"><?php echo ${$button_align['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_checkout_express_button_size"><?php echo $entry_button_size; ?></label>
								<div class="col-sm-10">
									<select name="paypal_setting[checkout][express][button_size]" id="input_checkout_express_button_size" class="form-control">
										<?php foreach ($setting['button_size'] as $button_size) { ?>
										<?php if ($button_size['code'] == $setting['checkout']['express']['button_size']) { ?>
										<option value="<?php echo $button_size['code']; ?>" selected="selected"><?php echo ${$button_size['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $button_size['code']; ?>"><?php echo ${$button_size['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_checkout_express_button_color"><?php echo $entry_button_color; ?></label>
								<div class="col-sm-10">
									<select name="paypal_setting[checkout][express][button_color]" id="input_checkout_express_button_color" class="form-control">
										<?php foreach ($setting['button_color'] as $button_color) { ?>
										<?php if ($button_color['code'] == $setting['checkout']['express']['button_color']) { ?>
										<option value="<?php echo $button_color['code']; ?>" selected="selected"><?php echo ${$button_color['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $button_color['code']; ?>"><?php echo ${$button_color['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_checkout_express_button_shape"><?php echo $entry_button_shape; ?></label>
								<div class="col-sm-10">
									<select name="paypal_setting[checkout][express][button_shape]" id="input_checkout_express_button_shape" class="form-control">
										<?php foreach ($setting['button_shape'] as $button_shape) { ?>
										<?php if ($button_shape['code'] == $setting['checkout']['express']['button_shape']) { ?>
										<option value="<?php echo $button_shape['code']; ?>" selected="selected"><?php echo ${$button_shape['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $button_shape['code']; ?>"><?php echo ${$button_shape['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_checkout_express_button_label"><?php echo $entry_button_label; ?></label>
								<div class="col-sm-10">
									<select name="paypal_setting[checkout][express][button_label]" id="input_checkout_express_button_label" class="form-control">
										<?php foreach ($setting['button_label'] as $button_label) { ?>
										<?php if ($button_label['code'] == $setting['checkout']['express']['button_label']) { ?>
										<option value="<?php echo $button_label['code']; ?>" selected="selected"><?php echo ${$button_label['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $button_label['code']; ?>"><?php echo ${$button_label['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						<div class="tab-pane hidden" id="tab_checkout_card">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_checkout_card_form_align"><?php echo $entry_form_align; ?></label>
								<div class="col-sm-10">
									<select name="paypal_setting[checkout][card][form_align]" id="input_checkout_card_form_align" class="form-control">
										<?php foreach ($setting['form_align'] as $form_align) { ?>
										<?php if ($form_align['code'] == $setting['checkout']['card']['form_align']) { ?>
										<option value="<?php echo $form_align['code']; ?>" selected="selected"><?php echo ${$form_align['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $form_align['code']; ?>"><?php echo ${$form_align['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_checkout_card_form_size"><?php echo $entry_form_size; ?></label>
								<div class="col-sm-10">
									<select name="paypal_setting[checkout][card][form_size]" id="input_checkout_card_form_size" class="form-control">
										<?php foreach ($setting['form_size'] as $form_size) { ?>
										<?php if ($form_size['code'] == $setting['checkout']['card']['form_size']) { ?>
										<option value="<?php echo $form_size['code']; ?>" selected="selected"><?php echo ${$form_size['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $form_size['code']; ?>"><?php echo ${$form_size['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_checkout_card_secure_status"><span data-toggle="tooltip" title="<?php echo $help_secure_status; ?>"><?php echo $entry_secure_status; ?></span></label>
								<div class="col-sm-10">
									<select name="paypal_setting[checkout][card][secure_status]" id="input_checkout_card_secure_status" class="form-control">
										<?php if ($setting['checkout']['card']['secure_status']) { ?>
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
								<label class="col-sm-2 control-label" for="input_checkout_card_secure_scenario"><span data-toggle="tooltip" title="<?php echo $help_secure_scenario; ?>"><?php echo $entry_secure_scenario; ?></span></label>
								<div class="col-sm-10">
									<?php foreach ($setting['secure_scenario'] as $secure_scenario) { ?>
									<div class="row">
										<div class="col-sm-8">
											<label class="control-label" for="input_checkout_card_secure_scenario"><?php echo ${$secure_scenario['name']}; ?></label>
										</div>
										<div class="col-sm-4">										
											<select name="paypal_setting[checkout][card][secure_scenario][<?php echo $secure_scenario['code']; ?>]" class="form-control">
												<option value="1" <?php if ($setting['checkout']['card']['secure_scenario'][$secure_scenario['code']]) { ?>selected="selected"<?php } ?>><?php echo $text_accept; ?><?php if ($secure_scenario['recommended']) { ?> <?php echo $text_recommended; ?><?php } ?></option>
												<option value="0" <?php if (!$setting['checkout']['card']['secure_scenario'][$secure_scenario['code']]) { ?>selected="selected"<?php } ?>><?php echo $text_decline; ?><?php if (!$secure_scenario['recommended']) { ?> <?php echo $text_recommended; ?><?php } ?></option>
											</select>
										</div>
									</div>
									<br />
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php if ($client_id && $secret && $merchant_id) { ?>
<script type="text/javascript" src = "https://www.paypal.com/sdk/js?components=hosted-fields&client-id=<?php echo $client_id; ?>&merchant-id=<?php echo $merchant_id; ?>" data-partner-attribution-id="<?php echo $setting['partner'][$environment]['partner_id']; ?>" data-client-token="<?php echo $client_token; ?>"></script>
<script type="text/javascript">

try {
	if (paypal.HostedFields.isEligible() === true) {
		$('[href="#tab_checkout_card"]').parents('.nav-tab').removeClass('hidden');
		$('#tab_checkout_card').removeClass('hidden');
		$('#input_checkout_card_status').removeClass('hidden');
	} else {
		$('#input_checkout_card_status').parents('.form-group').find('.alert').removeClass('hidden');
	}
} catch (error) {
	console.error('PayPal Card failed during startup', error);
}

</script>
<?php } ?>
<script type="text/javascript">

function onBoardedCallback(authorization_code, shared_id) {
	var environment = $('#input_environment').val();
	
	$.ajax({
		url: '<?php echo $callback_url; ?>',
		type: 'post',
		data: 'environment=' + environment + '&authorization_code=' + authorization_code + '&shared_id=' + shared_id + '&seller_nonce=<?php echo $seller_nonce; ?>',
		dataType: 'json',
		success: function(json) {
			
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

</script>
<script type="text/javascript">

$('#button_disconnect').on('click', function() {
	if (confirm('<?php echo $text_confirm; ?>')) {
		$('#input_client_id').val('');
		$('#input_secret').val('');
		$('#input_merchant_id').val('');
		$('#section_connect').removeClass('hidden');
		$('#section_disconnect').addClass('hidden');
	}
});

$('#input_environment').on('change', function() {
	var environment = $(this).val();
	
	if (environment == 'production') {
		$('#button_connect_ppcp').attr('href', '<?php echo $configure_url['production']['ppcp']; ?>');
		$('#button_connect_express_checkout').attr('href', '<?php echo $configure_url['production']['express_checkout']; ?>');
	} else {
		$('#button_connect_ppcp').attr('href', '<?php echo $configure_url['sandbox']['ppcp']; ?>');
		$('#button_connect_express_checkout').attr('href', '<?php echo $configure_url['sandbox']['express_checkout']; ?>');
	}
	
	$('#input_client_id').val('');
	$('#input_secret').val('');
	$('#input_merchant_id').val('');
	$('#section_connect').removeClass('hidden');
	$('#section_disconnect').addClass('hidden');
});

</script>
<script id="paypal-js" src="https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js"></script>   
<?php echo $footer; ?>