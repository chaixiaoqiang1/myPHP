<script type="text/javascript">
    function loadGMController1($scope, $http, alertService, $modal)
    {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.questions = [];
        $scope.reply_dones= [];
        $scope.processFrom = function(url) {
            alertService.alerts = $scope.alerts;
            $http({
                'method' : 'post',
                'url'    : url,
                'data'   : $.param($scope.formData),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                $scope.comments = data;
            }).error(function(data) {
                alertService.add('danger',  data.error);
            });
        };

        $scope.processFrom2 = function(url) {
            alertService.alerts = $scope.alerts;
            $scope.formData.comment_id = $('#comment_id').val();
            $http({
                'method' : 'post',
                'url'    : url,
                'data'   : $.param($scope.formData),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                alert('处理成功!（重新查询可看到结果）');
            }).error(function(data) {
                alert('处理失败!');
            });
        };

        $scope.dealthiscomment = function(target){
                $scope.comment_id = target.getAttribute('data'); 
                $('#comment_id').val($scope.comment_id);
        };
    }
</script>
<div class="col-xs-12" ng-controller="loadGMController1">
    <div class="row" >
        <div class="col-xs-2" >
            <form action="/game-server-api/yysg/comment" method="post" role="form"
                  ng-submit="processFrom('/game-server-api/yysg/comment')"
                  onsubmit="return false;">
                <div class="form-group"  style="margin-top:20px">
                    <p>页码:</p><input type="number" class="form-control" id="current_page"
                        placeholder="请输入页码数" required
                         ng-model="formData.current_page" name="current_page" />
                    <p>每页显示条数:</p><input style="margin-top:5px" type="number" class="form-control" id="num_per_page" required
                        placeholder="请输入每页显示数"
                         ng-model="formData.num_per_page" name="num_per_page" />
                    <select style="margin-top:5px" class="form-control" name="show_delete" ng-model="formData.show_delete" ng-init="formData.show_delete=0">
                        <option value="0">显示未删除的评论</option>
                        <option value="1">显示已删除的评论</option>
                    </select>
                    <select style="margin-top:5px" class="form-control" name="is_like" ng-model="formData.is_like" ng-init="formData.is_like=0">
                        <option value="0">按照时间排序</option>
                        <option value="1">按照赞数排序</option>
                    </select>
                    <p>输入玩家ID查询(可不填):</p><input type="number" class="form-control" id="player_id"
                        placeholder="按照玩家ID查询"
                         ng-model="formData.player_id" name="player_id" />
                    <p>输入武将ID查询(可不填):</p><input type="number" class="form-control" id="table_id"
                        placeholder="按照武将ID查询"
                         ng-model="formData.table_id" name="table_id" />
                    <input  style="margin-top:5px" type="submit" class="btn btn-success" value="查询评论">
                </div>
            </form>
            <form action="/game-server-api/yysg/dealcomment" method="post" role="form"
                  ng-submit="processFrom2('/game-server-api/yysg/dealcomment')"
                  onsubmit="return false;">
                <div style="margin-top:50px">
                    <p>所处理的评论ID(点击评论后的按钮):</p><input type="number" class="form-control" id="comment_id"
                        placeholder="需要处理的评论ID" value=""  disabled="disabled" 
                         ng-model="formData.comment_id" name="comment_id" />
                    <select  style="margin-top:5px" class="form-control" name="deal_type" ng-model="formData.deal_type" ng-init="formData.deal_type=1">
                        <option value="1">修改赞数</option>
                        <option value="2">删除评论</option>
                        <option value="3">撤销删除</option>
                    </select>
                    <p>如果修改赞数，请在此输入数字:</p><input  style="margin-top:5px" type="number" class="form-control" id="likes_num"
                        placeholder="修改赞数为"
                         ng-model="formData.likes_num" name="likes_num" />
                    <input  style="margin-top:5px" type="submit" class="btn btn-success" value="处理评论">
                </div>
            </form>
        </div>
    </script>
        <div class="col-xs-10" style="overflow-y:auto;height:700px;">
            <p ng-repeat="c in comments">
                Name：{{c.player_name}}---<small><b>(COMMENT_ID：{{c.id}})</b>---(Time：{{c.created_at}})---<b>(LIKES：{{c.like_count}})</b></small>  
                <input type="button" value="获取本评论ID" ng-click="dealthiscomment($event.target)" class="dealcomment" data="{{c.id}}"/>
                <br>
                <b style="color:#f00">{{c.content}}</b>
            </p>
        </div>
        <!-- /.col -->
    </div>
            <div class="row margin-top-10">
            <div class="eb-content">
                <alert ng-repeat="alert in alerts" type="alert.type"
                    close="alert.close()">{{alert.msg}}</alert>
            </div>
        </div>
</div>