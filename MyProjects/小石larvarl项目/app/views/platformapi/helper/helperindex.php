<script>
function HelperIndexController($scope, $http, alertService, $filter, $modal) {
    $scope.alerts = [];

    $scope.modify = function(id, name, ico_name, ico_version, is_open, description, type, data) {
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
                ico_name : function(){
                    return ico_name;
                },
                ico_version : function(){
                    return ico_version;
                },
                is_open : function(){
                    return is_open;
                },
                description : function(){
                    return description;
                },
                type : function(){
                    return type;
                },
                data : function(){
                    return data;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() {
            alert('Modify success'); 
            location.reload(true);  
        });
    }

    $scope.add = function(id) {
        var modalInstance = $modal.open({
            templateUrl: 'add.html',
            controller: HelperAddController,
            resolve: {
                id : function () {
                    return id;
                }
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

function HelperModifyController($scope, $modalInstance, id, name, ico_name, ico_version, is_open, description, type, data, $http, alertService) {
    $scope.modifyinit = {};
    $scope.modifyinit.id = id;
    $scope.modifyinit.ico_name = ico_name;
    $scope.modifyinit.ico_version = ico_version;
    $scope.modifyinit.is_open = is_open;
    $scope.modifyinit.description = description;
    $scope.modifyinit.type = type;
    $scope.modifyinit.name = name;
    $scope.modifyinit.data = data;
    $scope.ModifyData = {};

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    }
    $scope.ModifyForm= function(url) {
        $scope.ModifyData.update = 1;
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

function HelperAddController($scope, $modalInstance, id, $http, alertService){
    $scope.Addinit = {};
    $scope.Addinit.id = id;
    $scope.AddData = {};

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    }
    $scope.AddForm= function(url) {
        $scope.AddData.add = 1;
        $http({
            'method' : 'post',
            'url' : url,
            'data' : $.param($scope.AddData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $modalInstance.close();
        }).error(function(data) {
            alert('error: ' + data.error_description + '\n');
        });
    }
}
</script>
<?php 
    $maxid = 0;
    foreach ($view_data as $k => $v) {
        if($v->id > $maxid){
            $maxid = $v->id;
        }
    }
    $maxid++;
?>

<div class="col-xs-12" ng-controller="HelperIndexController">
    <p style="color:blue;font-size:15px">创建成功或需要修改图片内容的话请点击<a href="<?php echo $link; ?>" target="upload_img_helper"><?php echo $link; ?></a>跳转。</p>
    <table class="table table-striped">
        <thead>
            <tr class="info">
                <td><b><?php echo Lang::get('platformapi.function_id') ?></b></td>
                <td><b><?php echo Lang::get('platformapi.function_name') ?></b></td>
                <td><b><?php echo Lang::get('platformapi.function_ico_name') ?></b></td>
                <td><b><?php echo Lang::get('platformapi.function_ico_version') ?></b></td>
                <td><b><?php echo Lang::get('platformapi.function_is_open') ?></b></td>
                <td><b><?php echo Lang::get('platformapi.function_description') ?></b></td>
                <td><b><?php echo Lang::get('platformapi.function_type') ?></b></td>
                <td><b>DATA</b></td>
                <td><button class="btn btn-warning" ng-click="add(<?php echo $maxid; ?>)"><?php echo Lang::get('platformapi.add_one'); ?></button></td>
            </tr>
        </thead>
        <tbody> 
        <?php foreach ($view_data as $k => $v) { ?>
        <tr>
            <td><?php echo $v->id; ?></td>
            <td><?php if(in_array($v->type, array(1,4))){ ?><a href="/platform-api/mobilegame/helper/single_function?id=<?php echo $v->id; ?>&type=<?php echo $v->type; ?>" target="single_function_<?php echo $v->id; ?>"><button class="btn btn-primary"><?php } echo $v->name; ?><?php if(in_array($v->type, array(1,4))){ ?></button></a><?php } ?></td>
            <td><?php echo $v->ico_name; ?></td>
            <td><?php echo $v->ico_version; ?></td>
            <td><?php echo $v->is_open; ?></td>
            <td><?php echo $v->description; ?></td>
            <td><?php echo $v->type; ?></td>
            <td><?php 
            if(isset($v->data)){
                $datastr = '';
                foreach ($v->data as $key => $value) {
                    $datastr .= $key.'=>'.$value."; ";
                }
                echo $datastr;
            } ?></td>
            <td><button class="btn btn-primary" ng-click="modify(<?php echo $v->id; ?>, '<?php echo $v->name; ?>', '<?php echo $v->ico_name; ?>',<?php echo $v->ico_version; ?>,<?php echo $v->is_open; ?>, '<?php echo $v->description; ?>', <?php echo $v->type; ?>, '<?php echo isset($v->data) ? $datastr : ''; ?>')"><?php echo Lang::get('platformapi.modify'); ?></button></td>
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
        <form action="/project/release" method="post" role="form" ng-submit="ModifyForm('/platform-api/mobilegame/helper')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_id') ?>:</label>    
                <input type="number" class="form-control" readonly required ng-model="ModifyData.id" ng-init="ModifyData.id=modifyinit.id"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_name') ?>:</label>  
                <input type="text" class="form-control" readonly required ng-model="ModifyData.name" ng-init="ModifyData.name=modifyinit.name"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_ico_name') ?>:</label>
                <input type="text" class="form-control" readonly required ng-model="ModifyData.ico_name" ng-init="ModifyData.ico_name=modifyinit.ico_name"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_ico_version') ?>:</label>
                <input type="number" class="form-control" readonly required ng-model="ModifyData.ico_version" ng-init="ModifyData.ico_version=modifyinit.ico_version"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_is_open') ?>:</label>
                <input type="number" class="form-control"  required ng-model="ModifyData.is_open" ng-init="ModifyData.is_open=modifyinit.is_open"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_description') ?>:</label>
                <input type="text" class="form-control" readonly required ng-model="ModifyData.description" ng-init="ModifyData.description=modifyinit.description"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_type') ?>:</label>
                <input type="number" class="form-control" readonly required ng-model="ModifyData.type" ng-init="ModifyData.type=modifyinit.type"/>
            </div>
            <div class="form-group" ng-if="ModifyData.type == 1 || ModifyData.type == 4">
                <label><?php echo Lang::get('platformapi.function_data') ?>:</label>
                <input type="text" class="form-control" readonly required ng-model="ModifyData.data" ng-init="ModifyData.data=modifyinit.data"/>
            </div>
            <div class="form-group" ng-if="ModifyData.type != 1 && ModifyData.type != 4">
                <label><?php echo Lang::get('platformapi.function_data') ?>:</label>
                <input type="text" class="form-control" required ng-model="ModifyData.data" ng-init="ModifyData.data=modifyinit.data"/>
            </div>
        </div>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary"><?php echo Lang::get('platformapi.submit')?></button>
            <a class="btn btn-warning" ng-click="cancel()"><?php echo Lang::get('platformapi.cancel')?></a>
        </div>
        </form>
</script>

<script type="text/ng-template" id="add.html">
        <div class="modal-header">
        </div>
        <form action="/project/release" method="post" role="form" ng-submit="AddForm('/platform-api/mobilegame/helper')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_id') ?>:</label>    
                <input type="number" class="form-control" readonly required ng-model="AddData.id" ng-init="AddData.id=Addinit.id"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_name') ?>:</label>  
                <input type="text" class="form-control" required ng-model="AddData.name"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_is_open') ?>:</label>
                <input type="number" readonly class="form-control" required ng-model="AddData.is_open" ng-init="AddData.is_open=0" />
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_description') ?>:</label>
                <input type="text" class="form-control" required ng-model="AddData.description"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.function_type') ?>:(1:答疑;2:图片跳转;3:FB分享;4:公告;5:网络测试)</label>
                <input type="number" class="form-control" required ng-model="AddData.type"/>
            </div>
            <div class="form-group" ng-if="AddData.type!=1 && AddData.type!=5">
                <label><?php echo Lang::get('platformapi.function_data') ?>:</label>
                <input type="text" class="form-control" required ng-model="AddData.data"/>
            </div>
            <div class="form-group">
                <label style="color:red">请勿省略功能数据中的分号</label>
            </div>
            <div class="form-group">
                <label><b color="red">功能类型1,5:</b><?php echo Lang::get('platformapi.function_data') ?>不需要填写; </label>
                <label><b color="red">功能类型2:</b><?php echo Lang::get('platformapi.function_data') ?>填写 link_url=>链接地址; 如: link_url=>http://www.baidu.com; </label>
            <?php 
                $pic_url = substr($link, 0, strpos($link, '/upload')).'/assets/img/upload/'.Session::get('game_id');
             ?>
                <label><b color="red">功能类型3:</b><?php echo Lang::get('platformapi.function_data') ?>填写 game_des=>分享内容描述;click_url=>分享成功后点击分享内容跳转的地址;
                        show_content=>你想要分享的内容;picture_url=>分享的图片链接; 
                        <br>注：分享的图片链接，请先点击<a href="<?php echo $link.'/../'; ?>" style="color:red" target="upload_img"><div class="btn btn-primary">链接</div></a>上传,成功后把<?php echo $pic_url; ?>/图片名 填写在picture_url=>后面
                </label>
                <label><b color="red">功能类型4:</b><?php echo Lang::get('platformapi.function_data') ?>填写 name=>第一条公告名; 如: name=>2016-01-05更新公告; </label>
            </div>
            <div class="form-group">
                <label style="color:red">创建成功后注意上传对应功能的图片，之后再修改开启。</label>
            </div>
        </div>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary"><?php echo Lang::get('platformapi.submit')?></button>
            <a class="btn btn-warning" ng-click="cancel()"><?php echo Lang::get('platformapi.cancel')?></a>
        </div>
        </form>
</script>