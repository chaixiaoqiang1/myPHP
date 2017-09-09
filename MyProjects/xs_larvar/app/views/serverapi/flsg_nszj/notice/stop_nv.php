<script type="text/javascript">
function serverNoticeController($scope,$http,alertService,$filter){
    $scope.alerts = [];
    $scope.notices = [];
    $scope.formData = {};
    $scope.start_time = null;
    $scope.delete_id = null;
    $scope.stopNotice = function(t){
        alertService.alerts = $scope.alerts;
        $scope.delete_id = t;
        // $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method' : 'post',
            'url' : '/game-server-api/stopNotice_nv?tid='+t,
            'data' : $.param($scope.formData),
            'headers' : {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data){
            if(data['res']=='OK'){
                alertService.add('success',data['msg']);
                for(var index in $scope.notices){
                    if($scope.notices[index].BulletinID==$scope.delete_id)
                        $scope.notices.splice(index,1);
                }
            }else if(data['res']=='error'){
                alertService.add('danger',data['msg']);
            }
        }).error(function(data){
            alertService.add('danger',data.error);
        });
    };

    $scope.lookupNotice = function(url){
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url' : url,
            'data' : $.param($scope.formData),
            'headers' : {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data){
            //alert(data.length);
            $scope.notices = data;
        }).error(function(data){
            alertService.add('danger',data);
        });
    };

}
</script>
<div class="col-xs-12" ng-controller="serverNoticeController">
    <div class="row">
        <div class="eb-content">

            <form action="/game-server-api/notice" method="post" ng-submit="processForm()" onsubmit="return false;">
                <div class="form-group">
                    <select class="form-control" name="server_id" id="select_game_server"  ng-model="formData.server_id" ng-init="formData.server_id=0" ng-change="lookupNotice('/game-server-api/lookupNotice')">
                         <option value="0" selected>--<?php echo Lang::get('serverapi.select_game_server') ?>--</option>
                        <?php foreach ($servers as $k => $v) { ?>
                            <option value="<?php echo $v->server_id?>" ><?php echo $v->server_name.'----'.$v->game_id.'--'.$v->server_id.'--'.$v->server_internal_id;?></option>
                        <?php } ?>      
                    </select>
                </div>
            </form>
        </div>
    </div>
        <div class="row margin-top-10">
        <div class="eb-content"> 
            <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
    <br/>
    <div class="col-xs-12">
        <table class="table table-striped">
            <thead>
                <tr class="info">
                    <td><b><?php echo Lang::get('amount.bulletin_id')?></b></td>
                    <td><b><?php echo Lang::get('amount.bulletin_content');?></b></td>
                    <td><b><?php echo Lang::get('amount.interval')?></b></td>
                    <td><b><?php echo Lang::get('amount.start_time')?></b></td>
                    <td><b><?php echo Lang::get('amount.expiration_time');?></b></td>
                    <td><b><?php echo Lang::get('amount.operator');?></b></td>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="t in notices">
                    <td>{{t.BulletinID}}</td>
                    <td>{{t.content_txt}}</td>
                    <td>{{t.interval}}</td>
                    <td>{{t.StartTime}}</td>
                    <td>{{t.ExpirationTime}}</td>
                    <td><button ng-click="stopNotice(t.BulletinID)" class="btn btn-default"><?php echo Lang::get('amount.delete')?></button></td>
                </tr>
            </tbody>
        </table>
        
    </div>
</div>