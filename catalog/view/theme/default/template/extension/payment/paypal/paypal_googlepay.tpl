<?php if ($checkout_mode == 'multi_button') { ?>
<div id="paypal_form">
	<?php if ($googlepay_button_status) { ?>
	<div id="googlepay_button" class="googlepay-button buttons clearfix">
		<div id="googlepay_button_container" class="googlepay-button-container paypal-spinner"></div>
	</div>
	<?php } ?>
</div>
<script type="text/javascript">

if (typeof PayPalAPI !== 'undefined') {
	PayPalAPI.init();
}

</script>
<?php } else { ?>
<div class="buttons">
	<div class="pull-right">
		<button type="button" id="button-confirm" class="btn btn-primary paypal-googlepay-button-confirm" data-loading-text="<?php echo $text_loading; ?>"><?php echo $button_confirm; ?></button>
	</div>
</div>
<script type="text/javascript">

$(document).on('click', '.paypal-googlepay-button-confirm', function(event) {
	$('.paypal-googlepay-button-confirm').button('loading');
	
	$('#paypal_modal').remove();
	
	$('body').append('<div id="paypal_modal" class="modal fade"></div>');
	
	$('#paypal_modal').load('index.php?route=extension/payment/paypal_googlepay/modal #paypal_modal >', function() {		
		$('.paypal-googlepay-button-confirm').button('reset');
		
		$('#paypal_modal').modal('show');
		
		if (typeof PayPalAPI !== 'undefined') {
			PayPalAPI.init();
		}
	});
});

</script>
<?php } ?>