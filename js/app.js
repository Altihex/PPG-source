/* application js file */
'use strict';
/* App module */

var ppgApp = angular.module('ppgApp', [
               'ngRoute',
               'ppgControllers',
               'ngAnimate',
               'ui.bootstrap',
               'ppgDevServices',
               'ngCookies'
             ])
    .run(function($rootScope) {
    	$rootScope.loginTry = 0;
    	$rootScope.submissionMessage;

    });
ppgApp.config(['$routeProvider', '$locationProvider', 
   function($routeProvider, $locationProvider){
		    $routeProvider.    
		    	when('/', {
					templateUrl: 'partials/main.html',      
					controller: 'MainCtrl' 
				}).
				when('/main', {
					templateUrl: 'partials/mainLI.html',      
					controller: 'MainCtrlLI' 
				}).
				when('/login', {      
					templateUrl: 'partials/login.html',      
			    	controller: 'LoginCtrl'  
				}).
				when('/newAccount', {
					templateUrl: 'partials/newAccount.html',      
					controller: 'LoginCtrl' 
				}).
				when('/logout', {
					templateUrl: 'partials/logout.html',      
					controller: 'LoginCtrl' 
				}).
				when('/getProjList', {
					templateUrl: 'partials/getProjList.html',      
					controller: 'ProjCtrl' 
				}).
				when('/mainProj/:projId', {
					templateUrl: 'partials/mainProj.html',      
					controller: 'ProjCtrl' 
				}).
				when('/newProj', {
					templateUrl: 'partials/getProjName.html',      
					controller: 'ProjCtrl' 
				}).
				when('/updateCode', {
					templateUrl: 'partials/updateCode.html',      
					controller: 'DevCtrl' 
				}).
				when('/updateDB', {
					templateUrl: 'partials/updateDB.html',      
					controller: 'DevCtrl' 
				});
		    $locationProvider.html5Mode(false).hashPrefix('!'); 
	}]);

	
