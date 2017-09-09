<script>
function modifyController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.modify = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : '/platform-api/mobile_payment_method/modify',
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
            <form action="/platform-api/mobile_payment_method/modify" ng-submit="modify()" onsubmit="return false;">

                <?php 
                $url = explode('/', Request::url());
                $url = $url[count($url)-1];
                
                if('modify' == $url){ ?>
                    <div class="form-group">
                        <label for="payment_id">payment_id</label>
                        <input type="text" class="form-control" id="payment_id" ng-init="formData.payment_id='<?php echo $data['payment_id'] ?>'" required  ng-model="formData.payment_id" name="payment_id"/>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="method_name">method_name</label>
                    <input type="text" class="form-control" id="method_name" ng-init="formData.method_name='<?php echo $data['method_name'];?>'" required ng-model="formData.method_name" name="method_name"/>
                </div>

                <div class="form-group">
                    <label for="pay_type">pay_type</label>
                    <input type="text" class="form-control" id="pay_type" ng-init="formData.pay_type='<?php echo $data['pay_type'];?>'" required ng-model="formData.pay_type" name="pay_type"/>
                </div>

                <div class="form-group">
                    <label for="pay_lib">pay_lib</label>
                    <input type="text" class="form-control" id="pay_lib" ng-init="formData.pay_lib='<?php echo $data['pay_lib'];?>'"  ng-model="formData.pay_lib" name="pay_lib"/>
                </div>           
                
                <input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>  
            </form>
        </div>
    </div>
    
    <div class="row margin-top-10">
        <div class="eb-content">
            <alert ng-repeat="alert in alerts" type="alert.type"
                close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
</div>