<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form_module" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_module" class="form-horizontal">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_general" data-toggle="tab"><?php echo $text_general; ?></a></li>
						<?php foreach ($setting['page'] as $page) { ?>
						<li><a href="#tab_<?php echo $page['code']; ?>" data-toggle="tab"><?php echo ${$page['name']}; ?></a></li>
						<?php } ?>
					</ul>
					
					<div class="tab-content">
						<div class="tab-pane active" id="tab_general">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_status"><?php echo $entry_status; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_status" id="input_status" class="form-control">
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
							<?php foreach ($setting['page'] as $page) { ?>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_status"><?php echo ${'entry_' . $page['code'] . '_page_status'}; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][status]" id="input_page_<?php echo $page['code']; ?>_status" class="form-control">
										<?php if ($page['status']) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<?php } ?>
						</div>
						<?php foreach ($setting['page'] as $page) { ?>
						<div class="tab-pane" id="tab_<?php echo $page['code']; ?>">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_insert_tag"><?php echo $entry_insert_tag; ?></label>
								<div class="col-sm-10">
									<input type="text" name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][insert_tag]" value="<?php echo $page['insert_tag']; ?>" id="input_page_<?php echo $page['code']; ?>_insert_tag" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_insert_type"><?php echo $entry_insert_type; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][insert_type]" id="input_page_<?php echo $page['code']; ?>_insert_type" class="form-control">
										<?php foreach ($setting['insert_type'] as $insert_type) { ?>
										<?php if ($insert_type['code'] == $page['insert_type']) { ?>
										<option value="<?php echo $insert_type['code']; ?>" selected="selected"><?php echo ${$insert_type['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $insert_type['code']; ?>"><?php echo ${$insert_type['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_button_align"><?php echo $entry_button_align; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][button_align]" id="input_page_<?php echo $page['code']; ?>_button_align" class="form-control">
										<?php foreach ($setting['button_align'] as $button_align) { ?>
										<?php if ($button_align['code'] == $page['button_align']) { ?>
										<option value="<?php echo $button_align['code']; ?>" selected="selected"><?php echo ${$button_align['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $button_align['code']; ?>"><?php echo ${$button_align['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_button_size"><?php echo $entry_button_size; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][button_size]" id="input_page_<?php echo $page['code']; ?>_button_size" class="form-control">
										<?php foreach ($setting['button_size'] as $button_size) { ?>
										<?php if ($button_size['code'] == $page['button_size']) { ?>
										<option value="<?php echo $button_size['code']; ?>" selected="selected"><?php echo ${$button_size['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $button_size['code']; ?>"><?php echo ${$button_size['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_button_color"><?php echo $entry_button_color; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][button_color]" id="input_page_<?php echo $page['code']; ?>_button_color" class="form-control">
										<?php foreach ($setting['button_color'] as $button_color) { ?>
										<?php if ($button_color['code'] == $page['button_color']) { ?>
										<option value="<?php echo $button_color['code']; ?>" selected="selected"><?php echo ${$button_color['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $button_color['code']; ?>"><?php echo ${$button_color['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_shape"><?php echo $entry_button_shape; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][button_shape]" id="input_page_<?php echo $page['code']; ?>_shape" class="form-control">
										<?php foreach ($setting['button_shape'] as $button_shape) { ?>
										<?php if ($button_shape['code'] == $page['button_shape']) { ?>
										<option value="<?php echo $button_shape['code']; ?>" selected="selected"><?php echo ${$button_shape['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $button_shape['code']; ?>"><?php echo ${$button_shape['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_button_label"><?php echo $entry_button_label; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][button_label]" id="input_page_<?php echo $page['code']; ?>_button_label" class="form-control">
										<?php foreach ($setting['button_label'] as $button_label) { ?>
										<?php if ($button_label['code'] == $page['button_label']) { ?>
										<option value="<?php echo $button_label['code']; ?>" selected="selected"><?php echo ${$button_label['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $button_label['code']; ?>"><?php echo ${$button_label['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_button_tagline"><?php echo $entry_button_tagline; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][button_tagline]" id="input_page_<?php echo $page['code']; ?>_button_tagline" class="form-control">
										<?php foreach ($setting['button_tagline'] as $button_tagline) { ?>
										<?php if ($button_tagline['code'] == $page['button_tagline']) { ?>
										<option value="<?php echo $button_tagline['code']; ?>" selected="selected"><?php echo ${$button_tagline['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $button_tagline['code']; ?>"><?php echo ${$button_tagline['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_message_status"><span data-toggle="tooltip" title="<?php echo $help_message_status; ?>"><?php echo $entry_message_status; ?></span></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][message_status]" id="input_page_<?php echo $page['code']; ?>_message_status" class="form-control">
										<?php if ($page['message_status']) { ?>
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
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_message_align"><?php echo $entry_message_align; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][message_align]" id="input_page_<?php echo $page['code']; ?>_message_align" class="form-control">
										<?php foreach ($setting['message_align'] as $message_align) { ?>
										<?php if ($message_align['code'] == $page['message_align']) { ?>
										<option value="<?php echo $message_align['code']; ?>" selected="selected"><?php echo ${$message_align['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $message_align['code']; ?>"><?php echo ${$message_align['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_message_size"><?php echo $entry_message_size; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][message_size]" id="input_page_<?php echo $page['code']; ?>_message_size" class="form-control">
										<?php foreach ($setting['message_size'] as $message_size) { ?>
										<?php if ($message_size['code'] == $page['message_size']) { ?>
										<option value="<?php echo $message_size['code']; ?>" selected="selected"><?php echo ${$message_size['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $message_size['code']; ?>"><?php echo ${$message_size['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_message_layout"><?php echo $entry_message_layout; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][message_layout]" id="input_page_<?php echo $page['code']; ?>_message_layout" class="form-control">
										<?php foreach ($setting['message_layout'] as $message_layout) { ?>
										<?php if ($message_layout['code'] == $page['message_layout']) { ?>
										<option value="<?php echo $message_layout['code']; ?>" selected="selected"><?php echo ${$message_layout['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $message_layout['code']; ?>"><?php echo ${$message_layout['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group <?php if ($page['message_layout'] == 'flex') { ?>hidden<?php } ?>">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_message_text_color"><?php echo $entry_message_text_color; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][message_text_color]" id="input_page_<?php echo $page['code']; ?>_message_text_color" class="form-control">
										<?php foreach ($setting['message_text_color'] as $message_text_color) { ?>
										<?php if ($message_text_color['code'] == $page['message_text_color']) { ?>
										<option value="<?php echo $message_text_color['code']; ?>" selected="selected"><?php echo ${$message_text_color['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $message_text_color['code']; ?>"><?php echo ${$message_text_color['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group <?php if ($page['message_layout'] == 'flex') { ?>hidden<?php } ?>">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_message_text_size"><?php echo $entry_message_text_size; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][message_text_size]" id="input_page_<?php echo $page['code']; ?>_message_text_size" class="form-control">
										<?php foreach ($setting['message_text_size'] as $message_text_size) { ?>
										<?php if ($message_text_size == $page['message_text_size']) { ?>
										<option value="<?php echo $message_text_size; ?>" selected="selected"><?php echo $message_text_size; ?></option>
										<?php } else { ?>
										<option value="<?php echo $message_text_size; ?>"><?php echo $message_text_size; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group <?php if ($page['message_layout'] == 'text') { ?>hidden<?php } ?>">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_message_flex_color"><?php echo $entry_message_flex_color; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][message_flex_color]" id="input_page_<?php echo $page['code']; ?>_message_flex_color" class="form-control">
										<?php foreach ($setting['message_flex_color'] as $message_flex_color) { ?>
										<?php if ($message_flex_color['code'] == $page['message_flex_color']) { ?>
										<option value="<?php echo $message_flex_color['code']; ?>" selected="selected"><?php echo ${$message_flex_color['name']}; ?></option>
										<?php } else { ?>
										<option value="<?php echo $message_flex_color['code']; ?>"><?php echo ${$message_flex_color['name']}; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group <?php if ($page['message_layout'] == 'text') { ?>hidden<?php } ?>">
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_message_flex_ratio"><?php echo $entry_message_flex_ratio; ?></label>
								<div class="col-sm-10">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][message_flex_ratio]" id="input_page_<?php echo $page['code']; ?>_message_flex_ratio" class="form-control">
										<?php foreach ($setting['message_flex_ratio'] as $message_flex_ratio) { ?>
										<?php if ($message_flex_ratio == $page['message_flex_ratio']) { ?>
										<option value="<?php echo $message_flex_ratio; ?>" selected="selected"><?php echo $message_flex_ratio; ?></option>
										<?php } else { ?>
										<option value="<?php echo $message_flex_ratio; ?>"><?php echo $message_flex_ratio; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<?php foreach ($setting['button_funding'] as $button_funding) { ?>
								<label class="col-sm-2 control-label" for="input_page_<?php echo $page['code']; ?>_button_funding"><?php echo ${$button_funding['name']}; ?></label>
								<div class="col-sm-4">
									<select name="paypal_smart_button_setting[page][<?php echo $page['code']; ?>][button_funding][<?php echo $button_funding['code']; ?>]" class="form-control">
										<option value="0" <?php if ($page['button_funding'][$button_funding['code']] == 0) { ?>selected="selected"<?php } ?>><?php echo $text_auto; ?></option>
										<option value="1" <?php if ($page['button_funding'][$button_funding['code']] == 1) { ?>selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
										<option value="2" <?php if ($page['button_funding'][$button_funding['code']] == 2) { ?>selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>	
									</select>
									<br />
								</div>
								<?php } ?>
							</div>
						</div>
						<?php } ?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

$('#input_page_product_message_layout').on('change', function() {
	var layout = $(this).val();
	
	if (layout == 'text') {
		$('#input_page_product_message_flex_color').parents('.form-group').addClass('hidden');
		$('#input_page_product_message_flex_ratio').parents('.form-group').addClass('hidden');
		$('#input_page_product_message_text_color').parents('.form-group').removeClass('hidden');
		$('#input_page_product_message_text_size').parents('.form-group').removeClass('hidden');
	} else {
		$('#input_page_product_message_text_color').parents('.form-group').addClass('hidden');
		$('#input_page_product_message_text_size').parents('.form-group').addClass('hidden');
		$('#input_page_product_message_flex_color').parents('.form-group').removeClass('hidden');
		$('#input_page_product_message_flex_ratio').parents('.form-group').removeClass('hidden');
	}
});

$('#input_page_cart_message_layout').on('change', function() {
	var layout = $(this).val();
	
	if (layout == 'text') {
		$('#input_page_cart_message_flex_color').parents('.form-group').addClass('hidden');
		$('#input_page_cart_message_flex_ratio').parents('.form-group').addClass('hidden');
		$('#input_page_cart_message_text_color').parents('.form-group').removeClass('hidden');
		$('#input_page_cart_message_text_size').parents('.form-group').removeClass('hidden');
	} else {
		$('#input_page_cart_message_text_color').parents('.form-group').addClass('hidden');
		$('#input_page_cart_message_text_size').parents('.form-group').addClass('hidden');
		$('#input_page_cart_message_flex_color').parents('.form-group').removeClass('hidden');
		$('#input_page_cart_message_flex_ratio').parents('.form-group').removeClass('hidden');
	}
});

</script>
<?php echo $footer; ?>