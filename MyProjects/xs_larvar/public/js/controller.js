//(function() {
	/*global $, jQuery, angular, alert, eastblueApp, window, location, confirm*/
	function createGroupController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/groups',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.group_name = '';
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
	
	function createAppController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/apps',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.app_name = '';
				$scope.formData.app_key = '';
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
	
	function updateAppController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function createDepartmentController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/department',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.department_name = '';
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}


	function createUserController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/users',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.username = '';
				$scope.formData.email = '';
				$scope.formData.password = '';
				$scope.formData.password_confirmation = '';
				$scope.formData.is_admin = 0;
				$scope.formData.department_id = 0;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function updateUserProfileController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function updateUserPasswordController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				setTimeout('window.location.reload()',500);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function updateUserPermissionController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.formData.permissions = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
		$scope.is_checked = function(department_id, app_ids) {
			var is_checked = $scope['is_checked_' + department_id];
			var ids = app_ids.split(',');
			var i = 0;
			for (i=0; i<ids.length; i++) {
				if (is_checked === '1') {
					$scope.formData.permissions[ids[i]] = ids[i];
				} else {
					$scope.formData.permissions[ids[i]] = '0';
				}
			}
		};
	}

	function updateUserGamesController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.formData.games = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}


	function updateGroupNameController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}


	function updateGroupAppController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.formData.apps = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function updateGroupAppController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.formData.apps = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function createRegionController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.region_code = '';
				$scope.formData.region_name = '';
				$scope.formData.timezone = '';
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function updateRegionController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}


	function createPlatformController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.platform_url = '';
				$scope.formData.platform_name = '';
				$scope.formData.region_id = 0;
				$scope.formData.default_game_id = 0;
				$scope.formData.platform_api_url = '';
				$scope.formData.payment_api_url = '';
				$scope.formData.api_key = '';
				$scope.formData.api_secret_key = '';
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function updatePlatformController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}


	function createGameController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.game_name = '';
				$scope.formData.platform_id = 0;
				$scope.formData.is_recommend = 0;
				$scope.formData.eb_api_url = '';
				$scope.formData.eb_api_key = '';
				$scope.formData.eb_api_secret_key = '';
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function updateGameController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
		
	function createOrganizationController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.organ_name = '';
				$scope.formData.allowed_ips = '';
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
	
	function updateOrganizationController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
	
	function updateSettingController ($scope, $http, alertService){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function updateAdLpController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
	
	function createAdLpController($scope, $http, alertService) {
	    
	    $scope.alerts = [];
		$scope.formData = {};
		$scope.getSource = function() {
			$http({
				'method' : 'post',
				'url'	 : '/adslp/getSource',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.source = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
	    
	    $scope.alerts = [];
		$scope.formData = {};
		$scope.getCampaign = function() {
			$http({
				'method' : 'post',
				'url'	 : '/adslp/getCampaign',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.campaign = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
		
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.lp_name = '';
				$scope.formData.resource_url = '';
				$scope.formData.lp_url = '';
				$scope.formData.source = '';
				$scope.formData.campaign = '';
				$scope.formData.reg_success_js = '';
				$scope.formData.create_player_success_js = '';
				$scope.formData.recharge_success_js = '';
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
	
	
	function updateAdLinkController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg, 2000);
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
	}
	
	function createAttrController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.attr_key = '';
				$scope.formData.attr_value = '';
				$scope.formData.lp_id = '';
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
	
	function updateAttrController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
	
	
	
	function createAdCampaignController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.source = 0;
				$scope.formData.campaign_name = '';
				$scope.formData.campaign_value = '';
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function searchServerPlayerController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.players = [];
		$scope.processFrom = function(url) {
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.players = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}


	function freezePlayerAccountController($scope, $http, alertService)
	{
		$scope.alerts1 = [];
		$scope.alerts2 = [];
		$scope.formData1 = {};
		$scope.formData2 = {};
		$scope.processFrom1 = function(url) {
			alertService.alerts = $scope.alerts1;
			$http({
				'method' : 'post',
				'url'	 : url+"?form=1",
				'data'   : $.param($scope.formData1),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
		$scope.processFrom2 = function(url) {
			alertService.alerts = $scope.alerts2;
			$http({
				'method' : 'post',
				'url'	 : url+"?form=2",
				'data'   : $.param($scope.formData2),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data.result;
				var len = result.length;
				for (var i=0; i < len; i++) {
					if (result[i].status == 'ok') {
						alertService.add('success', result[i].msg);
					} else if (result[i]['status'] == 'error') {
	            		alertService.add('danger', result[i].msg);
					}
				}
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}



	function createGiftCodeController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.codes = [];
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.codes = data.codes;
				$scope.code_name = data.code_name;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function searchGiftCodeController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.codes = [];
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.codes = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function addWordFilterController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.words = [];
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.words=data.words;
				$scope.t=0;
				alertService.add('success', 'OK');
			}).error(function(data) {
				alertService.add('danger', JSON.stringify(data));
			});
		};
	}

	function modalReplyCtroller($scope, $modalInstance, gm, $http, alertService) {
		$scope.gm = gm;
		$scope.gmData = {};
			
		$scope.cancel = function () {
			$modalInstance.dismiss('cancel');
		};

		$scope.replyFrom = function(url) {
			gm.reply_message = $scope.gmData.reply_message;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.gmData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				//alertService.add('success', data.result);
				$modalInstance.close(gm);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function loadGMController($scope, $http, alertService, $modal)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.questions = [];
		$scope.reply_dones= [];
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.questions = data;
				$scope.reply_dones=data.reply_done;
				/*if (!data.GM_Logs) {
					alertService.add('danger', JSON.stringify(data));
				}*/
				// alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger',  data.error);
			});
		};
		/*add function myDate*/
		$scope.myDate = function(timestamp) {
			return timestamp*1000;
		};
		$scope.done = function(gm) {
			alertService.alerts = $scope.alerts;
			var params = {
				'ser_id':gm.ser_id, 
				'server_gm_id' : gm.GMID,
				'player_id' : gm.PlayerID,
				'type' : gm.GMType,
				'reply_message' : ''
			};
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/gm/reply',
				'data'   : $.param(params),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				gm.IsDone = 1;
				// alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', JSON.stringify(data));
			});
		};

		$scope.reply = function(gm) {
			var modalInstance = $modal.open({
					templateUrl: 'replyModalContent.html',
					controller: modalReplyCtroller,
					resolve: {
						gm : function () {
							return gm;
						}
					},
					backdrop : false,
					keyboard : false
				});
			modalInstance.result.then(function(gm) {
				var i = 0;
				var len = $scope.questions.length;
				for (i; i < len; i++) {
					if ($scope.questions[i].GMID === gm.GMID) {
						$scope.questions[i].IsDone = 1;
					}
				}
			});
		};
	}


	function getPlatformUserController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.user = {};
		$scope.created_players = [];
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.user = data.user_basic;
				$scope.created_players = data.created_players;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
	function SXDGetPlatformUserController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.user = {};
		$scope.created_players = [];
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.user = data.user_basic;
				$scope.created_players = data.created_players;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function updatePlatformUserPasswordController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.res);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function createAdSourceController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg, 2000);
				$scope.formData.game = 0;
				$scope.formData.source_name = '';
				$scope.formData.source_value = '';
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
	}
	
	function createAdTermController1($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg, 2000);
				/*$scope.formData.game = 0;
				$scope.formData.source_name = '';
				$scope.formData.source_value = '';*/
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
	}
	

	function uploadController($scope, $modalInstance, folder, filename, filetype, $http, alertService) {
	    $scope.cancel = function() {
	        $modalInstance.dismiss('cancel');
	    }

	    $scope.upload = function(url) {
			url = url + '?folder=' + folder + '&filename=' + filename + '&filetype=' + filetype;//folder为用于保存文件的路径，filename为上传文件的名称,filetype为保存文件的后缀
	        $.ajaxFileUpload
	        (
	            {
	                url: url, //用于文件上传的服务器端请求地址
	                secureuri: false, //是否需要安全协议，一般设置为false
	                fileElementId: 'file_upload', //文件上传域的ID
	                dataType: 'json', //返回值类型 一般设置为json
	                success: function (data, status)  //服务器成功响应处理函数
	                {
	                    if (data.error != "上传文件成功!") {
	                        alert(data.error);
	                    } else {
	                        alert(data.error);
	                        $scope.cancel();
	                    }
	                },
	                error: function (data, status, e)//服务器响应失败处理函数
	                {
	                    alert(data.error);
	                }
	            }
	        )
	        $("#file_upload").empty();
	    }
	}

	
	function myrefresh(){
	   window.location.reload();
	}
//}());