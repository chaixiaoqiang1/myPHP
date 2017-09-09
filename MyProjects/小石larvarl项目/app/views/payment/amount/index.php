<script type="text/javascript">
function deleteAmountController($scope, $http, alertService,$modal) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.amounts = {};
    $scope.processFrom = function(url) {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : url,
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $scope.amounts = data;
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
    $scope.update = function(url){
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url' : url,
            'data' : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data){
            if(data.res){
                alertService.add('success',data.msg);
            }else{
                alertService.add('danger','error');
            }
            $scope.amounts = {};
        }).error(function(data){
            alertService.add('danger',data);
        });
    };
    $scope.add = function(){
        var modalInstance = $modal.open({
            templateUrl:'createModalContent.html',
            controller:modalAddController
        });
        modalInstance.result.then(function(){
        });
    };
    $scope.getType = function() {
    $http({
        'method' : 'post',
        'url'    : '/pay-amount/get-type',
        'data'   : $.param($scope.formData),
        'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
    }).success(function(data) {
        $scope.type = data;
    }).error(function(data) {
        alertService.add('danger', data.error, 2000);
    });
    };  
    $scope.getPayment = function() {
        $http({
            'method' : 'post',
            'url'    : '/pay-amount/get-payment',
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $scope.payment = data;
        }).error(function(data) {
            alertService.add('danger', data.error, 2000);
        });
    };
}
function modalAddController($scope, $http, alertService,$modalInstance){
    $scope.alerts = [];
    $scope.amountsData = {};
    $scope.cancle = function(){
        $modalInstance.dismiss('cancle');
    };

    $scope.add = function(url) {
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : url,

            'data'   : $.param($scope.amountsData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $modalInstance.close();
        }).error(function(data) {
            alertService.add('danger', 'error');
            $modalInstance.close();
        });
    };
}
</script>
<div class="col-xs-12" ng-controller="deleteAmountController">
    <div class="row" >
        <div class="eb-content">
            <form action="/pay-amount/check" method="get"  onsubmit="return false;" ng-submit="processFrom('/pay-amount/check')">
<!--                 <div class="form-group">
                    <label for="pay_type_id">pay_type_id</label>
                    <input type="text" id="pay_type_id" name="pay_type_id" required ng-model="formData.pay_type_id"/>
                </div>
                <div class="form-group">
                    <label for="method_id">method_id</label>
                    <input type="text" id="method_id" name="method_id" ng-model="formData.method_id"/>
                </div> -->
                <div class="form-group">
                    <select name="platform_id" ng-model="formData.platform_id" ng-init="formData.platform_id=0" id="platform_id" class="form-control" ng-change="getType()">
                        <option value="0">选择支付平台</option>
                        <?php foreach (Platform::all() as $k => $v) { ?>
                        <option value="<?php echo $v->platform_id?>"><?php echo $v->platform_name;?></option>
                        <?php } ?>   
                    </select>
                </div>
                <div class="form-group">
                    <select name="type" ng-model="formData.type" ng-init="formData.type=0" id="type" class="form-control" ng-change="getPayment()">
                        <option value="0">选择支付类型</option>
                        <option ng-repeat="type in type" value="{{type.pay_type_id}}">{{type.pay_type_name}}</option>
                    </select>
                
                </div>
                <div class="form-group" >
                    <select name="payment" id="payment" ng-model="formData.payment"   class="form-control" >
                        <option value="">选择支付方法</option>
                        <option ng-repeat="payment in payment" value="{{payment.method_id}}">{{payment.method_name}}</option>
                    </select>
                </div>
<!--                 <div class="form-group">
                    <label for="domain_name">domain_name</label>
                    <input type="text" id="domain_name" name="domain_name" required ng-model="formData.domain_name"/>
                </div> -->
                <input type="button" value="<?php echo Lang::get('basic.btn_search')?>" ng-click="processFrom('/pay-amount/check')"/>
                <input type="button" value="<?php echo Lang::get('basic.btn_discard')?>" ng-click="update('/pay-amount/batch-update')"/>
                <!-- <input type="button" value="<?php echo Lang::get('basic.btn_add')?>" ng-click="add()"/> -->
            </form>
        </div>
    </div>
    <table class="table table-striped">
            <thead>
                 <tr>
                     <td><?php echo Lang::get('amount.amount_id') ?></td>
                     <td><?php echo Lang::get('amount.currency_id') ?></td>
                     <td><?php echo Lang::get('amount.pay_amount') ?></td>
                     <td><?php echo Lang::get('amount.yuanbao_amount') ?></td>
                     <td><?php echo Lang::get('amount.yuanbao_extra') ?></td>
                     <td><?php echo Lang::get('amount.yuanbao_hudong') ?></td>    
                     <td><?php echo Lang::get('amount.pay_type_id') ?></td>
                     <td><?php echo Lang::get('amount.method_id') ?></td>
                     <td><?php echo Lang::get('amount.domain_name') ?></td>
                     <td><?php echo Lang::get('amount.goods_type') ?></td>             
                 </tr>
             </thead>
            <tbody>
                <tr ng-repeat="t in amounts">
                    <td>{{t.pay_amount_id}}</td>
                    <td>{{t.currency_id}}</td>
                    <td>{{t.pay_amount}}</td>
                    <td>{{t.yuanbao_amount}}</td>
                    <td>{{t.yuanbao_extra}}</td>
                    <td>{{t.yuanbao_huodong}}</td>
                    <td>{{t.pay_type_id}}</td>
                    <td>{{t.method_id}}</td>
                    <td>{{t.domain_name}}</td>
                    <td>{{t.goods_type}}</td>
                </tr>
            </tbody>
        </table>
        <div class="row margin-top-10">
        <div class="eb-content"> 
            <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
</div>

<script type="text/ng-template" id="createModalContent.html">
    <div class="modal-header">
        <h3>添加pay-amount</h3>
    </div>
    <form action="/pay-amount" method="post" role="form" ng-submit="add('/pay-amount')" onsubmit="return false;">
    <div class="modal-body">
        <textarea class="form-control" id="amount" placeholder="<?php echo Lang::get('amount.pay_amount_format')?>"  ng-model="amountsData.amount" name="amount" rows="8"></textarea>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" ng-submit="add('/pay-amount')"><?php echo Lang::get('basic.btn_add')?></button>
        <a class="btn btn-warning" ng-click="cancle()">Cancle</a>
    </div>
</script>
