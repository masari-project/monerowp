# MasariWP
A WooCommerce extension for accepting Masari currency

## Dependencies
This plugin is rather simple but there are a few things that need to be set up before hand.

* A web server! Ideally with the most recent versions of PHP and mysql

* A Masari wallet. You can find the official wallet [here](https://github.com/masari-project/masari)

* [WordPress](https://wordpress.org)
Wordpress is the backend tool that is needed to use WooCommerce and this Masari plugin

* [WooCommerce](https://woocommerce.com)
This Masari plugin is an extension of WooCommerce, which works with WordPress

## Step 1: Activating the plugin
* Downloading: First of all, you will need to download the plugin. You can download the latest source code from GitHub. This can be done with the command `git clone https://github.com/masari-project/masariwp.git`

* Put the plugin in the correct directory: You will need to put the folder named `masari` from this repo/unzipped release into the wordpress plugins directory. This can be found at `path/to/wordpress/folder/wp-content/plugins`

* Activate the plugin from the WordPress admin panel: Once you login to the admin panel in WordPress, click on "Installed Plugins" under "Plugins". Then simply click "Activate" where it says "Masari - WooCommerce Gateway"

## Step 2 Option 1: Use your wallet address and viewkey

* Get your Masari wallet address starting with '5'
* Get your wallet secret viewkey from your wallet

A note on privacy: When you validate transactions with your private viewkey, your viewkey is sent to (but not stored on) msrchain.net over HTTPS. This could potentally allow an attacker to see your incoming, but not outgoing, transactions if he were to get his hands on your viewkey. Even if this were to happen, your funds would still be safe and it would be impossible for somebody to steal your money. For maximum privacy use your own masari-wallet-rpc instance.

## Step 2 Option 2: Get a masari daemon to connect to

### Running a full node

To do this: start the masari daemon on your server and leave it running in the background. This can be accomplished by running `./masarid` inside your masari downloads folder. The first time that you start your node, the masari daemon will download and sync the entire masari blockchain. This can take several hours and is best done on a machine with at least 4GB of ram, an SSD hard drive (with at least 5GB of free space), and a high speed internet connection.

### Setup your  masari wallet-rpc

* Setup a masari wallet using the masari-wallet-cli tool. If you do not know how to do this you can learn about it at [getmasari.org](https://getmasari.org)

* [Create a view-only wallet from that wallet for security.](https://monero.stackexchange.com/questions/3178/how-to-create-a-view-only-wallet-for-the-gui/4582#4582)

* Start the Wallet RPC and leave it running in the background. This can be accomplished by running `./masari-wallet-rpc --rpc-bind-port 38082 --disable-rpc-login --log-level 2 --wallet-file /path/viewOnlyWalletFile` where "/path/viewOnlyWalletFile" is the wallet file for your view-only wallet.

## Step 4: Setup Masari Gateway in WooCommerce

* Navigate to the "settings" panel in the WooCommerce widget in the WordPress admin panel.

* Click on "Checkout"

* Select "Masari GateWay"

* Check the box labeled "Enable this payment gateway"

* Check either "Use ViewKey" or "Use masari-wallet-rpc"

If You chose to use viewkey:

* Enter your masari wallet address in the box labled "Masari Address". If you do not know your address, you can run the `address` commmand in your masari wallet

* Enter your secret viewkey in the box labeled "ViewKey"

If you chose to use masari-wallet-rpc:

* Enter your masari wallet address in the box labled "Masari Address". If you do not know your address, you can run the `address` commmand in your masari wallet

* Enter the IP address of your server in the box labeled "Masari wallet rpc Host/IP"

* Enter the port number of the Wallet RPC in the box labeled "Masari wallet rpc port" (it would be `38082` if you used the above example).

Finally:

* Click on "Save changes"

## Donating to the Devs :)
MSR Address: `5iKvFxieyiYfo5yLMwsrXqByn2fb1upm77MTpiyQNpxqjEJWzVrfpDCDpZCNZ48f9xYZM2mG5GrfzP5UCt6bkVwn1xhjERf` (cryptochangements)

XMR Address : `44krVcL6TPkANjpFwS2GWvg1kJhTrN7y9heVeQiDJ3rP8iGbCd5GeA4f3c2NKYHC1R4mCgnW7dsUUUae2m9GiNBGT4T8s2X` (serhack)
