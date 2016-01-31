/* services.js *
 * 
 */

	'use strict'; /* Services */
	var ppgDevServices =  angular.module('ppgDevServices', ['ngResource','ngCookies']); 
	
		ppgDevServices.factory('PullCodeUpdate', ['$resource', function($resource) {
			return $resource("http://192.168.56.10/php/update.php", {}, {  
				get: {method: 'GET', cache: false, isArray: false}  
			});
		}]);
		
		ppgDevServices.factory('Status', ['$resource','$cookies','$http', function($resource,$cookies,$http) {
			var c_email = $cookies.get('email');
			if(c_email) { c_email = atob(c_email)};
			var c_pass = $cookies.get('pass');
			if(c_pass) { c_pass = atob(c_pass)};
			if((c_email)&&(c_pass)){ 
				console.log("Calling login.php with c_email = " + c_email + " and c_pass = " + c_pass);
				var crd = { email: c_email,
					  pass: c_pass};
				$http.post('/php/login.php', crd).
					success(function(data, status, headers, config) {
						console.log('logged in ' + data.loginName);
						console.log("calling status.php now");
						//return data;
					}).
					error(function(data,status,headers,config) {
						console.log("Status went pete tong");
						//return;
					});
			};	
			console.log("calling status.php now");
			return $resource("http://192.168.56.10/php/status.php", {}, {  
			get: {method: 'GET', cache: false, isArray: false}  
			});
		}]);
		
		
		
		
	//var ppgDevServices = angular.module('ppgDevServices',['$http']);

	//ppgDevServices.service('PullSourceCode',['$http', function($http) {
	//	this.pullSourceCode = function() {
	//		return $http.get('/php/update.php');
	//	};
		
	//}]);
		

		
		
    