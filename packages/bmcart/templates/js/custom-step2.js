
document.addEventListener('DOMContentLoaded', function(){

    var ajax_config = {url: './bmcart_step2.php'};
    var ajaxObj = new AjaxCompo(ajax_config, {}, {});

    let ptuses = document.querySelectorAll('.ptuse');
    ptuses.forEach(function (ptuse) {
        let point_use_value = ptuse.value;
        ptuse.addEventListener('input', function () {
            var ajax_data = {
                usage_of_point: point_use_value
            };
            var ajax_callback = {
                success: function(data){
                    let ele_point_amount = document.querySelector('input[name=usepoint]');
                    if (point_use_value === '1') {
                        ele_point_amount.disabled = false;
                    } else {
                        ele_point_amount.disabled = true;
                    }
                }
            };
            ajaxObj.sendRequest({}, ajax_data, ajax_callback);
        });
    });

    let ele_point_amount = document.querySelector('input[name=usepoint]');
    ele_point_amount.addEventListener('input', function () {
        let point_amount_value = ele_point_amount.value;
        var ajax_data = {
            usepoint: point_amount_value
        };
        var ajax_callback = {
            success: function(data){
                var updateObj = new updateStep2(data);
            }
        };
        ajaxObj.sendRequest({}, ajax_data, ajax_callback);
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

/* update step2 */
function updateStep2 (data) {
    this.data = data;
    this.basketInfo();
}

updateStep2.prototype.basketInfo = function () {
    let usepoint = this.data.usepoint; document.getElementById('usepoint').innerText = usepoint ;
    let totalprice = this.data.total_price; document.getElementById('totalprice').innerText = totalprice ;
};