
<script type="text/javascript">
function createTypeController($scope, $http, alertService) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : '/pay-type',
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            alertService.add('success', data.msg);
            $scope.formData.company = '';
            $scope.formData.pay_type_name = '';
            $scope.formData.pay_type_id = '';
            $scope.formData.platform_id = '';
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
}
</script>
<div class="col-xs-12" ng-controller="createTypeController">
    <div class="row">
        <div class="eb-content">
            <form action="/pay-type/store" method="post" role="form" ng-submit="processFrom('/pay-type/store')" onsubmit="return false;">

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
                    <label for="pay_type_name"></label>
                    <input type="text" class="form-control" id="pay_type_name" placeholder="<?php echo Lang::get('type.pay_type_name') ?>" required ng-model="formData.pay_type_name" name="pay_type_name"/>
                </div>

                <div class="form-group">
                    <label for="pay_type_id"></label>
                    <input type="text" class="form-control" id="pay_type_id" placeholder="<?php echo Lang::get('type.pay_type_id') ?>" required ng-model="formData.pay_type_id" name="pay_type_id"/>
                </div>
                
                <div class="form-group">
                    <label for="company"></label>
                    <input type="text" class="form-control" id="company" placeholder="<?php echo Lang::get('type.company') ?>" required ng-model="formData.company" name="company"/>
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