var ppgControllers = 
	angular.module('ppgControllers', []);

	ppgControllers.controller('MainCtrl', ['$scope','$rootScope','$cookies',
	   '$window','$http','Status','$location',function MainCtrl($scope,$rootScope,$cookies,
	 $window,$http,Status,$location) {  
			$scope.showHeader = true; 			
			console.log("running MainCtrl");
			sess={};
			$scope.init = function() {
				Status.get({},
					function success(response){
						sess = response;
							console.log("response is this ");
							console.log(response);
							if(sess.loggedIn) {
								$location.path('/main');								
							} else {
								$scope.showHeader = true;
							}
					},    
					function error(errorResponse) {
						//error code here - if needed 
					});
		
			};
			console.log("sess = ");
			console.log(sess);
	}]);
	ppgControllers.controller('MainCtrlLI', ['$scope','$rootScope','$window','$http','$location','Status',
	   function MainCtrl($scope,$rootScope,$window,$http,$location,Status) {  
	                               			
		$scope.showHeader = true; 
	                               			
		$scope.init = function() {
			Status.get({}, function success(response){
					sess = response;
						if(sess.loggedIn) {
							$scope.showUserName = true;
		                    $scope.showHeader = false;
		                    $scope.loginUser = sess.loginName;
						} else {
							$scope.showHeader = true;
							$location.path('/');
						}
				},    
				function error(errorResponse) {
					//error code here - if needed 
					$scope.submissionMessage = "Initial load failed";
					$scope.showUserName = false;
				});
		};
	}]);
	
	ppgControllers.controller('LoginCtrl', ['$scope','$rootScope','$window','$http','$location','$cookies',
	    function LoginCtrl($scope,$rootScope,$window,$http,$location,$cookies) {  
			$scope.showHeader = true;   
			$scope.auth = {
	        		email: "",
	        		pass: "",
	        		pass1:"",
	        		persist:false
	    		};
			
			$scope.init = function() {
				Status.get({}, function success(response){
						sess = response;
						console.log(sess);
							if(sess.loggedIn) {
								$scope.showUserName = true;
			                    $scope.showHeader = false;	
			                    $scope.loginUser = sess.loginName;
							} else {
								$scope.showHeader = true;
								$location.path('/');
							}
					},    
					function error(errorResponse) {
						//error code here - if needed 
						$scope.submissionMessage = "Initial load failed";
						$scope.showUserName = false;
					});
			};
			
			$scope.newAccount = function() {
        		$erMessage = "Please enter a password with at least eight characters";
				if(/\S+@\S+\.\S+/.test($scope.auth.email)){
					if(!$scope.auth.pass1) {
						$scope.submissionMessage = $erMessage;
					} else {
						if($scope.auth.pass1.length < 8) {
							$scope.submissionMessage = $erMessage;
						} else {
							if( $scope.auth.pass1 === $scope.auth.pass2) {
								$http.post('/php/new_account.php',{email:$scope.auth.email,pass:$scope.auth.pass1,handle:$scope.auth.name}).
									success(function(data,status,headers,config) {
										if(data === "cool") {
											$scope.submissionMessage = "We will send you an e-mail shortly, please click the link in the e-mail to validate your account";
											$location.path('/newAccountSplash');
										} else {
											$scope.submissionMessage = data;
										};
									}).
									error(function(data,status,headers,config) {
										$scope.submissionMessage = "New Account Request failed - Please try again";
									});
									
							} else {
								$scope.submissionMessage = "Passwords don't match, please try again";
								$scope.auth.pass2 = "";
							}
						}
					} 	
				} 
				else {
					$scope.submissionMessage = "Please enter a valid e-mail Address";
				}
    		};
			
			
			$scope.checkLogin = function() {
				$http.post('/php/login.php', {email:$scope.auth.email,pass:$scope.auth.pass}).
					success(function(data, status, headers, config) {
					    $scope.showHeader = false;
					    $rtnObj = data;
					    $scope.loginUser = $rtnObj.loginName;
						if($rtnObj.loggedIn) {
							var wm = "Welcome ";
							var mess = wm.concat($scope.loginUser); 
							$scope.submissionMessage = mess;
							$scope.showUserName = true;
							$scope.loginTry = 0;
							var expireDate = new Date();
							expireDate.setDate(expireDate.getDate() + 10);
							if($scope.auth.persist){
								$cookies.put('email',btoa($scope.auth.email),{'expires': expireDate});
								$cookies.put('pass',btoa($scope.auth.pass),{'expires': expireDate} );
							} else {
								$cookies.put('email',btoa($scope.auth.email));
								$cookies.put('pass',btoa($scope.auth.pass));
							}
							$location.path('/main');
						} 
						else {
							$scope.showUserName = false;
							$scope.loginTry++;
							if($scope.loginTry == 5) {
								$location.path('/newAccount');
							}
							$scope.submissionMessage = "Login Failed - 1 of " + $scope.loginTry;
							
						}     
					}
				);	
    		};
    		
    		$scope.cancelLogin = function() {
    			$location.path('/');
			};	
			
			$scope.endLogin = function () {
				$cookies.remove('email');
				$cookies.remove('pass');
				$cookies.remove('PHPSESSID');
				$location.path('/');
			}
	    }]);
	
	ppgControllers.controller('ProjCtrl', ['$scope','$rootScope','$cookies',
	    '$window','$http','Status','$routeParams','$location','$log',function ProjCtrl($scope,$rootScope,$cookies,
	    $window,$http,Status,$routeParams,$location,$log) {  	
		
		sess = {loggedIn: false};
		$scope.updArray = [];
		$scope.updArray[0] = [];
		$scope.updArray[1] = [];
		
		$scope.getVal = function(objIn,num) {
			iV = 1;
			for(val in objIn) {
				if(num === iV) {
					return objIn[val];
				}
				iV++;	
			}
		};
		
		$scope.parseDate = function (dInput) {
			if(typeof dInput == 'object') {
				return dInput;
			}
			if(typeof dInput == 'number') {
				nDate = new Date(dInput);
			} 
			if(typeof dInput == 'string') {
				//console.log("In string bit dInput = " + dInput);
				var chk = dInput[2];
				if(chk == '-') {
					parts = dInput.split('-');
					nDate = new Date(parts[2], parts[1]-1, parts[0]); // Note: months are 0-based	
				} else {
					nDate = new Date(dInput);
				}
			}
			//console.log("parseDate is returning - " + nDate);
			return nDate.getTime();			  
		};

		
		$scope.getDiffDate = function(date1,date2,startDateFlag) {
			//console.log("in getDiffDate");
			if(date1 == null || date2 == null) {
				console.log("one of the input dates are null - Returning");
				return;
			} 
			//console.log("input date1 = " + date1);
			//console.log("input date2 = " + date2);
			date1 = $scope.parseDate(date1);
			date2 = $scope.parseDate(date2);
			//console.log("input date1 after parseDate = " + date1);
			//console.log("input date2 after parseDate = " + date2);
			
			if(startDateFlag == 'l') {
				//console.log("startDateFlag is l");
				mday = new Date(date1);
				date1 = date1 - (mday.getDate() * $scope.cDay);
			}

			diff_ms = date2 - date1;
			rtn = Math.round(diff_ms/$scope.cDay);
			//console.log("Returning - " + rtn);
			return rtn;
		};

		
		
		//gannt type functions
		
		// This one does the position math for Gannt objects
		$scope.setGannt = function(type,row) {
			//console.log("in setGannt with type = " + type + " and row = " + row);
			rtn = 0;
			
			switch(type) {

				case "rect1w":
				case "rect2x": 
				case "c1cx": {
					console.log("calling getDiffDate from setGannt");
					rtn=$scope.getDiffDate($scope.mainData.headerArray.screen.projStartDate,$scope.mainData.cells[row][4],'l');
					rtn = rtn * $scope.mainData.headerArray.screen.colWidth;
					console.log(" in rect1w - colWidth = " + $scope.mainData.headerArray.screen.colWidth + " final offset = " + rtn);
					break;
				}
				case "rect2w": {
					rtn = parseInt($scope.mainData.cells[row][2],10);
					if(!rtn){
						rtn = 0;
					}
					rtn = rtn * $scope.mainData.headerArray.screen.colWidth;
					//console.log(" in rect2w - colWidth = " + $scope.mainData.headerArray.screen.colWidth + " final offset = " + rtn);
			   		break;
				}
				case "rect3x":
				case "pcx":  {
					rtn = $scope.setGannt('rect2w',row) + $scope.setGannt('rect1w',row);
					//console.log(" in rect3x - colWidth = " + $scope.mainData.headerArray.screen.colWidth + " final offset = " + rtn);
					break;
				}
				case "rect3w": {
					rtn=$scope.getDiffDate($scope.mainData.cells[row][5],$scope.mainData.headerArray.screen.projEndDate,'r') * $scope.mainData.headerArray.screen.colWidth;
					//console.log(" in rect3w - colWidth = " + $scope.mainData.headerArray.screen.colWidth + " final offset = " + rtn);
					break;
				}
	   		
			}
			return rtn;


		};
		
		$scope.pushHeaderArray = function(value) {
			$scope.headerArray.push(value);
			//console.log($scope.headerArray);
		};
		
		$scope.init = function() {
			Status.get({}, function success(response){
					sess = response;
					console.log(sess);
						if(sess.loggedIn) {
							$scope.showUserName = true;
		                    $scope.showHeader = false;	
		                    $scope.loginUser = sess.loginName;
						} else {
							$scope.showHeader = true;
							$location.path('/');
						}
				},    
				function error(errorResponse) {
					//error code here - if needed 
					$scope.submissionMessage = "Initial load failed";
					$scope.showUserName = false;
				});
		};
		
		$scope.getProjList = function() {
			main = {action:"getProjectList"};
			$http.post('/php/main.php',main).
			success(function(data,status,headers,config) {
				$scope.projList = data;
			}).
			error(function(data,status,headers,config) {
				$scope.submissionMessage = "No projects saved";
			});
		};
		
		$scope.loadMain = function(projId,event) {
			$location.path('/mainProj/' + projId );
		};
		
		$scope.loadProject = function() {
			console.log("in loadProject");
			projId = $routeParams.projId;
			console.log("projId = " + projId);
			//$scope.updArray[1].startDate = $scope.projList.cells[row][2];
			//$scope.updArray[1].endDate 	 = $scope.projList.cells[row][3];
			main = {action:"loadCurrent",
					winHeight:window.innerHeight,
					winWidth:innerWidth,
					projId:projId};
					//name:$scope.projList.cells[row][1],
					//sdate:$scope.updArray[1].startDate,
					//date:$scope.updArray[1].endDate};
			
			$http.post('/php/main.php',main).
			success(function(data,status,headers,config) {
				//$location.path('/mainProj');
				mainObj = data;
				$scope.mainHeader = mainObj.headerObj;
				$scope.mainData = mainObj.dataObj;
				console.log($scope.mainData);
				$scope.submissionMessage = "Project load succeded";
			}).
			error(function(data,status,headers,config) {
				$scope.auth.submissionMessage = "Project load failed";
				$scope.showMain = false;
			});
		};
		
		
		//Call create new project
		$scope.loadNew = function() {
			main = {action:"new",name:$scope.projName,winHeight:window.innerHeight,winWidth:innerWidth};
			$http.post('/php/main.php', main).
			success(function(data, status, headers, config) {
				mainObj = data;
				$scope.mainHeader = mainObj.headerObj;
				$scope.mainData = mainObj.dataObj;
				//console.log($scope.mainData.headerArray);
				//$scope.mainHeaderHtml = $sce.trustAsHtml($scope.mainData.headerHtml);
				$scope.submissionMessage = "New project creation succeded";
			}).
			error(function(data,status,headers,config) {
				$scope.submissionMessage = "New project creation failed";
			});

		};
		
		$scope.cancelProj = function() {
			$location.path('/');
		};	
	                                		
	}]);
	
	ppgControllers.controller('DevCtrl', ['$scope','PullCodeUpdate','$window','$http','$location','$rootScope',
       function DevCtrl($scope,PullCodeUpdate,$window,$http,$location,$rootScope) {  
		
		
		$scope.updateCode = function () {	
			PullCodeUpdate.get({},
			function success(response){         
				$scope.submissionMessage = "Updated Code Sucessfully";
				console.log("Updated code!");
				$window.history.back();
			},    
			function error(errorResponse) {       
				$scope.submissionMessage = "Code Update failed";
				console.log("Code update FAILED!");
			}); 
		};
		
		$scope.resetDB = function() {
			$http.get('/php/deploy.php').
			  success(function(data, status, headers, config) {
				  $scope.submissionMessage = "LOADED NEW SCHEMA";
				  $window.history.back();
			  }).
			  error(function(data, status, headers, config) {
				  $scope.submissionMessage = "Schema load FAILED";
				  $window.history.back();
			  });
		};
		
		
	   }]);
	