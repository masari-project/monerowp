# Masari Gateway for WooCommerce

## Features

* Payment validation done through either `masari-wallet-rpc` or the [msrchain.net blockchain explorer](https://msrchain.net/).
* Validates payments with `cron`, so does not require users to stay on the order confirmation page for their order to validate.
* Order status updates are done through AJAX instead of Javascript page reloads.
* Customers can pay with multiple transactions and are notified as soon as transactions hit the mempool.
* Configurable block confirmations, from `0` for zero confirm to `60` for high ticket purchases.
* Live price updates every minute; total amount due is locked in after the order is placed for a configurable amount of time (default 60 minutes) so the price does not change after order has been made.
* Hooks into emails, order confirmation page, customer order history page, and admin order details page.
* View all payments received to your wallet with links to the blockchain explorer and associated orders.
* Optionally display all prices on your store in terms of Masari.
* Shortcodes! Display exchange rates in numerous currencies.

## Requirements

* Masari wallet to receive payments - [GUI](https://github.com/masari-project/masari-wallet-gui/releases) - [CLI](https://github.com/masari-project/masari/releases) - [Web](https://wallet.getmasari.org/) - [Android](https://play.google.com/store/apps/details?id=org.masari.mobilewallet) - [Paper](https://getmasari.org/paper-wallet-generator.html)
* [BCMath](http://php.net/manual/en/book.bc.php) - A PHP extension used for arbitrary precision maths

## Installing the plugin

* Download the plugin from the [releases page](https://github.com/masari-project/masariwp) or clone with `git clone https://github.com/masari-project/masariwp`
* Unzip or place the `masari-woocommerce-gateway` folder in the `wp-content/plugins` directory.
* Activate "Masari Woocommerce Gateway" in your WordPress admin dashboard.
* It is highly recommended that you use native cronjobs instead of WordPress's "Poor Man's Cron" by adding `define('DISABLE_WP_CRON', true);` into your `wp-config.php` file and adding `* * * * * wget -q -O - https://yourstore.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1` to your crontab.

## Wallet setup

Create a wallet for your store using any of the wallets mentioned under the Requirements section. For the following wallet setups, you will need:
* Your Masari wallet address
* Your Masari wallet viewkey

We recommend using the `masari-wallet-rpc` options below. If you decide to use the `masari-wallet-rpc` methods, we recommend you use a view-only wallet. 

To create a view only wallet, launch `masari-wallet-cli` with parameters `--generate-from-view-key wallet_name_here` where `wallet_name_here` can be anything that does not currently exist. Follow the prompts. It will ask for your address and viewkey.

### Quick and Easy!

The quickest and easiest way to start accepting Masari is to use your own instance of `masari-wallet-rpc` connected to a remote daemon. You can run this on your web server or use another server to host the wallet. If running `masari-wallet-rpc` from a server that is not your webserver, be sure to use a view-only wallet.

If running on your web server, launch the masari-wallet-rpc instance in a `screen` or `tmux` as such
* `masari-wallet-rpc --daemon-host remote_node_url_here --wallet-file wallet_file_here --disable-rpc-login --prompt-for-password --rpc-bind-port pick_a_port`

For a remote node, view a list of open nodes at [nodes.masari.rocks](https://nodes.masari.rocks/). 

## Using local `masari-wallet-rpc` with `masarid`

This is the most secure way to accept Masari on your website. This requires launching the Masari daemon and sychronizing with the network along with creating a wallet.

Requirements: 
* Latest [Masari-currency binaries](https://github.com/masari-project/masari/releases)

After downloading (or compiling) the Masari binaries on your server, you may open a new `screen` or `tmux` session and run `masarid` from there. Alternatively, you may install the [systemd unit files](https://github.com/masari-project/masariwp/tree/master/assets/systemd-unit-files). 

Note on security: using this option, while the most secure, requires you to run the Masari wallet RPC program on your server. Best practice for this is to use a view-only wallet since otherwise your server would be running a hot-wallet and a security breach could allow hackers to empty your funds.

### Verify funds using a block explorer

This is the easiest way to start accepting Masari on your website. You'll need:

* Your Masari wallet address starting with `5`
* Your wallet's secret viewkey

Then simply select the `viewkey` option in the settings page and paste your address and viewkey. You're all set!

Note on privacy: when you validate transactions with your private viewkey, your viewkey is sent to (but not stored on) msrchain.net over HTTPS. This could potentially allow an attacker to see your incoming, but not outgoing, transactions if they were to get their hands on your viewkey. Even if this were to happen, your funds would still be safe and it would be impossible for somebody to steal your money. For maximum privacy use your own `masari-wallet-rpc` instance.

## Configuration

* `Enable / Disable` - Turn on or off Masari gateway. (Default: Disable)
* `Title` - Name of the payment gateway as displayed to the customer. (Default: Masari Gateway)
* `Discount for using Masari` - Percentage discount applied to orders for paying with Masari. Can also be negative to apply a surcharge. (Default: 0)
* `Order valid time` - Number of seconds after order is placed that the transaction must be seen in the mempool. (Default: 3600 [1 hour])
* `Number of confirmations` - Number of confirmations the transaction must recieve before the order is marked as complete. Use `0` for nearly instant confirmation. (Default: 5)
* `Confirmation Type` - Confirm transactions with either your viewkey, or by using `masari-wallet-rpc`. (Default: viewkey)
* `Masari Address` (if confirmation type is viewkey) - Your public Masari address starting with 4. (No default)
* `Secret Viewkey` (if confirmation type is viewkey) - Your *private* viewkey (No default)
* `Masari wallet RPC Host/IP` (if confirmation type is `masari-wallet-rpc`) - IP address where the wallet rpc is running. It is highly discouraged to run the wallet anywhere other than the local server! (Default: 127.0.0.1)
* `Masari wallet RPC port` (if confirmation type is `masari-wallet-rpc`) - Port the wallet rpc is bound to with the `--rpc-bind-port` argument. (Default 18080)
* `Testnet` - Check this to change the blockchain explorer links to the testnet explorer. (Default: unchecked)
* `SSL warnings` - Check this to silence SSL warnings. (Default: unchecked)
* `Show QR Code` - Show payment QR codes. (Default: unchecked)
* `Show Prices in Masari` - Convert all prices on the frontend to Masari. Experimental feature, only use if you do not accept any other payment option. (Default: unchecked)
* `Display Decimals` (if show prices in Masari is enabled) - Number of decimals to round prices to on the frontend. The final order amount will not be rounded and will be displayed down to the nanoMasari. (Default: 12)

## Shortcodes

This plugin makes available two shortcodes that you can use in your theme.

#### Live price shortcode

This will display the price of Masari in the selected currency. If no currency is provided, the store's default currency will be used.

```
[masari-price]
[masari-price currency="BTC"]
[masari-price currency="USD"]
[masari-price currency="CAD"]
[masari-price currency="EUR"]
[masari-price currency="GBP"]
```
Will display:
```
1 MSR = 0.03 USD
1 MSR = 0.00000400 BTC
1 MSR = 0.03 USD
1 MSR = 0.02 CAD
1 MSR = 0.02 EUR
1 MSR = 0.02 GBP
```

#### Masari accepted here badge

This will display a badge showing that you accept Masari-currency.

`[masari-accepted-here]`

![Masari Accepted Here](/assets/images/masari-accepted-here.png?raw=true "Masari Accepted Here")

## Donations

monero-integrations: 44krVcL6TPkANjpFwS2GWvg1kJhTrN7y9heVeQiDJ3rP8iGbCd5GeA4f3c2NKYHC1R4mCgnW7dsUUUae2m9GiNBGT4T8s2X (serhack, original author)

ryo-currency: 4A6BQp7do5MTxpCguq1kAS27yMLpbHcf89Ha2a8Shayt2vXkCr6QRpAXr1gLYRV5esfzoK3vLJTm5bDWk5gKmNrT6s6xZep (mosu-forge, contributor)
