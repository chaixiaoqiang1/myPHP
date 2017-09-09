<script>
function HelperSingleFunctionController($scope, $http, alertService, $filter, $modal) {
    $scope.alerts = [];

    $scope.modify = function(id, name, is_open) {
        var modalInstance = $modal.open({
            templateUrl: 'modify.html',
            controller: HelperModifyController,
            resolve: {
                id : function () {
                    return id;
                },
                name : function () {
                    return name;
                },
                is_open : function () {
                    return is_open;
                },
                picture_name : function () {
                    return '';
                },
                picture_version : function () {
                    return '';
                },
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() {
            alert('Modify success'); 
            location.reload(true);  
        });
    }

    $scope.add = function(){
        var modalInstance = $modal.open({
            templateUrl: 'modify.html',
            controller: HelperModifyController,
            resolve: {
                id : function () {
                    return 0;
                },
                name : function () {
                    return '';
                },
                is_open : function () {
                    return 0;
                },
                picture_name : function () {
                    return '';
                },
                picture_version : function () {
                    return '';
                },
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() {
            alert('Add success'); 
            location.reload(true);  
        });        
    }
}

function HelperModifyController($scope, $modalInstance, id, name, picture_name, picture_version, is_open, $http, alertService) {
    $scope.modifyinit = {};
    $scope.modifyinit.id = id;
    $scope.modifyinit.name = name;
    $scope.modifyinit.picture_name = picture_name;
    $scope.modifyinit.picture_version = picture_version;
    $scope.modifyinit.is_open = is_open;
    $scope.ModifyData = {};

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    }
    $scope.ModifyForm= function(url) {
        $scope.ModifyData.type = 4;
        $scope.ModifyData.function_id = <?php echo $id; ?>;
        $http({
            'method' : 'post',
            'url' : url,
            'data' : $.param($scope.ModifyData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $modalInstance.close();
        }).error(function(data) {
            alert('error: ' + data.error_description + '\n');
        });
    }
}

</script>
<div class="col-xs-12" ng-controller="HelperSingleFunctionController">
    <p style="color:blue;font-size:15px">创建成功或需要修改图片内容的话请点击<a href="<?php echo $link; ?>" target="upload_img"><?php echo $link; ?></a>跳转。</p>
    <table class="table table-striped">
        <thead>
            <tr class="info">
                <td><b><?php echo Lang::get('platformapi.id'); ?></b></td>
                <td><b><?php echo Lang::get('platformapi.name'); ?></b></td>
                <td><b><?php echo Lang::get('platformapi.is_open'); ?></b></td>
                <td><b><?php echo Lang::get('platformapi.picture_name'); ?></b></td>
                <td><b><?php echo Lang::get('platformapi.picture_version'); ?></b></td>
                <td><b><button class="btn btn-warning" ng-click="add()"><?php echo Lang::get('platformapi.add_one'); ?></b></td>
            </tr>
        </thead>
        <tbody> 
            <?php foreach ($view_data as $value) { ?>
                <tr>
                    <td><?php echo date('Y-m-d H:i:s',$value->id); ?></td>
                    <td><?php echo $value->name; ?></td>
                    <td><?php echo $value->is_open; ?></td>
                    <td><?php echo $value->picture_name; ?></td>
                    <td><?php echo $value->picture_version; ?></td>
                    <td><button class="btn btn-primary" ng-click="modify('<?php echo $value->id;?>', '<?php echo $value->name;?>', <?php echo $value->is_open;?>)"><?php echo Lang::get('platformapi.modify'); ?></button></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <div class="row margin-top-10">
        <div class="eb-content">
            <alert ng-repeat="alert in alerts" type="alert.type"
                close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
</div>

<script type="text/ng-template" id="modify.html">
        <div class="modal-header">
        </div>
        <form action="/project/release" method="post" role="form" ng-submit="ModifyForm('/platform-api/mobilegame/helper/single_function')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group" ng-if="modifyinit.id != 0">
                <label><?php echo Lang::get('platformapi.id') ?>:</label>    
                <input type="text" class="form-control" readonly required ng-model="ModifyData.id" ng-init="ModifyData.id=modifyinit.id"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.name') ?>:</label>
                <input type="text" class="form-control" required ng-model="ModifyData.name"  ng-init="ModifyData.name=modifyinit.name"/>
            </div>
            <div class="form-group"  ng-if="modifyinit.id != 0">
                <label><?php echo Lang::get('platformapi.is_open') ?>:</label>
                <input type="text" class="form-control" required ng-model="ModifyData.is_open"  ng-init="ModifyData.is_open=modifyinit.is_open"/>
            </div>
            <div class="form-group"  ng-if="modifyinit.id == 0">
                <label><?php echo Lang::get('platformapi.is_open') ?>:</label>
                <input type="text" class="form-control" readonly ng-model="ModifyData.is_open"  ng-init="ModifyData.is_open=0"/>
            </div>
            <div class="form-group">
                <label>创建成功后注意上传对应功能的图片，之后再修改公告开启。</label>
            </div>
        </div>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary"><?php echo Lang::get('platformapi.submit')?></button>
            <a class="btn btn-warning" ng-click="cancel()"><?php echo Lang::get('platformapi.cancel')?></a>
        </div>
        </form>
</script>