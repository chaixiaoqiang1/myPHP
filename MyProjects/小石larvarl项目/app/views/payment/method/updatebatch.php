<script type="text/javascript">
function updateBatchController($scope,$http,alertService,$filter){
    $scope.alerts = [];
    $scope.formData = {};
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.now_on = [<?php foreach ($now_on as $value) {
        echo $value.",";
    } ?>];

    $scope.processForm = function(url){
        var warning = 0;
        if($scope.now_on.length && $scope.formData.method_id.length){
            for(var i = 0;i < $scope.formData.method_id.length;i++){
                if($scope.now_on.indexOf(parseInt($scope.formData.method_id[i])) > -1){
                    warning = 1;
                    break;
                }
            }
        }
        if(warning > 0){
            if(!confirm("将更新某些正处于开启状态的支付方法，是否确认？")){
                return;
            }
        }
        alertService.alerts = $scope.alerts;
        $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
        $scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method' : 'post',
            'url' : url,
            'data' : $.param($scope.formData),
            'headers' : {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data){
            var len = data.length;
            for(var i =0;i<len;i++){
                if(data[i].status == 'ok'){
                    alertService.add('success',data[i].msg);
                }else if(data[i].status == 'error'){
                    alertService.add('danger',data[i].msg);
                }
            }
            setTimeout('myrefresh()', 200);
        }).error(function(data){
            alertService.add('danger',data.error);
        });
    };

}
</script>
<div class="col-xs-12" ng-controller="updateBatchController">
    <div class="row">
        <div class="eb-content">

            <form action="/pay-method/batch-update" method="post" role="form" ng-submit="processForm('/pay-method/batch-update')" onsubmit="return false;">
                <div class="form-group">
                    <select class="form-control" name="method_id" id="select_method" required ng-model="formData.method_id" multiple="multiple" ng-multiple="true" size=10>
                        <optgroup label="<?php echo Lang::get('serverapi.select_method') ?>">
                        <?php foreach ($methods as $k => $v) { ?>
                            <option value="<?php echo $v->platform_method_id; ?>" <?php if(in_array($v->platform_method_id, $now_on)){ echo 'style="color:red"';} ?>><?php echo $v->method_name.'----'.$v->method_description;?></option>
                        <?php } ?>      
                        </optgroup>
                    </select>
                </div>
                <div class="form-group" style="height:30px;">
                    <div class="col-md-2" style="padding: 0 0 0 0"><label>活动开始时间</label></div>
                    <div class="col-md-6" style="padding: 0 0 0 0">                       
                        <div class="input-group">
                            <quick-datepicker ng-model="start_time" init-value="00:00:00" ></quick-datepicker> 
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="height:30px;">
                    <div class="col-md-2" style="padding: 0 0 0 0"><label>活动结束时间</label></div>
                    <div class="col-md-6" style="padding: 0 0 0 0">                       
                        <div class="input-group">
                            <quick-datepicker ng-model="end_time" init-value="23:59:59" ></quick-datepicker> 
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12" style="margin-left:-15px">
                    <input type="text" class="form-control" id="huodong_rate"  placeholder="活动比例"  ng-model="formData.huodong_rate" name="huodong_rate" ng-init = "formData.huodong_rate =
                    <?php $id = Session::get('game_id');
                        if(in_array($id, $new_projects) || $id > 50)
                            echo 0.1;
                        else echo 1;?>
                    "/>
                </div>

                <div class="form-group" style="height: 30px;">
                    <br/>
                    <span style = "color:red; font-size:16px;">
                        <?php   $id = Session::get('game_id');
                        if(in_array($id, $new_projects) || $id > 50)
                            echo Lang::get('serverapi.rate_introduce_new_platform');
                        else echo Lang::get('serverapi.rate_introduce');?>
                    </span>
                </div>
                <br>
                <br>
                <div class="form-group" style="height:40px;">
                    <input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit')?>"/>
                </div>
            </form>
        </div>
    </div>

    <div class="row margin-top-12">
        <div class="eb-content"> 
            <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>

    <div style="margin-top:10px">
        <table class="table table-striped">
            <thead>
                <tr class="info">
                    <td><b><?php echo Lang::get('serverapi.method_name') ?></b></td>
                    <td><b><?php echo Lang::get('slave.start_time') ?></b></td>
                    <td><b><?php echo Lang::get('slave.end_time') ?></b></td>
                    <td><b><?php echo Lang::get('slave.huodong_rate') ?></b></td>
                    <td><b><?php echo Lang::get('slave.is_on_now') ?></b></td>
                </tr>
            </thead>
            <tbody> 
            <?php foreach ($payment_activities as $value) { ?>
                <tr>
                    <td><?php echo $value->method_name; ?></td>
                    <td><?php echo $value->start_time; ?></td>
                    <td><?php echo $value->end_time; ?></td>
                    <td><?php echo $value->huodong_rate; ?></td>
                    <td><?php if(date("Y-m-d H:i:s", time()) > $value->start_time && date("Y-m-d H:i:s", time()) < $value->end_time){
                            echo '<b style="color:red">YES</b>';
                        }else{
                            echo "<b>NO</b>";
                            } ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>