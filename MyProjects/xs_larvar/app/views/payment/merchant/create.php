
<script type="text/javascript">
function createMerchantDataController($scope, $http, alertService) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : '/merchant-data',
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
<div class="col-xs-12" ng-controller="createMerchantDataController">
    <div class="row">
        <div class="eb-content">
            <form action="/merchant-data/store" method="post" role="form" ng-submit="processFrom('/merchatn-data/store')" onsubmit="return false;">

                <div class="form-group">
                    <label for="platform_id"></label>
                    <select class="form-control" name="platform_id" ng-model="formData.platform_id" ng-init="formData.platform_id=<?php echo $platform_id?>">
                        <option value="0">选择平台</option>
                        <?php foreach (Platform::all() as $k => $v) {?>
                        <option value="<?php echo $v->platform_id?>"><?php echo $v->platform_name;?></option>
                        <?php } ?>      
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="merchant_name"><?php echo Lang::get('merchant.merchant_name') ?></label>
                    <input type="text" class="form-control" id="merchant_name"  required ng-model="formData.merchant_name" name="merchant_name"/>
                </div>
                <div class="form-group">
                    <label for="merchant_key"><?php echo Lang::get('merchant.merchant_key') ?></label>
                    <input type="text" class="form-control" id="merchant_key"   ng-model="formData.merchant_key" name="merchant_key"/>
                </div>
                <div class="form-group">
                    <label for="merchant_key2"><?php echo Lang::get('merchant.merchant_key2') ?></label>
                    <input type="text" class="form-control" id="merchant_key2"  ng-model="formData.merchant_key2" name="merchant_key2"/>
                </div>
                <div class="form-group">
                    <label for="merchant_key3"><?php echo Lang::get('merchant.merchant_key3') ?></label>
                    <input type="text" class="form-control" id="merchant_key3"  ng-model="formData.merchant_key3" name="merchant_key3"/>
                </div>
                <div class="form-group">
                    <label for="merchant_key4"><?php echo Lang::get('merchant.merchant_key4') ?></label>
                    <input type="text" class="form-control" id="merchant_key4"  ng-model="formData.merchant_key4" name="merchant_key4"/>
                </div>
                <div class="form-group">
                    <label for="pay_type_id"><?php echo Lang::get('merchant.pay_type_id') ?></label>
                    <input type="text" class="form-control" id="pay_type_id"  required ng-model="formData.pay_type_id" name="pay_type_id"/>
                </div>
                <div class="form-group">
                    <label for="method_id"><?php echo Lang::get('merchant.method_id') ?></label>
                    <input type="text" class="form-control" id="method_id"  required ng-model="formData.method_id" name="method_id"/>
                </div>
                <div class="form-group">
                    <label for="domain_name"><?php echo Lang::get('merchant.domain_name') ?></label>
                    <input type="text" class="form-control" id="domain_name"   ng-model="formData.domain_name" name="domain_name"/>
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