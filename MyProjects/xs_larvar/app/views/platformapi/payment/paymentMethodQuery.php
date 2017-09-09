<script>
function modifyController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.modify = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : '/platform-api/mobile_payment_method/query',
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $scope.items = data;
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
            <form action="/platform-api/mobile_payment_method/query" ng-submit="modify()" onsubmit="return false;">

                <div class="form-group">
                    <label><input type="radio" name="query_type" value='0' ng-model="formData.query_type" ng-init="formData.query_type='0'"/>按ID查询</label>
                    <label><input type="radio" name="query_type" value='1' ng-model="formData.query_type" />按method_name查询</label>
                    <label><input type="radio" name="query_type" value='2' ng-model="formData.query_type" />按pay_type查询</label>
                    <label><input type="radio" name="query_type" value='3' ng-model="formData.query_type" />按pay_lib查询</label
                </div>

                <div class="form-group">
                    <label for="query_data">请输入数据：</label>
                    <input type="text" class="form-control" id="query_data" ng-init="formData.query_data='<?php echo $data['query_data'];?>'" required ng-model="formData.query_data" name="query_data"/>
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

    <div class="col-xs-12">
        <table class="table table-striped">
            <thead>
                <tr class="info">
                    <td><b>payment_id</b></td>
                    <td><b>method_name</b></td>
                    <td><b>pay_type</b></td>
                    <td><b>pay_lib</b></td>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="t in items">
                    <td>{{t.payment_id}}</td>
                    <td>{{t.method_name}}</td>
                    <td>{{t.pay_type}}</td>
                    <td>{{t.pay_lib}}</td>
                </tr>
            </tbody>
        </table>  
    </div>
</div>