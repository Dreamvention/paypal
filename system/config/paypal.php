<?php 
$_['paypal_setting'] = array(
	'partner' => array(
		'production' => array(
			'partner_id' => 'TY2Q25KP2PX9L',
			'client_id' => 'AbjxI4a9fMnew8UOMoDFVwSh7h1aeOBaXpd2wcccAnuqecijKIylRnNguGRWDrEPrTYraBQApf_-O3_4'
		),
		'sandbox' => array(
			'partner_id' => 'EJNHWRJJNB38L',
			'client_id' => 'AfeIgIr-fIcEucsVXvdq21Ufu0wAALWhgJdVF4ItUK1IZFA9I4JIRdfyJ9vWrd9oi0B6mBGtJYDrlYsG'
		)
	),
	'order_status' => array(
		'canceled_reversal' => array(
			'code' => 'canceled_reversal',
			'name' => 'text_canceled_reversal_status',
			'id' => 9
		),
		'completed' => array(
			'code' => 'completed',
			'name' => 'text_completed_status',
			'id' => 5
		),
		'denied' => array(
			'code' => 'denied',
			'name' => 'text_denied_status',
			'id' => 8
		),
		'expired' => array(
			'code' => 'expired',
			'name' => 'text_expired_status',
			'id' => 14
		),
		'failed' => array(
			'code' => 'failed',
			'name' => 'text_failed_status',
			'id' => 10
		),
		'pending' => array(
			'code' => 'pending',
			'name' => 'text_pending_status',
			'id' => 1
		),
		'processed' => array(
			'code' => 'processed',
			'name' => 'text_processed_status',
			'id' => 15
		),
		'refunded' => array(
			'code' => 'refunded',
			'name' => 'text_refunded_status',
			'id' => 11
		),
		'reversed' => array(
			'code' => 'reversed',
			'name' => 'text_reversed_status',
			'id' => 12
		),
		'voided' => array(
			'code' => 'voided',
			'name' => 'text_voided_status',
			'id' => 16
		)
	),
	'checkout' => array(
		'express' => array(
			'status' => true,
			'button_align' => 'right',
			'button_size' => 'large',
			'button_color' => 'gold',
			'button_shape' => 'rect',
			'button_label' => 'paypal'
		),
		'card' => array(
			'status' => false,
			'form_align' => 'right',
			'form_size' => 'large',
			'secure_status' => true,
			'secure_scenario' => array(
				'undefined' => 1,
				'error' => 0,
				'skipped_by_buyer' => 0,
				'failure' => 0,
				'bypassed' => 0,
				'attempted' => 1,
				'unavailable' => 0,
				'card_ineligible' => 1
			),
			'secure_scenario_recommended' => array(
				'undefined' => 1,
				'error' => 0,
				'skipped_by_buyer' => 0,
				'failure' => 0,
				'bypassed' => 0,
				'attempted' => 1,
				'unavailable' => 0,
				'card_ineligible' => 1
			)
		)
	),
	'button_align' => array(
		'left' => array(
			'code' => 'left',
			'name' => 'text_align_left'
		),
		'center' => array(
			'code' => 'center',
			'name' => 'text_align_center'
		),
		'right' => array(
			'code' => 'right',
			'name' => 'text_align_right'
		)
	),
	'button_size' => array(
		'small' => array(
			'code' => 'small',
			'name' => 'text_small'
		),
		'medium' => array(
			'code' => 'medium',
			'name' => 'text_medium'
		),
		'large' => array(
			'code' => 'large',
			'name' => 'text_large'
		),
		'responsive' => array(
			'code' => 'responsive',
			'name' => 'text_responsive'
		)
	),
	'button_color' => array(
		'gold' => array(
			'code' => 'gold',
			'name' => 'text_gold'
		),
		'blue' => array(
			'code' => 'blue',
			'name' => 'text_blue'
		),
		'silver' => array(
			'code' => 'silver',
			'name' => 'text_silver'
		),
		'white' => array(
			'code' => 'white',
			'name' => 'text_white'
		),
		'black' => array(
			'code' => 'black',
			'name' => 'text_black'
		)
	),
	'button_shape' => array(
		'pill' => array(
			'code' => 'pill',
			'name' => 'text_pill'
		),
		'rect' => array(
			'code' => 'rect',
			'name' => 'text_rect'
		)
	),
	'button_label' => array(
		'checkout' => array(
			'code' => 'checkout',
			'name' => 'text_checkout'
		),
		'credit' => array(
			'code' => 'credit',
			'name' => 'text_credit'
		),
		'pay' => array(
			'code' => 'pay',
			'name' => 'text_pay'
		),
		'buynow' => array(
			'code' => 'buynow',
			'name' => 'text_buy_now'
		),
		'paypal' => array(
			'code' => 'paypal',
			'name' => 'text_pay_pal'
		),
		'installment' => array(
			'code' => 'installment',
			'name' => 'text_installment'
		)
	),
	'button_width' => array(
		'small' => '200px',
		'medium' => '250px',
		'large' => '350px',
		'responsive' => ''
	),
	'form_align' => array(
		'left' => array(
			'code' => 'left',
			'name' => 'text_align_left'
		),
		'center' => array(
			'code' => 'center',
			'name' => 'text_align_center'
		),
		'right' => array(
			'code' => 'right',
			'name' => 'text_align_right'
		)
	),
	'form_size' => array(
		'medium' => array(
			'code' => 'medium',
			'name' => 'text_medium'
		),
		'large' => array(
			'code' => 'large',
			'name' => 'text_large'
		),
		'responsive' => array(
			'code' => 'responsive',
			'name' => 'text_responsive'
		)
	),
	'form_width' => array(
		'medium' => '250px',
		'large' => '350px',
		'responsive' => ''
	),
	'secure_scenario' => array(
		'undefined' => array(
			'code' => 'undefined',
			'name' => 'text_3ds_undefined',
			'error' => 'error_3ds_undefined'
		),
		'error' => array(
			'code' => 'error',
			'name' => 'text_3ds_error',
			'error' => 'error_3ds_undefined'
		),
		'skipped_by_buyer' => array(
			'code' => 'skipped_by_buyer',
			'name' => 'text_3ds_skipped_by_buyer',
			'error' => 'error_3ds_skipped_by_buyer'
		),
		'failure' => array(
			'code' => 'failure',
			'name' => 'text_3ds_failure',
			'error' => 'error_3ds_failure'
		),
		'bypassed' => array(
			'code' => 'bypassed',
			'name' => 'text_3ds_bypassed',
			'error' => 'error_3ds_bypassed'
		),
		'attempted' => array(
			'code' => 'attempted',
			'name' => 'text_3ds_attempted',
			'error' => 'error_3ds_attempted'
		),
		'unavailable' => array(
			'code' => 'unavailable',
			'name' => 'text_3ds_unavailable',
			'error' => 'error_3ds_unavailable'
		),
		'card_ineligible' => array(
			'code' => 'card_ineligible',
			'name' => 'text_3ds_card_ineligible',
			'error' => 'error_3ds_card_ineligible'
		)
	)
);
?>