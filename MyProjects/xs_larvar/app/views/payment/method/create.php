<script type="text/javascript">
function createMethodController($scope, $http, alertService) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : '/payment',
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
        	$scope.formData.method_name = '';
            $scope.formData.method_description = '';
            $scope.formData.is_recommend = '';
            $scope.formData.method_order = '';
            $scope.formData.post_url = '';
            $scope.formData.html_name = '';
            $scope.formData.is_use = '';
            $scope.formData.currency_id = '';
            $scope.formData.zone = '';
            $scope.formData.platform_id = '';
            $scope.formData.pay_type_id = '';
            $scope.formData.method_id = '';
            alertService.add('success', data.msg);
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
}
</script>

<div class="col-xs-12" ng-controller="createMethodController">
    <div class="row">
        <div class="eb-content">
            <form action="/payment/store" method="post" role="form" ng-submit="processFrom('/payment')" onsubmit="return false;">

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
                    <label for="method_name"><?php echo Lang::get('payment.method_name') ?></label>
                    <input type="text" class="form-control" id="method_name"  required ng-model="formData.method_name" name="method_name"/>
                </div>

                <div class="form-group">
                    <label for="method_description"><?php echo Lang::get('payment.method_description') ?></label>
                    <input type="text" class="form-control" id="method_description"  required ng-model="formData.method_description" name="method_description"/>
                </div>
                
                <div class="form-group">
                    <label for="is_recommend"><?php echo Lang::get('payment.is_recommend') ?></label>
                    <input type="text" class="form-control" id="is_recommend"  required ng-model="formData.is_recommend" name="is_recommend"/>
                </div>

               <div class="form-group">
                    <label for="method_order"><?php echo Lang::get('payment.method_order') ?></label>
                    <input type="text" class="form-control" id="method_order"  required ng-model="formData.method_order" name="method_order"/>
                </div>
                
                <div class="form-group">
                    <label for="post_url"><?php echo Lang::get('payment.post_url') ?></label>
                    <input type="text" class="form-control" id="post_url"   ng-model="formData.post_url" name="post_url"/>
                </div>
                
                <div class="form-group">
                    <label for="html_name"><?php echo Lang::get('payment.html_name') ?></label>
                    <input type="text" class="form-control" id="html_name"   ng-model="formData.html_name" name="html_name"/>
                </div>
                
                <div class="form-group">
                    <label for="is_use"><?php echo Lang::get('payment.is_use') ?></label>
                    <input type="text" class="form-control" id="is_use"  required ng-model="formData.is_use" name="is_use"/>
                </div>
                
                <div class="form-group">
                    <label for="currency_id"><?php echo Lang::get('payment.currency_id') ?></label>
                    <input type="text" class="form-control" id="currency_id"  required ng-model="formData.currency_id" name="currency_id"/>
                </div>
                
                <div class="form-group">
                    <label for="domain_name"><?php echo Lang::get('payment.domain_name') ?></label>
                    <input type="text" class="form-control" id="domain_name"   ng-model="formData.domain_name" name="domain_name"/>
                </div>

                <div class="form-group">
                    <label for="zone"><?php echo Lang::get('payment.zone') ?></label>
                    <input type="text" class="form-control" id="zone"   ng-model="formData.zone" name="zone"/>
                </div>
                
                <div class="form-group">
                    <label for="pay_type_id"><?php echo Lang::get('payment.pay_type_id') ?></label>
                    <input type="text" class="form-control" id="pay_type_id"  required ng-model="formData.pay_type_id" name="pay_type_id"/>
                </div>
                
                <div class="form-group">
                    <label for="method_id"><?php echo Lang::get('payment.method_id') ?></label>
                    <input type="text" class="form-control" id="method_id"  required ng-model="formData.method_id" name="method_id"/>
                </div>

                <div class="form-group">
                    <label for="is_selected"><?php echo Lang::get('payment.is_selected') ?></label>
                    <input type="text" class="form-control" id="is_selected"  required ng-model="formData.is_selected" name="is_selected"/>
                </div>

                <div class="form-group">
                    <label for="class_name"><?php echo Lang::get('payment.class_name') ?></label>
                    <input type="text" class="form-control" id="class_name"   ng-model="formData.class_name" name="class_name"/>
                </div>

                <div class="form-group">
                    <label for="use_for_month_card"><?php echo Lang::get('payment.use_for_month_card') ?></label>
                    <input type="text" class="form-control" id="use_for_month_card"  required ng-model="formData.use_for_month_card" name="use_for_month_card"/>
                </div>
                
                <div class="well">
					<div class="form-group">
						<label for="is_update_platform"></label>
					<?php echo Lang::get('payment.is_update_platform')?>
					<select name="is_update_platform"
							ng-model="formData.is_update_platform"
							ng-init="formData.is_update_platform=1" class="form-control">
							<option value="1"><?php echo Lang::get('payment.yes')?></option>
							<option value="0"><?php echo Lang::get('payment.no')?></option>
							?>
						</select>
					</div>
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