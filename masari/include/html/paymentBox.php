<textarea id="clipboardTextarea" style="width:0;height:0;position:absolute;top:0;left:0;"></textarea>
<script>
	function setTextInClipboard(inputId){
		var inputElement = document.getElementById(inputId);
		var textarea = document.getElementById('clipboardTextarea');
		textarea.value = inputElement.value;
		textarea.select();
		try {
			document.execCommand('copy');
		} catch (err) {
		}

	}
</script>

<div class="msr-payment-container <?php echo $displayedDarkTheme ? 'dark' : ''; ?>">
	<div class="header">
		<img src="<?= $pluginDirectory?>assets/masari_icon.png" alt="Masari" />
		<?php _e('Masari Payment', $pluginIdentifier) ?>
	</div>
	<div class="content">
	<?php if($amount_msr2===null): ?>
		<div class="status message important critical" >
			<?php _e('Your transaction cannot be processed currently. If you are the shop owner, please check your configuration', $pluginIdentifier) ?>
		</div>
	<?php elseif(!$transactionConfirmed): ?>

		<noscript>
			<div class="status message important critical">
				<?php _e('You must enable javascript in order to confirm your order', $pluginIdentifier) ?>
			</div>
		</noscript>
		<div class="status message important info">
			<i class="material-icons rotating" >replay</i>
			<?php _e('We are waiting for your transaction to be confirmed', $pluginIdentifier) ?>
		</div>
	
		<div class="message important" >
			<?php _e('Please send your MSR with those informations', $pluginIdentifier) ?>
		</div>
	
		<div class="msr-amount-send">
			<div class="data-box" >
				<label><?php _e('Amount', $pluginIdentifier) ?></label>
				<input id="msr_amount" type="text" disabled="disabled" class="value" value="<?= $amount_msr2 ?>">
				<button class="copy" onclick="setTextInClipboard('msr_amount')" title="<?php _e('Copy', $pluginIdentifier) ?>"><i class="material-icons" >content_copy</i></button>
			</div>
			<div class="data-box" >
				<label><?php _e('Address', $pluginIdentifier) ?></label>
				<input id="msr_address" disabled="disabled" type="text" class="value" value="<?= $displayedPaymentAddress ?>">
				<button class="copy" onclick="setTextInClipboard('msr_address')" title="<?php _e('Copy', $pluginIdentifier) ?>"><i class="material-icons" >content_copy</i></button>
			</div>
			<?php if(isset($displayedPaymentId) && $displayedPaymentId !== null): ?>
			<div class="data-box" >
				<label><?php _e('Payment ID', $pluginIdentifier) ?></label>
				<input id="msr_paymentId" type="text" disabled="disabled" class="value" value="<?= $displayedPaymentId ?>">
				<button class="copy" onclick="setTextInClipboard('msr_paymentId')" title="<?php _e('Copy', $pluginIdentifier) ?>"><i class="material-icons" >content_copy</i></button>
			</div>
			<?php endif; ?>
		</div>
		<?php if(isset($qrUri)): ?>
		<div class="qr-code">
			<div class="message important"><?php _e('Or scan QR:', $pluginIdentifier) ?></div>
			<div><img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= $qrUri ?>" /></div>
		</div>
		<?php endif; ?>
	<?php else: ?>
	
		<div class="status message important success" >
			<i class="material-icons" >check</i>
			<?php _e('Your transaction has been successfully confirmed!', $pluginIdentifier) ?>
		</div>
	
	<?php endif; ?>
	</div>
	<div class="footer">
		<a href="https://getmasari.org" target="_blank"><?php _e('Help', $pluginIdentifier) ?></a> |
		<a href="https://getmasari.org" target="_blank"><?php _e('About Masari', $pluginIdentifier) ?></a>
	</div>
</div>

<script type="text/javascript">
	setTimeout(function () { window.location.reload(true); }, <?= $this->reloadTime; ?>);
</script>
