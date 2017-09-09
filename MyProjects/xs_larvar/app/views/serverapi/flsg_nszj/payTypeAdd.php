<script>
function modifyController($scope, $http, alertService) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.processFrom = function(url) {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
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

<div class="col-xs-12" ng-controller="modifyController">
    <div class="row" >
        <div class="eb-content">
            <form name="myForm" method="post" role="form" ng-submit="processFrom('/mobile_paytype/update')" onsubmit="return false;">

                <div class="form-group">
                    <label for="payment_id">payment_id</label>
                    <input type="text" class="form-control" id="payment_id" ng-init="formData.payment_id='<?php echo $type['payment_id']?>'" required ng-model="formData.payment_id" name="payment_id"/>
                </div>

                <div class="form-group">
                    <label for="method_name">method_name</label>
                    <input type="text" class="form-control" id="method_name" ng-init="formData.method_name='<?php echo $type['method_name']?>'" required ng-model="formData.method_name" name="method_name"/>
                </div>
                
                <div class="form-group">
                    <label for="domain_name">domain_name</label>
                    <input type="text" class="form-control" id="domain_name" ng-init="formData.domain_name='<?php echo $type['domain_name']?>'" required ng-model="formData.domain_name" name="domain_name"/>
                </div>

                <div class="form-group">
                    <label for="pay_lib">pay_lib</label>
                    <input type="text" class="form-control" id="pay_lib" ng-init="formData.pay_lib='<?php echo $type['pay_lib']?>'" required ng-model="formData.pay_lib" name="pay_lib"/>
                </div>

                <div class="form-group">
                    <label for="pay_type_id">pay_type_id</label>
                    <input type="text" class="form-control" id="pay_type_id" ng-init="formData.pay_type_id='<?php echo $type['pay_type_id']?>'" required ng-model="formData.pay_type_id" name="pay_type_id"/>
                </div>

                <div class="form-group">
                    <label for="method_id">method_id</label>
                    <input type="text" class="form-control" id="method_id" ng-init="formData.method_id='<?php echo $type['method_id']?>'" required ng-model="formData.method_id" name="method_id"/>
                </div>

                <div class="form-group">
                    <label for="currency">currency</label>
                    <input type="text" class="form-control" id="currency" ng-init="formData.currency='<?php echo $type['currency']?>'" required ng-model="formData.currency" name="currency"/>
                </div>

                <div class="form-group">
                    <label for="use_type">use_type</label>
                    <input type="text" class="form-control" id="use_type" ng-init="formData.use_type='<?php echo $type['use_type']?>'" required ng-model="formData.use_type" name="use_type"/>
                </div>

                 <div class="form-group">
                    <label for="payment_type">payment_type</label>
                    <input type="text" class="form-control" id="payment_type" ng-init="formData.payment_type='<?php echo $type['payment_type']?>'" required ng-model="formData.payment_type" name="payment_type"/>
                </div>

                 <div class="form-group">
                    <label for="method_order">method_order</label>
                    <input type="text" class="form-control" id="method_order" ng-init="formData.method_order='<?php echo $type['method_order']?>'" required ng-model="formData.method_order" name="method_order"/>
                </div>

                 <div class="form-group">
                    <label for="img_source">img_source</label>
                    <input type="text" class="form-control" id="img_source" ng-init="formData.img_source='<?php echo $type['img_source']?>'" required ng-model="formData.img_source" name="img_source"/>
                </div>

                 <div class="form-group">
                    <label for="extra">extra</label>
                    <input type="text" class="form-control" id="extra" ng-init="formData.extra='<?php echo $type['extra']?>'" ng-model="formData.extra"  name="extra"/>
                </div>

                 <div class="form-group">
                    <label for="tips">tips</label>
                    <input type="text" class="form-control" id="tips" ng-init="formData.tips='<?php echo $type['tips']?>'" ng-model="formData.tips"  name="tips"/>
                </div>

                <div class="form-group">
                    <label for="special_type">special_type</label>
                    <input type="text" class="form-control" id="special_type" ng-init="formData.special_type='<?php echo $type['special_type']?>'" ng-model="formData.special_type"  name="special_type"/>
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