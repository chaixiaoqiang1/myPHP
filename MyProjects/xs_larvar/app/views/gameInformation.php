<script> 
function modifyDataController($scope, $modalInstance, order, $http, alertService) {
    $scope.region_id = '<?php echo Platform::find(Session::get('platform_id'))->region_id?>';
    $scope.order = order;
    $scope.orderData = {};
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
            alert("修改成功");
            window.location.reload();
        }).error(function(data) {
            alert('error: ' + data.error + '\n');
        });
    }
}

function insertDataController($scope, $modalInstance, order, $http, alertService) {
    $scope.region_id = '<?php echo Platform::find(Session::get('platform_id'))->region_id?>';
    $scope.order = order;
    $scope.orderData = {};
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
            alert("新增成功");
            window.location.reload();
        }).error(function(data) {
            alert('error: ' + data.error + '\n');
        });
    }
}

function gameInformationController($scope, $http, alertService,$modal, $filter) {
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
            'url': '/platform-api/mobilegame/gameinformation',
            'data': $.param($scope.formData),
            'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            alertService.add('success', data.msg);
            setTimeout('window.location.reload()',500);
        }).error(function(data) {
            alertService.add('danger', data.msg);
        });
    };

    $scope.tp_applications_modify = function(id,tp_code,app_id,app_secret,app_access_token,fanpage_url) {
        var modalInstance = $modal.open({
            templateUrl: 'tp_applications_modify.html',
            controller: modifyDataController,
            resolve: {
                order : function () {
                    var order=[id,tp_code,app_id,app_secret,app_access_token,fanpage_url]
                    return order;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() {
        });
    }

    $scope.applications_modify = function(id,name,client_id,client_secret,redirect_uri,auto_approve,autonomous,status,suspended,notes) {
        var modalInstance = $modal.open({
            templateUrl: 'applications_modify.html',
            controller: modifyDataController,
            resolve: {
                order : function () {
                    var order=[id,name,client_id,client_secret,redirect_uri,auto_approve,autonomous,status,suspended,notes]
                    return order;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() { 
        });
    }

    $scope.game_list_qiqiwu_modify = function(game_id,game_name,game_lib,short_name,url,helper_name,helper_version) {
        var modalInstance = $modal.open({
            templateUrl: 'game_list_qiqiwu_modify.html',
            controller: modifyDataController,
            resolve: {
                order : function () {
                    var order=[game_id,game_name,game_lib,short_name,url]
                    return order;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() { 
        });
    }

    $scope.game_list_payment_modify = function(game_id,game_name,game_lib,on_recharge,sdk_recharge,giftbag_recharge,version,cs_email,fb_name) {
        var modalInstance = $modal.open({
            templateUrl: 'game_list_payment_modify.html',
            controller: modifyDataController,
            resolve: {
                order : function () {
                    var order=[game_id,game_name,game_lib,on_recharge,sdk_recharge,giftbag_recharge,version,cs_email,fb_name]
                    return order;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() {
        });
    }

    $scope.goods_list_modify = function(game_id,goods_type_id,goods_name,on_recharge) {
        var modalInstance = $modal.open({
            templateUrl: 'goods_list_modify.html',
            controller: modifyDataController,
            resolve: {
                order : function () {
                    var order=[game_id,goods_type_id,goods_name,on_recharge]
                    return order;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() {
        });
    }

    $scope.tp_applications_insert = function() {
        var modalInstance = $modal.open({
            templateUrl: 'tp_applications_modify.html',
            controller: insertDataController,
            resolve: {
                order : function () {
                    var order=[]
                    return order;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() { 
        });
    }

    $scope.applications_insert = function() {
        var modalInstance = $modal.open({
            templateUrl: 'applications_modify.html',
            controller: insertDataController,
            resolve: {
                order : function () {
                    var order=[]
                    return order;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() { 
        });
    }

    $scope.game_list_qiqiwu_insert = function() {
        var modalInstance = $modal.open({
            templateUrl: 'game_list_qiqiwu_modify.html',
            controller: insertDataController,
            resolve: {
                order : function () {
                    var order=[]
                    return order;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() { 
        });
    }

    $scope.game_list_payment_insert = function() {
        var modalInstance = $modal.open({
            templateUrl: 'game_list_payment_modify.html',
            controller: insertDataController,
            resolve: {
                order : function () {
                    var order=[]
                    return order;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() {
        });
    }

    $scope.goods_list_insert = function() {
        var modalInstance = $modal.open({
            templateUrl: 'goods_list_modify.html',
            controller: insertDataController,
            resolve: {
                order : function () {
                    var order=[]
                    return order;
                }
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() {
        });
    }
} 
</script>
<div class="col-xs-12" ng-controller="gameInformationController">
    <form action="/platform-api/mobilegame/gameinformation" method="post" role="form"
                 onsubmit="return false;">
        
        <div class="row margin-top-10">
            <div class="eb-content">
                <alert ng-repeat="alert in alerts" type="alert.type"
                    close="alert.close()">{{alert.msg}}</alert>
            </div>
        </div>
        <div class="col-xs-12">
            <b>tp_applications表</b>
            <table class="table table-striped">
                <thead>
                    <tr class="info" id="server">
                        <td>tp_code</td>
                        <td>app_id</td>
                        <td>app_secret</td>
                        <td>app_access_token</td>
                        <td>fanpage_url</td>
                        <?php if(count($result['tp_applications'])==0){?>        
                        <td><input type="button" value="新增" ng-click="tp_applications_insert()"></td>
                        <?php }else{?>
                        <td></td>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['tp_applications'] as $value) {?>
                        <tr>
                            <td><?php echo $value->tp_code ?></td>
                            <td><?php echo $value->app_id ?></td>
                            <td><?php echo $value->app_secret ?></td>
                            <td><?php echo $value->app_access_token ?></td>
                            <td><?php echo $value->fanpage_url ?></td>
                            <td><input type="button" value="修改" ng-click="tp_applications_modify('<?php echo $value->id ?>', '<?php echo $value->tp_code ?>','<?php echo $value->app_id ?>','<?php echo $value->app_secret ?>','<?php echo $value->app_access_token ?>','<?php echo $value->fanpage_url ?>')"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <b>applications表</b>
            <table class="table table-striped">
                <thead>
                    <tr class="info" id="server">
                        <td>游戏名称</td>
                        <td>client_id</td>
                        <td>client_secret</td>
                        <td>redirect_uri</td>
                        <td>auto_approve</td>
                        <td>autonomous</td>
                        <td>status</td>
                        <td>suspended</td>
                        <td>notes</td>
                        <?php if(count($result['applications'])==0){?>        
                        <td><input type="button" value="新增" ng-click="applications_insert()"></td>
                        <?php }else{?>
                        <td></td>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['applications'] as $value) {?>
                        <tr>
                            <td><?php echo $value->name ?></td>
                            <td><?php echo $value->client_id ?></td>
                            <td><?php echo $value->client_secret ?></td>
                            <td><?php echo $value->redirect_uri ?></td>
                            <td><?php echo $value->auto_approve ?></td>
                            <td><?php echo $value->autonomous ?></td>
                            <td><?php echo $value->status ?></td>
                            <td><?php echo $value->suspended ?></td>
                            <td><?php echo $value->notes ?></td>
                            <td><input type="button" value="修改" ng-click="applications_modify('<?php echo $value->id ?>', '<?php echo $value->name ?>','<?php echo $value->client_id ?>','<?php echo $value->client_secret ?>','<?php echo $value->redirect_uri ?>','<?php echo $value->auto_approve ?>','<?php echo $value->autonomous ?>','<?php echo $value->status ?>','<?php echo $value->suspended ?>','<?php echo $value->notes ?>')"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <b>game_list_qiqiwu表</b>
            <table class="table table-striped">
                <thead>
                    <tr class="info" id="server">
                        <td>游戏名称</td>
                        <td>game_lib</td>
                        <td>short_name</td>
                        <td>url</td>
                        <?php if(count($result['game_list_qiqiwu'])==0){?>        
                        <td><input type="button" value="新增" ng-click="game_list_qiqiwu_insert()"></td>
                        <?php }else{?>
                        <td></td>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['game_list_qiqiwu'] as $value) {?>
                        <tr>
                            <td><?php echo $value->game_name ?></td>
                            <td><?php echo $value->game_lib ?></td>
                            <td><?php echo $value->short_name ?></td>
                            <td><?php echo $value->url ?></td>
                            <td><input type="button" value="修改" ng-click="game_list_qiqiwu_modify('<?php echo $value->game_id ?>', '<?php echo $value->game_name ?>','<?php echo $value->game_lib ?>','<?php echo $value->short_name ?>','<?php echo $value->url ?>')"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <b>game_list_payment表</b>
            <table class="table table-striped">
                <thead>
                    <tr class="info" id="server">
                        <td>游戏名称</td>
                        <td>game_lib</td>
                        <td>on_recharge</td>
                        <td>sdk_recharge</td>
                        <td>giftbag_recharge</td>
                        <td>version</td>
                        <td>cs_email</td>
                        <td>fb_name</td>
                        <?php if(count($result['game_list_payment'])==0){?>        
                        <td><input type="button" value="新增" ng-click="game_list_payment_insert()"></td>
                        <?php }else{?>
                        <td></td>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['game_list_payment'] as $value) {?>
                        <tr>
                            <td><?php echo $value->game_name ?></td>
                            <td><?php echo $value->game_lib ?></td>
                            <td><?php echo $value->on_recharge ?></td>
                            <td><?php echo $value->sdk_recharge ?></td>
                            <td><?php echo $value->giftbag_recharge ?></td>
                            <td><?php echo $value->version ?></td>
                            <td><?php echo $value->cs_email ?></td>
                            <td><?php echo $value->fb_name ?></td>
                            <td><input type="button" value="修改" ng-click="game_list_payment_modify('<?php echo $value->game_id ?>', '<?php echo $value->game_name ?>','<?php echo $value->game_lib ?>','<?php echo $value->on_recharge ?>','<?php echo $value->sdk_recharge ?>','<?php echo $value->giftbag_recharge ?>','<?php echo $value->version ?>','<?php echo $value->cs_email ?>','<?php echo $value->fb_name ?>')"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <b>goods_list表</b>
            <table class="table table-striped">
                <thead>
                    <tr class="info" id="server">
                        <td>货物类型ID</td>
                        <td>货物名称</td>
                        <td>on_recharge</td>
                        <?php if(count($result['tmp_goods_list'])==0){?>        
                        <td><input type="button" value="新增" ng-click="goods_list_insert()"></td>
                        <?php }else{?>
                        <td></td>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['tmp_goods_list'] as $value) {?>
                        <tr>
                            <td><?php echo $value->goods_type_id ?></td>
                            <td><?php echo $value->goods_name ?></td>
                            <td><?php echo $value->on_recharge ?></td>
                            <td><input type="button" value="修改" ng-click="goods_list_modify('<?php echo $value->game_id ?>', '<?php echo $value->goods_type_id ?>','<?php echo $value->goods_name ?>','<?php echo $value->on_recharge ?>')"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div> 
    </form>
</div>

<script type="text/ng-template" id="tp_applications_modify.html">
        <div class="modal-header">
        </div>
        <form action="/platform-api/mobilegame/formdata_modify?table_name=tp_applications" method="post" role="form" ng-submit="modifyForm('/platform-api/mobilegame/formdata_modify?table_name=tp_applications')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label>tp_code:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.tp_code" autofocus="autofocus" ng-init="orderData.tp_code = order[1]" required/></input>
            </div>
            <div class="form-group">
                <label>app_id:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.app_id" autofocus="autofocus" ng-init="orderData.app_id = order[2]" required/></input>
            </div>
            <div class="form-group">
                <label>app_secret:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.app_secret" autofocus="autofocus" ng-init="orderData.app_secret = order[3]" required/></input>
            </div>
            <div class="form-group">
                <label>app_access_token:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.app_access_token" autofocus="autofocus" ng-init="orderData.app_access_token = order[4]" required/></input>
            </div>
            <div class="form-group">
                <label>fanpage_url:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.fanpage_url" autofocus="autofocus" ng-init="orderData.fanpage_url = order[5]" required/></input>
            </div>
        </div>
        <input type="hidden" ng-model="orderData.id" ng-init="orderData.id = order[0]" name="id"/>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary">确认</button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>

<script type="text/ng-template" id="applications_modify.html">
        <div class="modal-header">
        </div>
        <form action="/platform-api/mobilegame/formdata_modify?table_name=applications" method="post" role="form" ng-submit="modifyForm('/platform-api/mobilegame/formdata_modify?table_name=applications')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label>游戏名称:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.name" autofocus="autofocus" ng-init="orderData.name = order[1]" required/></input>
            </div>
            <div class="form-group">
                <label>client_id:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.client_id" autofocus="autofocus" ng-init="orderData.client_id = order[2]" required/></input>
            </div>
            <div class="form-group">
                <label>client_secret:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.client_secret" autofocus="autofocus" ng-init="orderData.client_secret = order[3]" required/></input>
            </div>
            <div class="form-group">
                <label>redirect_uri:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.redirect_uri" autofocus="autofocus" ng-init="orderData.redirect_uri = order[4]" required/></input>
            </div>
            <div class="form-group">
                <label>auto_approve:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.auto_approve" autofocus="autofocus" ng-init="orderData.auto_approve = order[5]" required/></input>
            </div>
            <div class="form-group">
                <label>autonomous:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.autonomous" autofocus="autofocus" ng-init="orderData.autonomous = order[6]" required/></input>
            </div>
            <div class="form-group">
                <label>status:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.status" autofocus="autofocus" ng-init="orderData.status = order[7]" required/></input>
            </div>
            <div class="form-group">
                <label>suspended:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.suspended" autofocus="autofocus" ng-init="orderData.suspended = order[8]" required/></input>
            </div>
            <div class="form-group">
                <label>notes:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.notes" autofocus="autofocus" ng-init="orderData.notes = order[9]" required/></input>
            </div>
        </div>
        <input type="hidden" ng-model="orderData.id" ng-init="orderData.id = order[0]" name="id"/>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary">确认</button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>

<script type="text/ng-template" id="game_list_qiqiwu_modify.html">
        <div class="modal-header">
        </div>
        <form action="/platform-api/mobilegame/formdata_modify?table_name=gamelistqiqiwu" method="post" role="form" ng-submit="modifyForm('/platform-api/mobilegame/formdata_modify?table_name=gamelistqiqiwu')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label>游戏名称:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.game_name" autofocus="autofocus" ng-init="orderData.game_name = order[1]" required/></input>
            </div>
            <div class="form-group">
                <label>game_lib:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.game_lib" autofocus="autofocus" ng-init="orderData.game_lib = order[2]" required/></input>
            </div>
            <div class="form-group">
                <label>short_name:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.short_name" autofocus="autofocus" ng-init="orderData.short_name = order[3]" required/></input>
            </div>
            <div class="form-group">
                <label>url:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.url" autofocus="autofocus" ng-init="orderData.url = order[4]" required/></input>
            </div>
        </div>
        <input type="hidden" ng-model="orderData.game_id" ng-init="orderData.game_id = order[0]" name="game_id"/>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary">确认</button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>

<script type="text/ng-template" id="game_list_payment_modify.html">
        <div class="modal-header">
        </div>
        <form action="/platform-api/mobilegame/formdata_modify?table_name=gamelistpayment" method="post" role="form" ng-submit="modifyForm('/platform-api/mobilegame/formdata_modify?table_name=gamelistpayment')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label>游戏名称:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.game_name" autofocus="autofocus" ng-init="orderData.game_name = order[1]" required/></input>
            </div>
            <div class="form-group">
                <label>game_lib:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.game_lib" autofocus="autofocus" ng-init="orderData.game_lib = order[2]" required/></input>
            </div>
            <div class="form-group">
                <label>on_recharge:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.on_recharge" autofocus="autofocus" ng-init="orderData.on_recharge = order[3]" required/></input>
            </div>
            <div class="form-group">
                <label>sdk_recharge:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.sdk_recharge" autofocus="autofocus" ng-init="orderData.sdk_recharge = order[4]" required/></input>
            </div>
            <div class="form-group">
                <label>giftbag_recharge:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.giftbag_recharge" autofocus="autofocus" ng-init="orderData.giftbag_recharge = order[5]" required/></input>
            </div>
            <div class="form-group">
                <label>version:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.version" autofocus="autofocus" ng-init="orderData.version = order[6]" required/></input>
            </div>
            <div class="form-group">
                <label>cs_email:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.cs_email" autofocus="autofocus" ng-init="orderData.cs_email = order[7]" required/></input>
            </div>
            <div class="form-group">
                <label>fb_name:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.fb_name" autofocus="autofocus" ng-init="orderData.fb_name = order[8]" required/></input>
            </div>
        </div>
        <input type="hidden" ng-model="orderData.game_id" ng-init="orderData.game_id = order[0]" name="game_id"/>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary">确认</button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>

<script type="text/ng-template" id="goods_list_modify.html">
        <div class="modal-header">
        </div>
        <form action="/platform-api/mobilegame/formdata_modify?table_name=goodslist" method="post" role="form" ng-submit="modifyForm('/platform-api/mobilegame/formdata_modify?table_name=goodslist')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label>货物类型ID:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.goods_type_id" autofocus="autofocus" ng-init="orderData.goods_type_id = order[1]" required/></input>
            </div>
            <div class="form-group">
                <label>货物名称:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.goods_name" autofocus="autofocus" ng-init="orderData.goods_name = order[2]" required/></input>
            </div>
            <div class="form-group">
                <label>on_recharge:</label>
                <input type="text" class="form-control" ng-cols="30"  ng-model="orderData.on_recharge" autofocus="autofocus" ng-init="orderData.on_recharge = order[3]" required/></input>
            </div>
        </div>
        <input type="hidden" ng-model="orderData.game_id" ng-init="orderData.game_id = order[0]" name="game_id"/>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary">确认</button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>