<?php foreach($errors as $error): ?>
<div class="error"><p><strong>Masari Gateway Error</strong>: <?php echo $error; ?></p></div>
<?php endforeach; ?>

<h1>Masari Gateway Settings</h1>

<?php if($confirm_type === 'masari-wallet-rpc'): ?>
<div style="border:1px solid #ddd;padding:5px 10px;">
    <?php
         echo 'Wallet height: ' . $balance['height'] . '</br>';
         echo 'Your balance is: ' . $balance['balance'] . '</br>';
         echo 'Unlocked balance: ' . $balance['unlocked_balance'] . '</br>';
         ?>
</div>
<?php endif; ?>

<table class="form-table">
    <?php echo $settings_html ?>
</table>

<h4><a href="https://github.com/masari-project/masariwp">Learn more about using the Masari payment gateway</a></h4>

<script>
function masariUpdateFields() {
    var confirmType = jQuery("#woocommerce_masari_gateway_confirm_type").val();
    if(confirmType == "masari-wallet-rpc") {
        jQuery("#woocommerce_masari_gateway_masari_address").closest("tr").hide();
        jQuery("#woocommerce_masari_gateway_viewkey").closest("tr").hide();
        jQuery("#woocommerce_masari_gateway_daemon_host").closest("tr").show();
        jQuery("#woocommerce_masari_gateway_daemon_port").closest("tr").show();
    } else {
        jQuery("#woocommerce_masari_gateway_masari_address").closest("tr").show();
        jQuery("#woocommerce_masari_gateway_viewkey").closest("tr").show();
        jQuery("#woocommerce_masari_gateway_daemon_host").closest("tr").hide();
        jQuery("#woocommerce_masari_gateway_daemon_port").closest("tr").hide();
    }
    var useMasariPrices = jQuery("#woocommerce_masari_gateway_use_masari_price").is(":checked");
    if(useMasariPrices) {
        jQuery("#woocommerce_masari_gateway_use_masari_price_decimals").closest("tr").show();
    } else {
        jQuery("#woocommerce_masari_gateway_use_masari_price_decimals").closest("tr").hide();
    }
}
masariUpdateFields();
jQuery("#woocommerce_masari_gateway_confirm_type").change(masariUpdateFields);
jQuery("#woocommerce_masari_gateway_use_masari_price").change(masariUpdateFields);
</script>

<style>
#woocommerce_masari_gateway_masari_address,
#woocommerce_masari_gateway_viewkey {
    width: 100%;
}
</style>