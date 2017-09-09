<script>
function updateTypeController($scope, $http, alertService) {
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

<div class="col-xs-12" ng-controller="updateTypeController">
    <div class="row" >
        <div class="eb-content">
            <form action="/pay-type/<?php echo $type->type_id; ?>" method="put" role="form" ng-submit="processFrom('/pay-type/<?php echo $type->type_id; ?>')" onsubmit="return false;">

             
                <div class="form-group">
                    <label for="platform_id"></label>
                    <select class="form-control" name="platform_id" ng-model="formData.platform_id" ng-init="formData.platform_id=<?php echo $type->platform_id ?>">
                        <option value="0">选择支付平台</option>
                        <?php foreach (Platform::all() as $k => $v) { ?>
                        <option value="<?php echo $v->platform_id?>"><?php echo $v->platform_name;?></option>
                        <?php } ?>      
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="company"><?php echo Lang::get('type.company') ?></label>
                    <input type="text" class="form-control" id="company" ng-init="formData.company='<?php echo $type->company?>'" required ng-model="formData.company" name="company"/>
                </div>

                <div class="form-group">
                    <label for="pay_type_name"><?php echo Lang::get('type.pay_type_name') ?></label>
                    <input type="text" class="form-control" id="pay_type_name" ng-init="formData.pay_type_name='<?php echo $type->pay_type_name?>'" required ng-model="formData.pay_type_name" name="pay_type_name"/>
                </div>
                
                <div class="form-group">
                    <label for="pay_type_id"><?php echo Lang::get('type.pay_type_id') ?></label>
                    <input type="text" class="form-control" id="pay_type_id" ng-init="formData.pay_type_id='<?php echo $type->pay_type_id?>'" required ng-model="formData.pay_type_id" name="pay_type_id"/>
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