<script> 
function modifyDataController($scope, $modalInstance, order, $http, alertService) {
    $scope.region_id = '<?php echo Platform::find(Session::get('platform_id'))->region_id?>';
    $scope.order = order;
    $scope.orderData = {};
    // alert(order[1]);
    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    }
    $scope.modifyForm= function(url) {
        $http({
            'method' : 'post',
            'url' : url,
            'data' : $.param($scope.orderData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $modalInstance.close();
            window.location.reload();
        }).error(function(data) {
            alert('error: ' + data.error + '\n');
        });
    }
}
function getOfficalWebController($scope, $http, alertService,$modal, $filter) {
    $scope.alerts = [];
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.formData = {};
    $scope.total = {};
    $scope.processFrom = function(check) {
        alertService.alerts = $scope.alerts;
        $scope.formData.check = check;
        $http({
            'method': 'post',
            'url': '/slave-api/mobilegame/officalwebmodify',
            'data': $.param($scope.formData),
            'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            alertService.add('success', data.msg);
            setTimeout('window.location.reload()',500);
        }).error(function(data) {
            alertService.add('danger', data.msg);
        });
    };

    $scope.modify = function(id,data) {
        var modalInstance = $modal.open({
            templateUrl: 'modify_data.html',
            controller: modifyDataController,
            resolve: {
                order : function () {
                    var order=[id,data]
                    return order;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() {
            // $scope.processFrom();   
        });
    }
} 
</script>
<div class="col-xs-12" ng-controller="getOfficalWebController">
    <form action="/slave-api/mobilegame/officalwebmodify" method="post" role="form"
                 onsubmit="return false;">
        <div class="row">
            <div class="eb-content">
                
                    <textarea type="text" class="form-control" id="data" placeholder="新增一条数据" ng-cols="30"  ng-model="formData.data" name="campaign" autofocus="autofocus" required/></textarea>
                    
                    <input type="submit" class="btn btn-default" ng-click="processFrom(0)"
                        value="<?php echo Lang::get('basic.btn_submit') ?>" />
                
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
                    <tr class="info" id="server">
                        <td>game_id</td>
                        <td>内容</td>
                        <td>最后操作时间</td>
                        <td>操作1</td>
                        <td>操作2</td>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $value) {?>
                        <tr>
                            <td><?php echo $value->game_id ?></td>
                            <td><?php echo $value->data ?></td>
                            <td><?php echo $value->last_modified_time ?></td>
                            <td><input type="button" value="修改" ng-click="modify('<?php echo $value->id?>','<?php echo $value->data ?>')"></td>
                            <td><input type="button" value="删除" ng-click="processFrom(<?php echo $value->id ?>)"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div> 
    </form>
</div>

<script type="text/ng-template" id="modify_data.html">
        <div class="modal-header">
        </div>
        <form action="/slave-api/mobilegame/modifyofficalwebdata" method="post" role="form" ng-submit="modifyForm('/slave-api/mobilegame/modifyofficalwebdata')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label>内容修改:</label>
                <textarea type="text" class="form-control" ng-cols="30"  ng-model="orderData.data" autofocus="autofocus" ng-init="orderData.data = order[1]" required/></textarea>
            </div>
        </div>
        <input type="hidden" ng-model="orderData.id" ng-init="orderData.id = order[0]" name="id"/>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary">确认修改</button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>