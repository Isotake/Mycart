document.addEventListener('DOMContentLoaded', function(){

    var ajax_config = {url: './bmcart_step1.php'};
    var ajaxObj = new AjaxCompo(ajax_config, {}, {});

    $(".button_to_add").on("click",function(){
        let item_id = $(this).data('cardid');
        let quantity = $('#num' + item_id).val();
        let price = $('#price' + item_id).text();
        let point = $('#point' + item_id).text();
        let item_data = {};
        item_data[item_id] = {
            'quantity': quantity,
            'price': price,
            'point': point
        };
        let ajax_data = {
            additem: item_data,
        };
        let ajax_callback = {
            success: function(data){
                // console.log(data);
                let updateObj = new updateStep1(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });

    $(".button_to_remove").on("click",function(){
        let item_id = $(this).data('cardid');
        $('#num' + item_id).val('0');
        let quantity = $('#num' + item_id).val();
        let price = $('#price' + item_id).text();
        let point = $('#point' + item_id).text();
        let item_data = {};
        item_data[item_id] = {
            'quantity': quantity,
            'price': price,
            'point': point
        };
        let ajax_data = {
            additem: item_data
        };
        var ajax_callback = {
            success: function(data){
                // console.log(data);
                var updateObj = new updateStep1(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });

    let radio_shippings = document.querySelectorAll('.shipping');
    radio_shippings.forEach(function (radio_shipping) {
        let shipping_method = radio_shipping.value;
        radio_shipping.addEventListener('change', function () {
            var ajax_data = {
                shipping_method: shipping_method
            };
            var ajax_callback = {
                success: function(data){
                    // console.log(data);
                    var updateObj = new updateStep1(data);
                }
            };
            ajaxObj.sendRequest({}, ajax_data, ajax_callback);
        });
    });

    let card_buys = document.querySelectorAll('.payment');
    card_buys.forEach(function (ele_card_buy) {
        let payment_method = ele_card_buy.value;
        ele_card_buy.addEventListener('change', function () {
            var ajax_data = {
                payment_method: payment_method
            };
            var ajax_callback = {
                success: function(data){
                    // console.log(data);
                    var updateObj = new updateStep1(data);
                }
            };
            ajaxObj.sendRequest({}, ajax_data, ajax_callback);
        });
    });
});

/* Ajax Component */
function AjaxCompo(_config, _data, _callback){
    this.config = {};
    this.config.url = (_config.url) ? _config.url : null ;
    this.config.type = (_config.type) ? _config.type : 'POST' ;
    this.data = (_data) ? _data : {} ;
    this.config.datatype= (_config.datatype) ? _config.datatype : 'json' ;
    this.callback = {};
    this.callback.beforesend = (_callback.beforesend) ? _callback.beforesend : function(data, status, xhr){} ;
    this.callback.error = function (xhr, status, errorThrown) {
        switch (xhr.status) {
            case 307: location.href = errorThrown; break;
            default: alert('ajax error: status ' + status);
        }
    } ;
    this.callback.success = (_callback.success) ? _callback.success : function(data, status, xhr){} ;
    this.callback.complete = (_callback.complete) ? _callback.complete : function(xhr, status, xhr){} ;
}

AjaxCompo.prototype.request = function(){
    $.ajax({
        url: this.config.url,
        type: this.config.type,
        data: this.data,
        dataType: this.config.datatype,
        beforeSend: this.callback.beforesend,
        error: this.callback.error,
        success: this.callback.success,
        complete: this.callback.complete
    });
};

AjaxCompo.prototype.sendRequest = function(_config, _data, _callback){
    $.extend(true, this.config, _config);
    $.extend(true, this.data, _data);
    $.extend(true, this.callback, _callback);

    this.request();
};

/* update step1 */
function updateStep1 (data) {
    this.data = data;
    this.itemRows();
    this.basketInfo();
    this.moreBuy();
    this.shipping_choice();
    this.shipping_fees();
    this.payment_choice();
    this.cart_error();
}

updateStep1.prototype.itemRows = function () {
    let basket = this.data.basket;
    for (let item_id in basket) {
        let item = basket[item_id];
        let price = item['price']; document.getElementById('price' + item_id).innerText = price ;
        let quantity = item['quantity']; document.getElementById('num' + item_id).innerText = quantity ;
        let point = item['point']; document.getElementById('point' + item_id).innerText = point ;
        let subtotal = price * quantity; document.getElementById('subtotal' + item_id).innerText = subtotal ;
    }
};

updateStep1.prototype.basketInfo = function () {
    let totalnum = this.data.result_totalnum; document.getElementById('totalnum').innerText = totalnum ;
    let subtotal = this.data.result_total; document.getElementById('subtotal').innerText = subtotal ;

    let excharge = this.data.excharge;
    let excharge_displayed = (excharge !== null) ? '¥' + excharge : '-' ;
    document.getElementById('excharge').innerText = excharge_displayed ;

    let totalpoint = this.data.result_totalpoint; document.getElementById('totalpoint').innerText = totalpoint ;

    let totalprice = this.data.total_price;
    let totalprice_displayed = (totalprice !== null) ? '¥' + totalprice : '-' ;
    document.getElementById('totalprice').innerText = totalprice_displayed ;
};

updateStep1.prototype.moreBuy = function () {
    let morebuy_message = this.data.morebuy_message; document.getElementById('more-buy').innerHTML = morebuy_message ;
};

updateStep1.prototype.shipping_choice = function () {
    let shipping_method = this.data.shippping_method;
    document.querySelectorAll('shipping').checked = false;
    switch (shipping_method) {
        case SHIPPING_YUPACKET_ID:
            document.getElementById('shipping_yupacket').checked = true;
            break;
        case SHIPPING_NEKOPOS_ID:
            document.getElementById('shipping_nekopos').checked = true;
            break;
        case SHIPPING_SAGAWA_ID:
            document.getElementById('shipping_sagawa').checked = true;
            break;
        case SHIPPING_YAMATO_ID:
            document.getElementById('shipping_yamato').checked = true;
            break;
    }
};

updateStep1.prototype.shipping_fees = function () {
    let result_yupacket_fee = this.data.result_yupacket_fee;
    let result_nekopos_fee = this.data.result_nekopos_fee;
    let result_sagawa_fee = this.data.result_sagawa_fee;
    let result_yamato_fee = this.data.result_yamato_fee;

    if (this.data.result_mail_allowed) {
        let ele_yupacket = document.getElementById('shipping_yupacket_pr');
        ele_yupacket.dataset.pr = result_yupacket_fee;
        ele_yupacket.innerText = result_yupacket_fee;

        let ele_nekopos = document.getElementById('shipping_nekopos_pr');
        ele_nekopos.dataset.pr = result_nekopos_fee;
        ele_nekopos.innerText = result_nekopos_fee;
    }

    let ele_sagawa = document.getElementById('shipping_sagawa_pr');
    ele_sagawa.dataset.pr = result_sagawa_fee;
    ele_sagawa.innerText = result_sagawa_fee;

    let ele_yamato = document.getElementById('shipping_yamato_pr');
    ele_yamato.dataset.pr = result_yamato_fee;
    ele_yamato.innerText = result_yamato_fee;
};

updateStep1.prototype.payment_choice = function () {
    let payment_method = this.data.payment_method;
    document.querySelectorAll('card_buy').checked = false;
    switch (payment_method) {
        case PAYMENT_CREDIT_ID:
            document.getElementById('credit').checked = true;
            break;
        case PAYMENT_DELIVERY_ID:
            document.getElementById('cashon').checked = true;
            break;
        case PAYMENT_BANK_ID:
            document.getElementById('bank').checked = true;
            break;
    }
};

updateStep1.prototype.cart_error = function () {
    let cart_error = this.data.request_cart_error;
    if (!cart_error) {
        let cart_errors = document.querySelectorAll('.cart-error');
        cart_errors.forEach(function (cart_error) {
            cart_error.classList.add('hidden');
        });
        return;
    }

    if (cart_error.indexOf('shipping') == -1) {
        document.querySelector('.incorrect_shipping_method').classList.add('hidden');
    }

    if (cart_error.indexOf('payment') == -1) {
        document.querySelector('.incorrect_payment_method').classList.add('hidden');
    }

    if (cart_error.indexOf('stock_error') == -1) {
        document.querySelector('.stock_error').classList.add('hidden');
    }
}