<script>
function huodongUpdateMethodController($scope, $http, alertService,$filter) {
    $scope.alerts = [];
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.formData = {};
    $scope.processFrom = function(url) {
        $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
        $scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
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

<div class="col-xs-12" ng-controller="huodongUpdateMethodController">
    <div class="row" >
        <div class="eb-content">
            <form action="/payment/<?php echo $method->pay_id; ?>" method="post" role="form" ng-submit="processFrom('/pay-method/huodong-update')" onsubmit="return false;">
                <div class="form-group">
                    <h3><?php echo $method->method_name; ?></h3>
                </div>
                <input type="hidden" class="form-control" id="platform_method_id" ng-init="formData.platform_method_id='<?php echo $method->platform_method_id?>'" required ng-model="formData.platform_method_id" name="platform_method_id"/>
                <input type="hidden" class="form-control" id="pay_id" ng-init="formData.pay_id='<?php echo $method->pay_id?>'" required ng-model="formData.pay_id" name="pay_id"/>

                <div class="form-group">
                    <label for="huodong_rate"><?php echo Lang::get('payment.huodong_rate') ?></label>
                    <input type="text" class="form-control" id="huodong_rate" ng-init="formData.huodong_rate='<?php
                    $id = Session::get('platform_id');
                    if($id == 1 || $id == 50 || $id == 38)
                        echo 0.1;
                    else echo 1;
                    ?>'" required ng-model="formData.huodong_rate" name="huodong_rate"/>
                </div>

                <div class="form-group" style="height:30px;">
                    <div class="col-md-6" style="padding: 0">
                        <div class="input-group">
                            <quick-datepicker ng-model="start_time" init-value="<?php echo date('Y-m-d h:i:s',$method->start_time)?>"></quick-datepicker> 
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding: 0">
                        <div class="input-group">
                            <quick-datepicker ng-model="end_time" init-value="<?php echo date('Y-m-d h:i:s',$method->end_time)?>"></quick-datepicker> 
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="height: 30px;">
                    <br/>
                    <span style = "color:red; font-size:16px;">
                        <?php   $id = Session::get('platform_id');
                        if($id == 1 || $id == 50 || $id == 38)
                            echo Lang::get('serverapi.rate_introduce_new_platform');
                        else echo Lang::get('serverapi.rate_introduce');?>
                    </span>
                </div>
                <br>
                <div class="form-group" style="height:40px;">

                <input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>  
            </form>
        </div>
    </div>
</div>
<div class="row margin-top-10">
    <div class="eb-content">
        <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
    </div>
</div>