/*
 * Copyright (c) 2018, Ryo Currency Project
 * Copyright (c) 2019, Masari Project
*/
function masari_showNotification(message, type='success') {
    var toast = jQuery('<div class="' + type + '"><span>' + message + '</span></div>');
    jQuery('#masari_toast').append(toast);
    toast.animate({ "right": "12px" }, "fast");
    setInterval(function() {
        toast.animate({ "right": "-400px" }, "fast", function() {
            toast.remove();
        });
    }, 2500)
}
function masari_fetchDetails() {
    var data = {
        '_': jQuery.now(),
        'order_id': masari_details.order_id
    };
    jQuery.get(masari_ajax_url, data, function(response) {
        if (typeof response.error !== 'undefined') {
            console.log(response.error);
        } else {
            masari_details = response;
            masari_updateDetails();
        }
    });
}

function masari_updateDetails() {

    var details = masari_details;
    jQuery('#masari_payment_messages').children().hide();
    switch(details.status) {
        case 'unpaid':
            jQuery('.masari_payment_unpaid').show();
            jQuery('.masari_payment_expire_time').html(details.order_expires);
            break;
        case 'partial':
            jQuery('.masari_payment_partial').show();
            jQuery('.masari_payment_expire_time').html(details.order_expires);
            break;
        case 'paid':
            jQuery('.masari_payment_paid').show();
            jQuery('.masari_confirm_time').html(details.time_to_confirm);
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'confirmed':
            jQuery('.masari_payment_confirmed').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'expired':
            jQuery('.masari_payment_expired').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'expired_partial':
            jQuery('.masari_payment_expired_partial').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
    }

    jQuery('#masari_exchange_rate').html('1 MSR = '+details.rate_formatted+' '+details.currency);
    jQuery('#masari_total_amount').html(details.amount_total_formatted);
    jQuery('#masari_total_paid').html(details.amount_paid_formatted);
    jQuery('#masari_total_due').attr('value', details.amount_due_formatted);

    jQuery('#masari_integrated_address').attr('value', details.integrated_address);

    if(masari_show_qr) {
        var qr = jQuery('#masari_qr_code').html('');
        new QRCode(qr.get(0), details.qrcode_uri);
    }

    if(details.txs.length) {
        jQuery('#masari_tx_table').show();
        jQuery('#masari_tx_none').hide();
        jQuery('#masari_tx_table tbody').html('');
        for(var i=0; i < details.txs.length; i++) {
            var tx = details.txs[i];
            var height = tx.height == 0 ? 'N/A' : tx.height;
            var row = ''+
                '<tr>'+
                '<td style="word-break: break-all">'+
                '<a href="'+masari_explorer_url+'/tx/'+tx.txid+'" target="_blank">'+tx.txid+'</a>'+
                '</td>'+
                '<td>'+height+'</td>'+
                '<td>'+tx.amount_formatted+' Masari</td>'+
                '</tr>';

            jQuery('#masari_tx_table tbody').append(row);
        }
    } else {
        jQuery('#masari_tx_table').hide();
        jQuery('#masari_tx_none').show();
    }

    // Show state change notifications
    var new_txs = details.txs;
    var old_txs = masari_order_state.txs;
    if(new_txs.length != old_txs.length) {
        for(var i = 0; i < new_txs.length; i++) {
            var is_new_tx = true;
            for(var j = 0; j < old_txs.length; j++) {
                if(new_txs[i].txid == old_txs[j].txid && new_txs[i].amount == old_txs[j].amount) {
                    is_new_tx = false;
                    break;
                }
            }
            if(is_new_tx) {
                masari_showNotification('Transaction received for '+new_txs[i].amount_formatted+' Masari');
            }
        }
    }

    if(details.status != masari_order_state.status) {
        switch(details.status) {
            case 'paid':
                masari_showNotification('Your order has been paid in full');
                break;
            case 'confirmed':
                masari_showNotification('Your order has been confirmed');
                break;
            case 'expired':
            case 'expired_partial':
                masari_showNotification('Your order has expired', 'error');
                break;
        }
    }

    masari_order_state = {
        status: masari_details.status,
        txs: masari_details.txs
    };

}
jQuery(document).ready(function($) {
    if (typeof masari_details !== 'undefined') {
        masari_order_state = {
            status: masari_details.status,
            txs: masari_details.txs
        };
        setInterval(masari_fetchDetails, 30000);
        masari_updateDetails();
        new ClipboardJS('.clipboard').on('success', function(e) {
            e.clearSelection();
            if(e.trigger.disabled) return;
            switch(e.trigger.getAttribute('data-clipboard-target')) {
                case '#masari_integrated_address':
                    masari_showNotification('Copied destination address!');
                    break;
                case '#masari_total_due':
                    masari_showNotification('Copied total amount due!');
                    break;
            }
            e.clearSelection();
        });
    }
});