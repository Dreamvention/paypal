<style type="text/css">

#paypal_smart_button {
	text-align: <?php echo $button_align; ?>;
}

#paypal_smart_button_container {
	<?php if ($button_width) { ?>
	display: inline-block;
	width: <?php echo $button_width; ?>;
	<?php } ?>
}

</style>
<script type="text/javascript" src="https://www.paypal.com/sdk/js?components=buttons&client-id=<?php echo $client_id; ?>&merchant-id=<?php echo $merchant_id; ?>&currency=<?php echo $currency_code; ?>&intent=<?php echo $transaction_method; ?>&commit=false<?php if ($environment == 'sandbox') { ?>&buyer-country=NL<?php } ?>" data-partner-attribution-id="<?php echo $partner_id; ?>" data-namespace="paypal_sdk"></script>
<script type="text/javascript">

window.onload = function() {
	setupPayPalSmartButton();
};

function setupPayPalSmartButton() {
	if ($('<?php echo $insert_tag; ?>').length) {
		$('<?php echo $insert_tag; ?>').<?php echo $insert_type; ?>('<div id="paypal_smart_button" class="buttons clearfix"><div id="paypal_smart_button_container"></div></div>');
			
		try {		
			// Render the PayPal button into #paypal_smart_button_container
			paypal_sdk.Buttons({
				env: '<?php echo $environment; ?>',
				locale: '<?php echo $locale; ?>',
				style: {
					layout: 'horizontal',
					size: '<?php echo $button_size; ?>',
					color: '<?php echo $button_color; ?>',
					shape: '<?php echo $button_shape; ?>',
					label: '<?php echo $button_label; ?>',
					tagline: '<?php echo $button_tagline; ?>'
				},
				// Set up the transaction
				createOrder: function(data, actions) {
					order_id = false;
					
					$.ajax({
						method: 'post',
						url: 'index.php?route=module/paypal_smart_button/createOrder',
						data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
						dataType: 'json',
						async: false,
						success: function(json) {							
							showPayPalSmartButtonAlert(json);
							
							order_id = json['order_id'];
						},
						error: function(xhr, ajaxOptions, thrownError) {
							console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
					
					return order_id;	
				},
				// Finalize the transaction
				onApprove: function(data, actions) {						
					// Call your server to save the transaction
					$.ajax({
						method: 'post',
						url: 'index.php?route=module/paypal_smart_button/approveOrder',
						data: {'order_id': data.orderID},
						dataType: 'json',
						async: false,
						success: function(json) {							
							showPayPalSmartButtonAlert(json);
							
							if (json['url']) {
								location = json['url'];
							}
						},
						error: function(xhr, ajaxOptions, thrownError) {
							console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				}
			}).render('#paypal_smart_button_container');
		} catch (error) {
			console.error('PayPal failed during startup', error);
		}
	}
}

function showPayPalSmartButtonAlert(json) {			
	if (json['error']) {
		if (json['error']['warning']) {
			alert(json['error']['warning']);
		}
	}
}

</script>