<script>
function VipPlayersController($scope, $http, alertService, $filter, $modal) {
    $scope.alerts = [];
    $scope.AddData = {};
    $scope.show = 0;
    $scope.pagecontrol = 0;
    $scope.pagination = {};

    $scope.pagination.totalItems = 0;
    $scope.pagination.currentPage = 1;
    $scope.pagination.perPage= 50;

    $scope.$watch('pagination.currentPage', function(newPage, oldPage) {
        if ($scope.pagecontrol > 0 && newPage != oldPage) {
            $scope.check(newPage);
        }
    });

    $scope.check= function(newPage) {
        $scope.alerts = [];
        $scope.AddData.page = newPage;
        alertService.alerts = $scope.alerts;
        $scope.AddData.type = 'check';
        $http({
            'method' : 'post',
            'url' : '/slave-api/vip/players',
            'data' : $.param($scope.AddData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $scope.pagecontrol = 1;
            $scope.pagination.currentPage = data.current_page;
            $scope.pagination.totalItems = data.count;
            $scope.players = data.players;
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    }

    $scope.AddForm= function() {
        $scope.alerts = [];
        alertService.alerts = $scope.alerts;
        $scope.AddData.type = 'add';
        $http({
            'method' : 'post',
            'url' : '/slave-api/vip/players',
            'data' : $.param($scope.AddData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $scope.AddData.new_players = '';
            alertService.add('success', data.msg);
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    }

    $scope.delete = function(id) {
        $scope.alerts = [];
        alertService.alerts = $scope.alerts;
        $scope.AddData.type = 'delete';
        $scope.AddData.id = id;
        $http({
            'method' : 'post',
            'url' : '/slave-api/vip/players',
            'data' : $.param($scope.AddData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $scope.check($scope.pagination.currentPage);
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    }

    $scope.switch = function(){
        $scope.AddData.new_players = '';
        $scope.show = ($scope.show+1)%2;
        if(0 == $scope.show){
            $scope.check(1);
        }
    }

    $(document).ready(function(){
        $scope.check(1);
    });
}

</script>

<div class="col-xs-12" ng-controller="VipPlayersController">
    <button class="btn btn-danger" ng-click="switch()" ng-if="show==0"><?php echo Lang::get('slave.new_vips'); ?></button>
    <button class="btn btn-danger" ng-click="switch()" ng-if="show==1"><?php echo Lang::get('slave.now_vips'); ?></button>
    <div class="row" id="top" ng-show="show==1" style="margin-top:10px">
        <div class="eb-content">
            <form action="" method="get" role="form"
                ng-submit="AddForm()" onsubmit="return false;">
                <div class="form-group">
                    <textarea ng-model="AddData.new_players" style="width:300px;height:500px" placeholder="<?php echo Lang::get('slave.vip_note'); ?>" required>
                    </textarea>
                </div>
                 <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="<?php echo Lang::get('slave.submit'); ?>" />
                </div>
            </form>
        </div>
    </div>
    <div ng-show="show==0" style="margin-top:10px">
        <table class="table table-striped">
            <thead>
                <tr class="info">
                    <td><b><?php echo Lang::get('slave.player_id') ?></b></td>
                    <td><b><?php echo Lang::get('slave.player_name') ?></b></td>
                    <td><b><?php echo Lang::get('slave.created_time') ?></b></td>
                    <td><b><?php echo Lang::get('slave.operator') ?></b></td>
                    <td></td>
                </tr>
            </thead>
            <tbody> 
            <tr ng-repeat="p in players">
                <td>{{p.player_id}}</td>
                <td>{{p.player_name}}</td>
                <td>{{p.created_time}}</td>
                <td>{{p.user_name}}</td>
                <td><button class="btn btn-primary" ng-click="delete(p.id)"><?php echo Lang::get('slave.delete'); ?></button></td>
            </tr>
            </tbody>
        </table>
        <div ng-show="!!pagination.totalItems">
            <pagination total-items="pagination.totalItems"
                page="pagination.currentPage" class="pagination-sm"
                boundary-links="true" rotate="false"
                items-per-page="pagination.perPage" max-size="10"></pagination>
        </div>
    </div>
    
    <div class="row margin-top-10">
        <div class="eb-content">
            <alert ng-repeat="alert in alerts" type="alert.type"
                close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
</div>