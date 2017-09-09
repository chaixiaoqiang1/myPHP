<script type="text/javascript">
function createOrderController($scope, $http, alertService,$modal) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.amounts = {};
    $scope.processFrom = function(url) {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : url,
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            if(data.res){
                alertService.add('success',data.msg);
            }else{
                alertService.add('danger',data.msg);
            }
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
    $scope.getPayment = function() {
        $http({
            'method' : 'post',
            'url'    : '/payment/get-payments',
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $scope.payment = data;
        }).error(function(data) {
            alertService.add('danger', data.error, 2000);
        });
    };
}

</script>
<div class="col-xs-12" ng-controller="createOrderController">
    <div class="row" >
        <div class="eb-content">
            <form action="/platform-api/payment/createorder" method="post" onsubmit="return false;" ng-submit="processFrom('/platform-api/payment/createorder')">

                <div class="form-group">
                    <select class="form-control" name="type" ng-model="formData.type" ng-init="formData.type=0" id="type" ng-change="getPayment()">
                        <option value="0">选择支付类型</option>
                        <?php foreach ($types as $k => $v) { ?>
                            <option value="<?php echo $v->pay_type_id?>"><?php echo $v->pay_type_name;?></option>
                        <?php } ?>      
                    </select>
                </div>
                <div class="form-group" >
                    <select name="payment" id="payment" ng-model="formData.payment"   class="form-control" >
                        <option value="">选择支付方法</option>
                        <option ng-repeat="payment in payment" value="{{payment.method_id}}">{{payment.method_name}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0">
                        <option value="0"><?php echo Lang::get('slave.select_server') ?></option>
                        <?php foreach ($servers as $k => $v) { ?>
                            <option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
                        <?php } ?>      
                    </select>
                </div>
                <div class="form-group">
                    <select class="form-control" name="currency_code" ng-model="formData.currency_code" ng-init="formData.currency_code=0">
                        <option value="0"><?php echo Lang::get('platformapi.select_currency') ?></option>
                        <?php foreach ($currencies as  $k => $v) { ?>
                            <option value="<?php echo $v->currency_code?>"><?php echo $v->currency_name;?></option>
                        <?php } ?>      
                    </select>
                </div>
                <div class="form-group">
                    uid:
                    <input type="text" name="pay_user_id" value="" ng-model="formData.pay_user_id" class="form-control" required/>
                </div>
                <div class="form-group">
                    pay_amount:
                    <input type="text" name="pay_amount" value="" ng-model="formData.pay_amount" class="form-control" required/>
                </div>
                <div class="form-group">
                    basic yuanbao:
                    <input type="text" name="basic_yuanbao_amount" value="" ng-model="formData.basic_yuanbao_amount" class="form-control" required/>
                </div>
                <div class="form-group">
                    extra yuanbao:
                    <input type="text" name="extra_yuanbao_amount" value="" ng-model="formData.extra_yuanbao_amount" class="form-control" required/>
                </div>
                <div class="form-group">
                    huodong yuanbao:
                    <input type="text" name="basic_yuanbao_amount" value="" ng-model="formData.huodong_yuanbao_amount" class="form-control" required/>
                </div>
                <div class="form-group">
                    total yuanbao:
                    <input type="text" name="yuanbao_amount" value="" ng-model="formData.yuanbao_amount" class="form-control" required/>
                </div>
                <input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-default"/>
            </form>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="eb-content"> 
            <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
</div>