<?php
// Text
$_['text_title']							= 'PayPal (Express, Card)';
$_['text_paypal_express']					= 'PayPal Express';
$_['text_paypal_card']						= 'PayPal Card';
$_['text_wait']								= 'Please wait!';
$_['text_order_message']					= 'PayPal Seller Protection - %s';

// Entry
$_['entry_card_number']						= 'Card Number';
$_['entry_expiration_date']					= 'Expiration Date';
$_['entry_cvv']								= 'CVV';

// Button
$_['button_pay']							= 'Pay with Card';

// Error
$_['error_warning']							= 'Please check the form carefully for errors.';
$_['error_order_voided']					= 'We could not process your payment. All purchase units in the order are voided. Please <a href="%s" target="_blank">contact us</a>.';
$_['error_order_completed']					= 'We could not process your payment. The payment was authorized or the authorized payment was captured for the order. Please <a href="%s" target="_blank">contact us</a>.';
$_['error_authorization_captured']			= 'We could not process your payment. The authorized payment has one or more captures against it. The sum of these captured payments is greater than the amount of the original authorized payment. Please <a href="%s" target="_blank">contact us</a>.';
$_['error_authorization_denied']			= 'We could not process the transaction with this card. The funds could not be captured. Please try a different funding source.';
$_['error_authorization_expired']			= 'We could not process your payment. The authorized payment has expired. Please <a href="%s" target="_blank">contact us</a>.';
$_['error_capture_declined']				= 'We could not process the transaction with this card. The funds could not be captured. Please try a different funding source.';
$_['error_capture_failed']					= 'We could not process your payment. There was an error while capturing payment. Please <a href="%s" target="_blank">contact us</a>.';
$_['error_3ds_failed_authentication']		= 'We could not process the transaction with this card. You may have failed the challenge or the device was not verified.';
$_['error_3ds_rejected_authentication']		= 'We could not process the transaction with this card. 3D Secure authentication was skipped by you.';
$_['error_3ds_attempted_authentication'] 	= 'We could not process the transaction with this card. Card is not enrolled in 3D Secure as card issuing bank is not participating in 3D Secure.';
$_['error_3ds_unable_authentication']		= 'We could not process the transaction with this card. Issuing bank is not able to complete authentication.';
$_['error_3ds_challenge_authentication']	= 'We could not process the transaction with this card. Challenge required for authentication.';
$_['error_3ds_card_ineligible']				= 'We could not process the transaction with this card. Card type and issuing bank are not ready to complete a 3D Secure authentication.';
$_['error_3ds_system_unavailable']			= 'We could not process the transaction with this card. An error occurred with the 3D Secure authentication system.';
$_['error_3ds_system_bypassed'] 			= 'We could not process the transaction with this card. 3D Secure was skipped as authentication system did not require a challenge.';
$_['error_payment']							= 'Please choose another payment method or <a href="%s" target="_blank">contact us</a>.';
$_['error_timeout'] 	  					= 'Sorry, PayPal is currently busy. Please try again later!';