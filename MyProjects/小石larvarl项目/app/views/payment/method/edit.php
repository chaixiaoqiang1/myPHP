<script>
function updateMethodController($scope, $http, alertService) {
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

<div class="col-xs-12" ng-controller="updateMethodController">
    <div class="row" >
        <div class="eb-content">
            <form action="/payment/<?php echo $method->pay_id; ?>" method="put" role="form" ng-submit="processFrom('/payment/<?php echo $method->pay_id; ?>')" onsubmit="return false;">

             
                <div class="form-group">
                    <label for="method_id"></label>
                    <select class="form-control" name="platform_id" ng-model="formData.platform_id" ng-init="formData.platform_id=<?php echo $method->platform_id ?>">
                        <option value="0">选择支付平台</option>
                        <?php foreach (Platform::all() as $k => $v) { ?>
                        <option value="<?php echo $v->platform_id?>"><?php echo $v->platform_name;?></option>
                        <?php } ?>      
                    </select>
                </div>
                
                 <div class="form-group">
                    <label for="platform_method_id"><?php echo Lang::get('payment.platform_method_id') ?></label>
                    <input type="text" class="form-control" id="platform_method_id" ng-init="formData.platform_method_id='<?php echo $method->platform_method_id?>'" required ng-model="formData.platform_method_id" name="platform_method_id"/>
                </div>

                <div class="form-group">
                    <label for="method_name"><?php echo Lang::get('payment.method_name') ?></label>
                    <input type="text" class="form-control" id="method_name" ng-init="formData.method_name='<?php echo $method->method_name?>'" required ng-model="formData.method_name" name="method_name"/>
                </div>

                <div class="form-group">
                    <label for="is_selected"><?php echo Lang::get('payment.is_selected') ?></label>
                    <input type="text" class="form-control" id="is_selected" ng-init="formData.is_selected='<?php echo $method->is_selected?>'" required ng-model="formData.is_selected" name="is_selected"/>
                </div>

                <div class="form-group">
                    <label for="method_description"><?php echo Lang::get('payment.method_description') ?></label>
                    <input type="text" class="form-control" id="method_description" ng-init="formData.method_description='<?php echo $method->method_description?>'" required ng-model="formData.method_description" name="method_description"/>
                </div>
                
                <div class="form-group">
                    <label for="is_recommend"><?php echo Lang::get('payment.is_recommend') ?></label>
                    <input type="text" class="form-control" id="is_recommend" ng-init="formData.is_recommend='<?php echo $method->is_recommend?>'" required ng-model="formData.is_recommend" name="is_recommend"/>
                </div>

               <div class="form-group">
                    <label for="method_order"><?php echo Lang::get('payment.method_order') ?></label>
                    <input type="text" class="form-control" id="method_order" ng-init="formData.method_order='<?php echo $method->method_order?>'" required ng-model="formData.method_order" name="method_order"/>
                </div>
                
                <div class="form-group">
                    <label for="post_url"><?php echo Lang::get('payment.post_url') ?></label>
                    <input type="text" class="form-control" id="post_url" ng-init="formData.post_url='<?php echo $method->post_url?>'"  ng-model="formData.post_url" name="post_url"/>
                </div>
                
                <div class="form-group">
                    <label for="html_name"><?php echo Lang::get('payment.html_name') ?></label>
                    <input type="text" class="form-control" id="html_name" ng-init="formData.html_name='<?php echo $method->html_name?>'"  ng-model="formData.html_name" name="html_name"/>
                </div>
                
                <div class="form-group">
                    <label for="class_name"><?php echo Lang::get('payment.class_name') ?></label>
                    <input type="text" class="form-control" id="class_name" ng-init="formData.class_name='<?php echo $method->class_name?>'"  ng-model="formData.class_name" name="class_name"/>
                </div>

                <div class="form-group">
                    <label for="is_use"><?php echo Lang::get('payment.is_use') ?></label>
                    <input type="text" class="form-control" id="is_use" ng-init="formData.is_use='<?php echo $method->is_use?>'" required ng-model="formData.is_use" name="is_use"/>
                </div>
                
                <div class="form-group">
                    <label for="currency_id"><?php echo Lang::get('payment.currency_id') ?></label>
                    <input type="text" class="form-control" id="currency_id" ng-init="formData.currency_id='<?php echo $method->currency_id?>'" required ng-model="formData.currency_id" name="currency_id"/>
                </div>
                
                <div class="form-group">
                    <label for="zone"><?php echo Lang::get('payment.zone') ?></label>
                    <input type="text" class="form-control" id="zone" ng-init="formData.zone='<?php echo $method->zone?>'"  ng-model="formData.zone" name="zone"/>
                </div>
                
                <div class="form-group">
                    <label for="pay_type_id"><?php echo Lang::get('payment.pay_type_id') ?></label>
                    <input type="text" class="form-control" id="pay_type_id" ng-init="formData.pay_type_id='<?php echo $method->pay_type_id?>'" required ng-model="formData.pay_type_id" name="pay_type_id"/>
                </div>
                
                <div class="form-group">
                    <label for="method_id"><?php echo Lang::get('payment.method_id') ?></label>
                    <input type="text" class="form-control" id="method_id" ng-init="formData.method_id='<?php echo $method->method_id?>'" required ng-model="formData.method_id" name="method_id"/>
                </div>

                <div class="form-group">
                    <label for="domain_name"><?php echo Lang::get('payment.domain_name') ?></label>
                    <input type="text" class="form-control" id="domain_name" ng-init="formData.domain_name='<?php echo $method->domain_name?>'"  ng-model="formData.domain_name" name="domain_name"/>
                </div>

                <div class="form-group">
                    <label for="use_for_month_card"><?php echo Lang::get('payment.use_for_month_card') ?></label>
                    <input type="text" class="form-control" id="use_for_month_card" ng-init="formData.use_for_month_card='<?php echo $method->use_for_month_card?>'" required ng-model="formData.use_for_month_card" name="use_for_month_card"/>
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