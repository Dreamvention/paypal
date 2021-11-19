<style type="text/css">

#paypal_form {
	position: relative;
}

#paypal_smart_button {
	text-align: <?php echo $button_align; ?>;
}

#paypal_smart_button_container {
	<?php if ($button_width) { ?>
	display: inline-block;
	width: <?php echo $button_width; ?>;
	<?php } ?>
}

#paypal_message {
	text-align: <?php echo $message_align; ?>;
}

#paypal_message_container {
	<?php if ($message_width) { ?>
	display: inline-block;
	width: <?php echo $message_width; ?>;
	<?php } ?>
}

</style>
<script type="text/javascript">

function setupPayPalSmartButton() {
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
				paypal_order_id = false;
					
				$.ajax({
					method: 'post',
					url: 'index.php?route=module/paypal_smart_button/createOrder',
					data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
					dataType: 'json',
					async: false,
					success: function(json) {							
						showPayPalSmartButtonAlert(json);
							
						paypal_order_id = json['paypal_order_id'];
					},
					error: function(xhr, ajaxOptions, thrownError) {
						console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
					
				return paypal_order_id;	
			},
			// Finalize the transaction
			onApprove: function(data, actions) {						
				// Call your server to save the transaction
				$.ajax({
					method: 'post',
					url: 'index.php?route=module/paypal_smart_button/approveOrder',
					data: {'paypal_order_id': data.orderID},
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

function showPayPalSmartButtonAlert(json) {			
	if (json['error']) {
		if (json['error']['warning']) {
			alert(json['error']['warning']);
		}
	}
}

function paypalReady() {
	if (typeof paypal_sdk === 'undefined') {
		setTimeout(paypalReady, 100);
	} else {
		setupPayPalSmartButton();
	}
}

window.addEventListener('load', function () {
	if ($('<?php echo $insert_tag; ?>').length) {
		var html = '<div id="paypal_form"><div id="paypal_smart_button" class="buttons clearfix"><div id="paypal_smart_button_container"></div></div>';
		
		<?php if ($message_status) { ?>
		html += '<div id="paypal_message"><div id="paypal_message_container"><div data-pp-message data-pp-placement="<?php echo $message_placement; ?>" data-pp-amount="<?php echo $message_amount; ?>" data-pp-style-layout="<?php echo $message_layout; ?>" <?php if ($message_layout == 'text') { ?>data-pp-style-text-color="<?php echo $message_text_color; ?>" data-pp-style-text-size="<?php echo $message_text_size; ?>"<?php } else { ?>data-pp-style-color="<?php echo $message_flex_color; ?>" data-pp-style-ratio="<?php echo $message_flex_ratio; ?>"<?php } ?>></div></div></div>';
		<?php } ?>
		
		html += '</div>';
		
		$('<?php echo $insert_tag; ?>').<?php echo $insert_type; ?>(html);
		
		if (typeof paypal_sdk === 'undefined') {
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = 'https://www.paypal.com/sdk/js?components=buttons,messages&client-id=<?php echo $client_id; ?>&merchant-id=<?php echo $merchant_id; ?>&currency=<?php echo $currency_code; ?>&intent=<?php echo $transaction_method; ?>&commit=false<?php if ($button_enable_funding) { ?>&enable-funding=<?php echo implode(',', $button_enable_funding); ?><?php } ?><?php if ($button_disable_funding) { ?>&disable-funding=<?php echo implode(',', $button_disable_funding); ?><?php } ?>';
			script.setAttribute('data-partner-attribution-id', '<?php echo $partner_attribution_id; ?>');
			script.setAttribute('data-namespace', 'paypal_sdk');
			script.async = false;
			script.onload = paypalReady();
	
			var paypal_form = document.querySelector('#paypal_form');
			paypal_form.appendChild(script);
		} else {
			setupPayPalSmartButton();
		}
	}
});

</script>