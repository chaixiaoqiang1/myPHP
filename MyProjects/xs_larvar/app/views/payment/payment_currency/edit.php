<script>
function updateCurrencyDataController($scope, $http, alertService) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.processFrom = function(url) {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'put',
            'url'    : url,
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            alertService.add('success', data.msg);
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
}
</script>

<div class="col-xs-12" ng-controller="updateCurrencyDataController">
    <div class="row" >
        <div class="eb-content">
            <form action="/payment-currency/<?php echo $edit_currency->id; ?>" method="put" role="form" ng-submit="processFrom('/payment-currency/<?php echo $edit_currency->id; ?>')" onsubmit="return false;">

             
                <div class="form-group">
                    <label for="platform_id"></label>
                    <select class="form-control" name="platform_id" ng-model="formData.platform_id" ng-init="formData.platform_id=<?php echo $platform_id ?>">
                        <option value="0">选择支付平台</option>
                        <?php foreach (Platform::all() as $k => $v) { ?>
                        <option value="<?php echo $v->platform_id?>"><?php echo $v->platform_name;?></option>
                        <?php } ?>      
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="id"><?php echo Lang::get('merchant.id') ?></label>
                    <input type="text" class="form-control" id="id" ng-init="formData.id='<?php echo $edit_currency->id?>'" required ng-model="formData.id" name="id"/>
                </div>
                <div class="form-group">
                    <label for="pay_type_id"><?php echo Lang::get('merchant.pay_type_id') ?></label>
                    <input type="text" class="form-control" id="pay_type_id" ng-init="formData.pay_type_id='<?php echo $edit_currency->pay_type_id?>'" required ng-model="formData.pay_type_id" name="pay_type_id"/>
                </div>
                <div class="form-group">
                    <label for="method_id"><?php echo Lang::get('merchant.method_id') ?></label>
                    <input type="text" class="form-control" id="method_id" ng-init="formData.method_id='<?php echo $edit_currency->method_id?>'" required ng-model="formData.method_id" name="method_id"/>
                </div>
                
                <div class="form-group">
                    <label for="currency_id"><?php echo Lang::get('merchant.currency_id') ?></label>
                    <input type="text" class="form-control" id="currency_id" ng-init="formData.currency_id='<?php echo $edit_currency->currency_id?>'" required ng-model="formData.currency_id" name="currency_id"/>
                </div>
                <div class="form-group">
                    <label for="currency_order"><?php echo Lang::get('merchant.currency_order') ?></label>
                    <input type="text" class="form-control" id="currency_order" ng-init="formData.currency_order='<?php echo $edit_currency->currency_order?>'" required ng-model="formData.currency_order" name="currency_order"/>
                </div>
               
                <input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>  
            </form>
        </div>
    </div>
    
    <div class="row margin-top-10">
        <div class="eb-content"> 
            <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
</div>