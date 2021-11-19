<?php 
$_['paypal_setting'] = array(
	'partner' => array(
		'production' => array(
			'partner_id' => 'TY2Q25KP2PX9L',
			'client_id' => 'AbjxI4a9fMnew8UOMoDFVwSh7h1aeOBaXpd2wcccAnuqecijKIylRnNguGRWDrEPrTYraBQApf_-O3_4',
			'partner_attribution_id' => 'OPENCARTLIMITED_Cart_OpenCartPCP'
		),
		'sandbox' => array(
			'partner_id' => 'EJNHWRJJNB38L',
			'client_id' => 'AfeIgIr-fIcEucsVXvdq21Ufu0wAALWhgJdVF4ItUK1IZFA9I4JIRdfyJ9vWrd9oi0B6mBGtJYDrlYsG',
			'partner_attribution_id' => 'OPENCARTLIMITED_Cart_OpenCartPCP'
		)
	),
	'order_status' => array(
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
			'button_label' => 'paypal',
			'button_funding' => array(
				'card' => 0,
				'credit' => 0,
				'bancontact' => 0,
				'blik' => 0,
				'eps' => 0,
				'giropay' => 0,
				'ideal' => 0,
				'mercadopago' => 0,
				'mybank' => 0,
				'p24' => 0,
				'sepa' => 0,
				'sofort' => 0,
				'venmo' => 0,
				'paylater' => 0
			)
		),
		'card' => array(
			'status' => false,
			'form_align' => 'right',
			'form_size' => 'large',
			'secure_status' => true,
			'secure_scenario' => array(
				'failed_authentication' => 0,
				'rejected_authentication' => 0,
				'attempted_authentication' => 1,
				'unable_authentication' => 0,
				'challenge_authentication' => 0,
				'card_ineligible' => 1,
				'system_unavailable' => 0,
				'system_bypassed' => 1
			)
		),
		'message' => array(
			'status' => true,
			'message_align' => 'right',
			'message_size' => 'large',
			'message_layout' => 'text',
			'message_text_color' => 'black',
			'message_text_size' => '12',
			'message_flex_color' => 'blue',
			'message_flex_ratio' => '8x1'
		)
	),
	'currency' => array(
		'AUD' => array(
			'code' => 'AUD',
			'name' => 'text_currency_aud',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'BRL' => array(
			'code' => 'BRL',
			'name' => 'text_currency_brl',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => false
		),
		'CAD' => array(
			'code' => 'CAD',
			'name' => 'text_currency_cad',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'CZK' => array(
			'code' => 'CZK',
			'name' => 'text_currency_czk',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'DKK' => array(
			'code' => 'DKK',
			'name' => 'text_currency_dkk',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'EUR' => array(
			'code' => 'EUR',
			'name' => 'text_currency_eur',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'HKD' => array(
			'code' => 'HKD',
			'name' => 'text_currency_hkd',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'HUF' => array(
			'code' => 'HUF',
			'name' => 'text_currency_huf',
			'decimal_place' => 0,
			'express_status' => true,
			'card_status' => true
		),
		'INR' => array(
			'code' => 'INR',
			'name' => 'text_currency_inr',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => false
		),
		'ILS' => array(
			'code' => 'ILS',
			'name' => 'text_currency_ils',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => false
		),
		'JPY' => array(
			'code' => 'JPY',
			'name' => 'text_currency_jpy',
			'decimal_place' => 0,
			'express_status' => true,
			'card_status' => true
		),
		'MYR' => array(
			'code' => 'MYR',
			'name' => 'text_currency_myr',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => false
		),
		'MXN' => array(
			'code' => 'MXN',
			'name' => 'text_currency_mxn',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => false
		),
		'TWD' => array(
			'code' => 'TWD',
			'name' => 'text_currency_twd',
			'decimal_place' => 0,
			'express_status' => true,
			'card_status' => false
		),
		'NZD' => array(
			'code' => 'NZD',
			'name' => 'text_currency_nzd',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'NOK' => array(
			'code' => 'NOK',
			'name' => 'text_currency_nok',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'PHP' => array(
			'code' => 'PHP',
			'name' => 'text_currency_php',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => false
		),
		'PLN' => array(
			'code' => 'PLN',
			'name' => 'text_currency_pln',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'GBP' => array(
			'code' => 'GBP',
			'name' => 'text_currency_gbp',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'RUB' => array(
			'code' => 'RUB',
			'name' => 'text_currency_rub',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => false
		),
		'SGD' => array(
			'code' => 'SGD',
			'name' => 'text_currency_sgd',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'SEK' => array(
			'code' => 'SEK',
			'name' => 'text_currency_sek',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'CHF' => array(
			'code' => 'CHF',
			'name' => 'text_currency_chf',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
		),
		'THB' => array(
			'code' => 'THB',
			'name' => 'text_currency_thb',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => false
		),
		'USD' => array(
			'code' => 'USD',
			'name' => 'text_currency_usd',
			'decimal_place' => 2,
			'express_status' => true,
			'card_status' => true
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
	'button_funding' => array(
		'card' => array(
			'code' => 'card',
			'name' => 'text_card',
		),
		'credit' => array(
			'code' => 'credit',
			'name' => 'text_credit',
		),
		'bancontact' => array(
			'code' => 'bancontact',
			'name' => 'text_bancontact',
		),
		'bancontact' => array(
			'code' => 'bancontact',
			'name' => 'text_bancontact',
		),
		'blik' => array(
			'code' => 'blik',
			'name' => 'text_blik',
		),
		'eps' => array(
			'code' => 'eps',
			'name' => 'text_eps',
		),
		'giropay' => array(
			'code' => 'giropay',
			'name' => 'text_giropay',
		),
		'ideal' => array(
			'code' => 'ideal',
			'name' => 'text_ideal',
		),
		'mercadopago' => array(
			'code' => 'mercadopago',
			'name' => 'text_mercadopago',
		),
		'mybank' => array(
			'code' => 'mybank',
			'name' => 'text_mybank',
		),
		'p24' => array(
			'code' => 'p24',
			'name' => 'text_p24',
		),
		'sepa' => array(
			'code' => 'sepa',
			'name' => 'text_sepa',
		),
		'sofort' => array(
			'code' => 'sofort',
			'name' => 'text_sofort',
		),
		'venmo' => array(
			'code' => 'venmo',
			'name' => 'text_venmo',
		),
		'paylater' => array(
			'code' => 'paylater',
			'name' => 'text_paylater',
		)
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
		'failed_authentication' => array(
			'code' => 'failed_authentication',
			'name' => 'text_3ds_failed_authentication',
			'error' => 'error_3ds_failed_authentication',
			'recommended' => 0
		),
		'rejected_authentication' => array(
			'code' => 'rejected_authentication',
			'name' => 'text_3ds_rejected_authentication',
			'error' => 'error_3ds_rejected_authentication',
			'recommended' => 0
		),
		'attempted_authentication' => array(
			'code' => 'attempted_authentication',
			'name' => 'text_3ds_attempted_authentication',
			'error' => 'error_3ds_attempted_authentication',
			'recommended' => 1
		),
		'unable_authentication' => array(
			'code' => 'unable_authentication',
			'name' => 'text_3ds_unable_authentication',
			'error' => 'error_3ds_unable_authentication',
			'recommended' => 0
		),
		'challenge_authentication' => array(
			'code' => 'challenge_authentication',
			'name' => 'text_3ds_challenge_authentication',
			'error' => 'error_3ds_challenge_authentication',
			'recommended' => 0
		),
		'card_ineligible' => array(
			'code' => 'card_ineligible',
			'name' => 'text_3ds_card_ineligible',
			'error' => 'error_3ds_card_ineligible',
			'recommended' => 1
		),
		'system_unavailable' => array(
			'code' => 'system_unavailable',
			'name' => 'text_3ds_system_unavailable',
			'error' => 'error_3ds_system_unavailable',
			'recommended' => 0
		),
		'system_bypassed' => array(
			'code' => 'system_bypassed',
			'name' => 'text_3ds_system_bypassed',
			'error' => 'error_3ds_system_bypassed',
			'recommended' => 1
		)
	),
	'message_align' => array(
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
	'message_size' => array(
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
	'message_width' => array(
		'small' => '200px',
		'medium' => '250px',
		'large' => '350px',
		'responsive' => ''
	),
	'message_layout' => array(
		'text' => array(
			'code' => 'text',
			'name' => 'text_text'
		),
		'flex' => array(
			'code' => 'flex',
			'name' => 'text_flex'
		)
	),
	'message_text_color' => array(
		'black' => array(
			'code' => 'black',
			'name' => 'text_black'
		),
		'white' => array(
			'code' => 'white',
			'name' => 'text_white'
		)
	),
	'message_text_size' => array('10', '11', '12', '13', '14', '15', '16'),
	'message_flex_color' => array(
		'blue' => array(
			'code' => 'blue',
			'name' => 'text_blue'
		),
		'black' => array(
			'code' => 'black',
			'name' => 'text_black'
		),
		'white' => array(
			'code' => 'white',
			'name' => 'text_white'
		)
	),
	'message_flex_ratio' => array('1x1', '1x4', '8x1', '20x1')
);
?>