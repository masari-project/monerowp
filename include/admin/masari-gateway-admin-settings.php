<?php

defined( 'ABSPATH' ) || exit;

return array(
    'enabled' => array(
        'title' => __('Enable / Disable', 'masari_gateway'),
        'label' => __('Enable this payment gateway', 'masari_gateway'),
        'type' => 'checkbox',
        'default' => 'no'
    ),
    'title' => array(
        'title' => __('Title', 'masari_gateway'),
        'type' => 'text',
        'desc_tip' => __('Payment title the customer will see during the checkout process.', 'masari_gateway'),
        'default' => __('Masari Gateway', 'masari_gateway')
    ),
    'description' => array(
        'title' => __('Description', 'masari_gateway'),
        'type' => 'textarea',
        'desc_tip' => __('Payment description the customer will see during the checkout process.', 'masari_gateway'),
        'default' => __('Pay securely using Masari. You will be provided payment details after checkout.', 'masari_gateway')
    ),
    'discount' => array(
        'title' => __('Discount for using Masari', 'masari_gateway'),
        'desc_tip' => __('Provide a discount to your customers for making a private payment with Masari', 'masari_gateway'),
        'description' => __('Enter a percentage discount (i.e. 5 for 5%) or leave this empty if you do not wish to provide a discount', 'masari_gateway'),
        'type' => __('number'),
        'default' => '0'
    ),
    'valid_time' => array(
        'title' => __('Order valid time', 'masari_gateway'),
        'desc_tip' => __('Amount of time order is valid before expiring', 'masari_gateway'),
        'description' => __('Enter the number of seconds that the funds must be received in after order is placed. 3600 seconds = 1 hour', 'masari_gateway'),
        'type' => __('number'),
        'default' => '3600'
    ),
    'confirms' => array(
        'title' => __('Number of confirmations', 'masari_gateway'),
        'desc_tip' => __('Number of confirms a transaction must have to be valid', 'masari_gateway'),
        'description' => __('Enter the number of confirms that transactions must have. Enter 0 to zero-confim. Each confirm will take approximately four minutes', 'masari_gateway'),
        'type' => __('number'),
        'default' => '5'
    ),
    'confirm_type' => array(
        'title' => __('Confirmation Type', 'masari_gateway'),
        'desc_tip' => __('Select the method for confirming transactions', 'masari_gateway'),
        'description' => __('Select the method for confirming transactions', 'masari_gateway'),
        'type' => 'select',
        'options' => array(
            'viewkey'        => __('viewkey', 'masari_gateway'),
            'masari-wallet-rpc' => __('masari-wallet-rpc', 'masari_gateway')
        ),
        'default' => 'viewkey'
    ),
    'masari_address' => array(
        'title' => __('Masari Address', 'masari_gateway'),
        'label' => __('Useful for people that have not a daemon online'),
        'type' => 'text',
        'desc_tip' => __('Masari Wallet Address (MasariL)', 'masari_gateway')
    ),
    'viewkey' => array(
        'title' => __('Secret Viewkey', 'masari_gateway'),
        'label' => __('Secret Viewkey'),
        'type' => 'text',
        'desc_tip' => __('Your secret Viewkey', 'masari_gateway')
    ),
    'daemon_host' => array(
        'title' => __('Masari wallet RPC Host/IP', 'masari_gateway'),
        'type' => 'text',
        'desc_tip' => __('This is the Daemon Host/IP to authorize the payment with', 'masari_gateway'),
        'default' => '127.0.0.1',
    ),
    'daemon_port' => array(
        'title' => __('Masari wallet RPC port', 'masari_gateway'),
        'type' => __('number'),
        'desc_tip' => __('This is the Wallet RPC port to authorize the payment with', 'masari_gateway'),
        'default' => '18080',
    ),
    'testnet' => array(
        'title' => __(' Testnet', 'masari_gateway'),
        'label' => __(' Check this if you are using testnet ', 'masari_gateway'),
        'type' => 'checkbox',
        'description' => __('Advanced usage only', 'masari_gateway'),
        'default' => 'no'
    ),
    'javascript' => array(
        'title' => __(' Javascript', 'monero_gateway'),
        'label' => __(' Check this to ENABLE Javascript in Checkout page ', 'monero_gateway'),
        'type' => 'checkbox',
        'default' => 'no'
     ),
    'onion_service' => array(
        'title' => __(' SSL warnings ', 'masari_gateway'),
        'label' => __(' Check to Silence SSL warnings', 'masari_gateway'),
        'type' => 'checkbox',
        'description' => __('Check this box if you are running on an Onion Service (Suppress SSL errors)', 'masari_gateway'),
        'default' => 'no'
    ),
    'show_qr' => array(
        'title' => __('Show QR Code', 'masari_gateway'),
        'label' => __('Show QR Code', 'masari_gateway'),
        'type' => 'checkbox',
        'description' => __('Enable this to show a QR code after checkout with payment details.'),
        'default' => 'no'
    ),
    'use_masari_price' => array(
        'title' => __('Show Prices in Masari', 'masari_gateway'),
        'label' => __('Show Prices in Masari', 'masari_gateway'),
        'type' => 'checkbox',
        'description' => __('Enable this to convert ALL prices on the frontend to Masari (experimental)'),
        'default' => 'no'
    ),
    'use_masari_price_decimals' => array(
        'title' => __('Display Decimals', 'masari_gateway'),
        'type' => __('number'),
        'description' => __('Number of decimal places to display on frontend. Upon checkout exact price will be displayed.'),
        'default' => 12,
    ),
);
