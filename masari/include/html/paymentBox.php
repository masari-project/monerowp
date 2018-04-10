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
<script>window.ajaxurl = '<?php echo $ajaxurl ?>';</script>
<script>window.orderId = '<?php echo $order_id ?>';</script>
<script>
	function update() {
		jQuery(document).ready(function () {
			jQuery.post(
				ajaxurl+'?_d='+Date.now(),
				{
					'action': 'masari_gateway_ajax_reload',
					'order_id': window.orderId
				},
				function (response) {
					if(typeof response === 'string')
						response = JSON.parse(response);
					
					jQuery('#masari_gateway_payment_wait').hide();
					jQuery('#masari_gateway_payment_process').hide();
					jQuery('#masari_gateway_payment_success').hide();

					if(response.confirmed === true) {
						jQuery('#masari_gateway_payment_success').show();
						clearInterval(intervalRefreshStatus);
					}else if(response.currentConfirmations === null)
						jQuery('#masari_gateway_payment_wait').show();
					else{
						jQuery('#masari_gateway_payment_process').show();
						jQuery('#masari_gateway .count_currentConfirmations').html(response.currentConfirmations);
						jQuery('#masari_gateway .count_maxConfirmations').html(response.maxConfirmation);
						jQuery('#masari_gateway .meter .progress').css('width',''+Math.floor(response.currentConfirmations/response.maxConfirmation*100)+'%');
					}
					console.log(response);
				}
			);
		});
	}
	
	var intervalRefreshStatus = setInterval(function(){
		update();
	}, <?= $this->reloadTime; ?>);
</script>

<div id="masari_gateway" class="msr-payment-container <?php echo $displayedDarkTheme ? 'dark' : ''; ?>">
	<div class="header">
		<img src="<?= $pluginDirectory?>assets/masari_icon.png" alt="Masari" />
		<?php _e('Masari Payment', $pluginIdentifier) ?>
	</div>
	<div class="content">
		<?php if($amount_msr2===null): ?>
			<div class="status message important critical" id="masari_gateway_error_generic">
				<?php _e('Your transaction cannot be processed currently. If you are the shop owner, please check your configuration', $pluginIdentifier) ?>
			</div>
		<?php endif; ?>
		
		<div id="masari_gateway_payment_process" <?php if(!($displayedCurrentConfirmation !== null && $displayedCurrentConfirmation >= 0 && !$transactionConfirmed)): ?>style="display:none"<?php endif; ?>>
			<div class="status message important info">
				<i class="material-icons rotating" >replay</i>
				<?php _e('Your payment is being processed', $pluginIdentifier) ?> (<span class="count_currentConfirmations" ><?= $displayedCurrentConfirmation ?></span>/<span class="count_maxConfirmations" ><?= $displayedMaxConfirmation ?></span> <?php _e('confirmations', $pluginIdentifier) ?>)
			</div>
			<div class="meter">
				<span class="progress" style="width: <?php echo $displayedCurrentConfirmation/$displayedMaxConfirmation*100; ?>%"></span>
				<span class="text" >(<span class="count_currentConfirmations" ><?= $displayedCurrentConfirmation ?></span>/<span class="count_maxConfirmations" ><?= $displayedMaxConfirmation ?></span>) <?php _e('confirmations', $pluginIdentifier) ?></span>
			</div>
		</div>
	
		<div id="masari_gateway_payment_wait" <?php if(!(!$transactionConfirmed && $displayedCurrentConfirmation === null)): ?>style="display:none"<?php endif; ?>>
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
		</div>
	
		<div class="status message important success" id="masari_gateway_payment_success" <?php if(!$transactionConfirmed): ?>style="display:none"<?php endif; ?> >
			<i class="material-icons" >check</i>
			<?php _e('Your transaction has been successfully confirmed!', $pluginIdentifier) ?>
		</div>
	</div>
	<div class="footer">
		<a href="https://getmasari.org" target="_blank"><?php _e('Help', $pluginIdentifier) ?></a> |
		<a href="https://getmasari.org" target="_blank"><?php _e('About Masari', $pluginIdentifier) ?></a>
	</div>
</div>