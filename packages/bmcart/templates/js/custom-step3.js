
document.addEventListener('DOMContentLoaded', function(){

    var ajax_config = {url: './bmcart_step3.php'};
    var ajaxObj = new AjaxCompo(ajax_config, {}, {});

    let radio_address_ok = document.querySelectorAll('input[name=address_ok]');
    radio_address_ok.forEach(function (radio_address) {
        let address_ok_value = radio_address.value;
        radio_address.addEventListener('change', function () {
            var ajax_data = {
                address_ok: address_ok_value
            };
            var ajax_callback = {
                success: function(data){
                    addressDisabled();
                }
            };
            ajaxObj.sendRequest({}, ajax_data, ajax_callback);
        });
    });

    let ele_postcode_1 = document.querySelector('input[name=postcode1]');
    ele_postcode_1.addEventListener('input', function () {
        let postcode_1_value = ele_postcode_1.value;
        var ajax_data = {
            postcode1: postcode_1_value
        };
        var ajax_callback = {
            success: function(data){
                var updateObj = new updateStep3(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });


    let ele_postcode_2 = document.querySelector('input[name=postcode2]');
    ele_postcode_2.addEventListener('input', function () {
        let postcode_2_value = ele_postcode_2.value;
        var ajax_data = {
            postcode2: postcode_2_value
        };
        var ajax_callback = {
            success: function(data){
                var updateObj = new updateStep3(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });

    let ele_prefecture_a = document.querySelector('select[name=prefecture]');
    ele_prefecture_a.addEventListener('change', function () {
        let prefecture_a_value = ele_prefecture_a.value;
        var ajax_data = {
            prefecture: prefecture_a_value
        };
        var ajax_callback = {
            success: function(data){
                var updateObj = new updateStep3(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });

    let ele_city = document.querySelector('input[name=city]');
    ele_city.addEventListener('input', function () {
        let city_value = ele_city.value;
        var ajax_data = {
            city: city_value
        };
        var ajax_callback = {
            success: function(data){
                var updateObj = new updateStep3(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });

    let ele_town = document.querySelector('input[name=town]');
    ele_town.addEventListener('input', function () {
        let town_value = ele_town.value;
        var ajax_data = {
            town: town_value
        };
        var ajax_callback = {
            success: function(data){
                var updateObj = new updateStep3(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });

    let ele_address = document.querySelector('input[name=address]');
    ele_address.addEventListener('input', function () {
        let address_value = ele_address.value;
        var ajax_data = {
            address: address_value
        };
        var ajax_callback = {
            success: function(data){
                var updateObj = new updateStep3(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });

    let ele_apartment = document.querySelector('input[name=apartment]');
    ele_apartment.addEventListener('input', function () {
        let apartment_value = ele_apartment.value;
        var ajax_data = {
            apartment: apartment_value
        };
        var ajax_callback = {
            success: function(data){
                var updateObj = new updateStep3(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });

    let radio_cms = document.querySelectorAll('input[name=cm]');
    radio_cms.forEach(function (radio_cm) {
        let radio_cm_value = radio_cm.value;
        radio_cm.addEventListener('change', function () {
            var ajax_data = {
                cm: radio_cm_value
            };
            var ajax_callback = {
                success: function(data){
                    shippingDatetimeDisabled();
                }
            };
            ajaxObj.sendRequest({}, ajax_data, ajax_callback);
        });
    });

    let ele_choicedate = document.querySelector('select[name=choicedate]');
    ele_choicedate.addEventListener('change', function () {
        let choicedate_value = ele_choicedate.value;
        var ajax_data = {
            choicedate: choicedate_value
        };
        var ajax_callback = {
            success: function(data){
                var updateObj = new updateStep3(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });

    let ele_choicetime = document.querySelector('select[name=choicetime]');
    ele_choicetime.addEventListener('change', function () {
        let choicetime_value = ele_choicetime.value;
        var ajax_data = {
            cm2: choicetime_value
        };
        var ajax_callback = {
            success: function(data){
                var updateObj = new updateStep3(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });

    let ele_cm_comment = document.querySelector('textarea[name=cm_comment]');
    ele_cm_comment.addEventListener('input', function () {
        let cm_comment_value = ele_cm_comment.value;
        var ajax_data = {
            cm_comment: cm_comment_value
        };
        var ajax_callback = {
            success: function(data){
                var updateObj = new updateStep3(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
    });

    var addressDisabled = function () {
        var address_ok_checked = $('input[name="address_ok"]:checked').val();
        if (address_ok_checked === '1') {
            $('#postcode1, #postcode2, #prefecture, #city, #town, #address, #apartment').attr('disabled', false);
        } else {
            $('#postcode1, #postcode2, #prefecture, #city, #town, #address, #apartment').attr('disabled', true);
        }
    };

    var shippingDatetimeDisabled = function () {
        var cm_checked = $('input[name="cm"]:checked').val();
        if (cm_checked === '1') {
            $('select[name=choicedate], select[name=choicetime]').attr('disabled', false);
        } else {
            $('select[name=choicedate], select[name=choicetime]').attr('disabled', true);
        }
    };

    addressDisabled();
    shippingDatetimeDisabled();

});

$(function () {

    $('#Shipping').validate({
        submitHandler: function (form) {
            var form = document.getElementById('Shipping');
            var card_buy = document.getElementsByName('card_buy')[0].value;
            if ($('#step1').hasClass('submitted')) {
                return false;
            } else {
                if ($('input[name="address_ok"]:checked').val() === '1') {
                    if ($('input#postcode1').val() == '' || $('input#postcode2').val() == '') {
                        alert('郵便番号の入力をお願いします');
                        return false;
                    }
                    if ($('select#prefectureA').val() == '') {
                        alert('都道府県名の選択をお願いします');
                        return false;
                    }
                    if ($('input#town').val() == '' || $('input#apartment').val() == '' || $('input#town').val() == 'addressname') {
                        alert('住所の入力をお願いします');
                        return false;
                    }
                }

                $('#step1').addClass('submitted');
                if (card_buy === PAYMENT_CREDIT_ID) {
                    getToken();
                } else {
                    form.submit();
                }
            }
        },
        rules: {
            cardNo: {required: true, creditcard: true},
            cardValidTermMM: {required: true},
            cardValidTermYY: {required: true},
            cvc: {required: true},
            name: {required: true}
        },
        messages: {
            cardNo: {required: "カード番号を入力してください。", creditcard: "カード番号が正しくありません。"},
            cardValidTermMM: {required: "カード有効期限(Month)を選択してください。"},
            cardValidTermYY: {required: "カード有効期限(Year)を選択してください。"},
            cvc: {required: "セキュリティコードを入力してください。"},
            name: {required: "カード名義を入力してください。"},
        }
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
//	console.log(this.config);

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

/**
 * Paygent Credit Payment
 */

function getToken() {
    var form = document.getElementById('Shipping');
    var merchant_id = document.getElementById('step1').dataset.merchantId;
    var generate_key = document.getElementById('step1').dataset.generateKey;
    var paygentToken = new PaygentToken();				 //PaygentTokenオブジェクトの生成
    paygentToken.createToken(
        merchant_id,
        generate_key,
        {                                               //第3引数:クレジットカード情報
            card_number:form.cardNo.value,          //クレジットカード番号
            expire_year:form.cardValidTermYY.value,          //有効期限-YY
            expire_month: form.cardValidTermMM.value,       //有効期限-MM
            cvc:form.cvc.value,                          //セキュリティーコード
            name:form.name.value                         //カード名義
        },
        execPurchase                                     //第4引数:コールバック関数(トークン取得後に実行)
    )
}

function execPurchase(response) {
    var form = document.getElementById('Shipping');
    if (response.result == '0000') { //トークン処理結果が正常の場合
        /* カード情報入力フォームから、入力情報を削除。*/
        form.cardNo.removeAttribute('name');
        form.cardValidTermYY.removeAttribute('name');
        form.cardValidTermMM.removeAttribute('name');
        form.cvc.removeAttribute('name');
        form.name.removeAttribute('name');

        /* 予め用意したhidden項目にcreateToken()から応答されたトークン等を設定。*/
        form.token.value = response.tokenizedCardObject.token;
        form.masked_card_number.value = response.tokenizedCardObject.masked_card_number;
        form.valid_until.value = response.tokenizedCardObject.valid_until;
        form.fingerprint.value = response.tokenizedCardObject.fingerprint;
        form.hc.value = response.hc;

        /* カード情報入力フォームをsubmitしてtokenを送信する */
        console.log('success - ');
        console.log(JSON.stringify(response));
        //form.submit();
    } else {
        /* エラー時の処理をここに記述する */
        alert('クレジット決済のトークン取得に失敗しました。解決しない場合は管理者にお問い合わせください。');
        console.log('error - ');
        console.log(response);
    }
}

/* update step3 */
function updateStep3 (data) {
    this.data = data;
    this.commentUpdate();
}

updateStep3.prototype.commentUpdate = function () {
    let remark = this.data.remark; document.getElementById('cm_comment').value = remark ;
};