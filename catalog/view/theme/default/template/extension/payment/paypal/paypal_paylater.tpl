<?php if ($checkout_mode == 'multi_button') { ?>
<div id="paypal_form">
	<?php if ($button_status) { ?>
	<div id="paypal_button" class="paypal-button buttons clearfix">
		<div id="paypal_button_container" class="paypal-button-container paypal-spinner"></div>
	</div>
	<?php } ?>
</div>
<script type="text/javascript">

PayPalAPI.init();
	
</script>
<?php } else { ?>
<div class="buttons">
	<div class="pull-right">
		<button type="button" id="button-confirm" class="btn btn-primary paypal-paylater-button-confirm"><?php echo $button_confirm; ?></button>
	</div>
</div>
<script type="text/javascript">

$(document).on('click', '.paypal-paylater-button-confirm', function(event) {
	$('#paypal_modal').remove();
	
	$('body').append('<div id="paypal_modal" class="modal fade"></div>');
	
	$('#paypal_modal').load('index.php?route=extension/payment/paypal_paylater/modal #paypal_modal >', function() {		
		$('#paypal_modal').modal('show');
		
		PayPalAPI.init();
	});
});

</script>
<?php } ?>