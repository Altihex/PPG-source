<!DOCTYPE html>                                                                         
<html lang="en" ng-app="myApp" id="top" >
<!-- html lang="en"-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=4000, initial-scale=1">
	<meta http-equiv="Expires" content="Tue,01 Jan 1995 12:12:12 GMT">
	<meta http-equiv="Pragma" content="no-cache">
	<title>Plan Print Go - Test Mule</title>
	<script src='/angular.js'></script>
	<script src='/ui-bootstrap-tpls-0.14.3.js'></script>
	<script src='/angular-animate.js'></script>
	<script src='/moment.js'></script>
	<link type="text/css" rel="stylesheet" href="normalize.css"/>
	<link rel="stylesheet" type="text/css" href="/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/ppg-style-new.css"/>

	<!--link rel="stylesheet" type="text/css" href="/skeleton.css"/-->
</head>
<body>
	<div ng-controller="AuthController" ng-init="init()" >
    	<div id="header" ng-show="showHeader">
			<h1>Plan Print Go</h1> 
		</div>
    	<div id="menu">		
			<div class="btn-group" uib-dropdown is-open="projectButton.isopen" ng-show="showUserName">
      			<button id="project-button" type="button" class="btn btn-primary" uib-dropdown-toggle ng-disabled="disabled">
        			Project <span class="caret"></span>
      			</button>
      			<ul class="uib-dropdown-menu" role="menu" aria-labelledby="project-button">
        			<li role="menuitem" ng-click="getProjList()"><a href="#projLoad">Load</a></li>
        			<li role="menuitem" ng-click="getProjName()"><a href="#projNew">New</a></li>
        			<li role="menuitem" ng-click="loadMain()" class="m-list"> Prev Version</li>
        			<li role="menuitem" ng-click="saveMain()"><a href="#projSave">Save</a></li>
        		</ul>
    		</div>	
			<div class="btn-group" uib-dropdown is-open="reportButton.isopen" ng-show="showUserName">
      			<button id="report-button" type="button" class="btn btn-primary" uib-dropdown-toggle ng-disabled="disabled">
        			Reports <span class="caret"></span>
      			</button>
      			<ul class="uib-dropdown-menu" role="menu" aria-labelledby="report-button">
        			<li role="menuitem" ng-click="getProjList()" class="m-list"> Load</li>
        			<li role="menuitem" ng-click="getProjName()" class="m-list"> New</li>
        			<li role="menuitem" ng-click="loadMain()" class="m-list"> Prev Version</li>
        			<li role="menuitem" ng-click="saveMain()" class="m-list"> Save</li>
        		</ul>
    		</div>	
    		<div class="btn-group" uib-dropdown is-open="resourceButton.isopen" ng-show="showUserName">
      			<button id="resource-button" type="button" class="btn btn-primary" uib-dropdown-toggle ng-disabled="disabled">
        			Resources <span class="caret"></span>
      			</button>
      			<ul class="uib-dropdown-menu" role="menu" aria-labelledby="resource-button">
        			<li role="menuitem" ng-click="getProjList()" class="m-list"> Load</li>
        			<li role="menuitem" ng-click="getProjName()" class="m-list"> New</li>
        			<li role="menuitem" ng-click="loadMain()" class="m-list"> Prev Version</li>
        			<li role="menuitem" ng-click="saveMain()" class="m-list"> Save</li>
        		</ul>
    		</div>	
			<span class="btn btn-success" ng-show="showUserName">Logged in as: {{loginUser}}</span>
			<div class="btn-group" uib-dropdown is-open="loginButton.isopen">
      			<button id="login-button" type="button" class="btn btn-primary" uib-dropdown-toggle ng-disabled="disabled">
        			Account <span class="caret"></span>
      			</button>
      			<ul class="uib-dropdown-menu" role="menu" aria-labelledby="login-button">
        			<li role="menuitem" ng-click="toggleLogin()"><a href="#login">Login</li>
        			<li role="menuitem" ng-click="toggleNewAccount()"><a href="#newAccount">New Account</li>
        			<li class="divider"></li>
        			<li role="menuitem"><a href="#logout">Log out</a></li>
      			</ul>
    		</div>
    		<div class="btn-group" uib-dropdown is-open="devButton.isopen">
      			<button id="dev-button" type="button" class="btn btn-danger" uib-dropdown-toggle ng-disabled="disabled">
        			Developer <span class="caret"></span>
      			</button>
      			<ul class="uib-dropdown-menu" role="menu" aria-labelledby="dev-button">
        			<li role="menuitem" ng-click="updateCode()" class="m-list">Update Code</li>
        			<li role="menuitem" ng-click="showTest()" class="m-list">Show Test Page</li>
        			<li class="divider"></li>
        			<li role="menuitem" ng-click="resetDB()" class="m-list" style="color:red" >RESET DB</li>
      			</ul>
    		</div>
		</div>
		<div ng-show="showNewAccount" class="eForm">
			<br>
			<table class="table">
				<tbody>
					<tr>
						<td>Your Email</td>
						<td><input type=text ng-model="auth.emailn" placeholder="Enter e-mail Address" > </td>
					</tr>
					<tr>
						<td>Your Name</td>
						<td><input type=text ng-model="auth.namen" placeholder="Enter User Name"> </td>
					</tr>
					<tr>
						<td>Password</td>
						<td><input type=password ng-model="auth.pass1" placeholder="Enter Password"> </td>
					</tr>
					<tr>
						<td>Confirm Password</td>
						<td><input type=password ng-model="auth.pass2" placeholder="Enter Password Again"> </td>
					</tr>
					<tr>
						<td colspan="2"><button class="btn btn-primary" ng-click="newAccount()">Ok</button>
						<button class="btn btn-default" ng-click="resetFlags()">Cancel</button></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div ng-show="showLogin" class="eForm" >
			<form>
			<table class="table">
				<tbody>
					<tr>
						<td>Email</td>
						<td><input type=text ng-model="auth.email" placeholder="Enter e-mail Address"> </td>
					</tr>
					<tr>
						<td>Password</td>
						<td><input type=password ng-model="auth.pass" placeholder="Enter Password"> </td>
					</tr>
					<tr>
						<td colspan="2"><button class="btn btn-primary" ng-click="checkLogin()">Ok</button>
						<button class="btn btn-default" ng-click="resetFlags()">Cancel</button></td>
					</tr>
				</tbody>
			</table>
			</form>
		</div>
		<div ng-show="showNewProject" class="eForm">
			<br>
			<table class="table">
				<thead>
					<tr>
						<td>Project Name</td>
						<td><input type=text ng-model="projName" placeholder="New Project Name"> </td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2"><button class="btn btn-primary" ng-click="loadNew()">Ok</button>
						<button class="btn btn-default" ng-click="resetFlags()">Cancel</button></td>
					</tr>
				</tbody>
			</table>
		</div>	
		
		<div id="topl" class="panel-1" ng-show="showMain" ng-init="tindex=0" >
			<form name="mainEntryForm">
				<table>
					<thead class="tbl-rbord">
						<tr><td colspan=7></td></tr>			
						<tr>
							<td colspan=7>
								<div class="btn-group" uib-dropdown is-open="ActionButton.isopen" ng-show="showUserName">
      								<button id="action-button" type="button" class="" uib-dropdown-toggle ng-disabled="disabled">
        								Actions <span class="caret"></span>
      								</button>
      								<ul class="uib-dropdown-menu" role="menu" aria-labelledby="action-button">
        								<li role="menuitem" ng-click="depAction('link')" class="m-list"> LINK</li>
        								<li role="menuitem" ng-click="depAction('delink')" class="m-list">Delete Link</li>
        								
        							</ul>
    							</div>	
							</td>
						</tr>
						<tr>
							<th ng-repeat="lH in mainData.headerArray.leftHeaders|orderBy:'-leftHeaders'" style="width:{{getVal(lH,1)}}px" class="tbl-under">
								<div ng-repeat="(key,value) in lH" {{pushHeaderArray(value)}}>
									{{key}}
								</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="row in mainData.rows" ng-class-even="'even-row'" ng-class-odd="'odd-row'" >
							<td ng-repeat="column in mainHeader.cols" >
								<div>
									<!--  Display non updateables -->
									<div ng-if="mainData.style.updateable[column]">
										<div ng-if="column == 0 && mainData.cells[row][column] > 0" >
											<input type="checkbox" name="selectRow" ng-model="mainData.deps[row]['status']" />
											{{mainData.cells[row][column]}}									
										</div>
										
										<div ng-if="!mainData.style.date[column]" ng-style="{width: getVal(mainData.headerArray.leftHeaders[column],1) + 'px'}">
											<div ng-if="!column==0">
												{{mainData.cells[row][column]}}
											</div>
										</div>
									</div>
									<!--  Display Dates -->
									<div ng-if="mainData.style.date[column]" >
										<div>
       										<input 
              									type="text" 
              									id="mt-{{(row*8)+column}}" 
              									name="mt{{(row*8)+column}}" 
              									tabindex="{{(row*8)+column}}" 
              									ng-init="setOldValue('empty')" 
									   			ng-focus="setOldValue(parseDate(mainData.cells[row][column]))" 
									   			ng-blur="updateCell(parseDate(oldValue),row,column,$event)" 
												ng-style="{width: getVal(mainData.headerArray.leftHeaders[column],1) + 'px'}"
              									uib-datepicker-popup="dd-MM-yyyy"
              									datepicker-options="dateOptions"
              									ng-model="mainData.cells[row][column]"
              									is-open="mainData.status[row][column]" 
              									min-date="getMinDate($event,row,column)" 
              									max-date="maxDate" 
              									starting-day="1" 
              									date-disabled="null" 
              									ng-required="true" 
              									close-text="Close" 
              									ng-click="openDt($event,row,column)"
              								/>
            								
        								</div>
									</div>
              						<!-- Display regular values (not dates) -->
              						<div ng-if="!mainData.style.updateable[column]">
										<div ng-if="!mainData.style.date[column]">
											<input 	type="text" 
												id="mt-{{(row*8)+column}}" 
												name="mt{{(row*8)+column}}" 
									   			tabindex="{{(row*8)+column}}" 
									   			ng-init="oldValue=''" 
									   			ng-focus="oldValue=mainData.cells[row][column]" 
									   			ng-blur="updateCell(oldValue,row,column,$event)" 
									   			ng-model="mainData.cells[row][column]"
									   			ng-style="{width: getVal(mainData.headerArray.leftHeaders[column],1) + 'px'}"
									   			/> 
										</div>
									</div>
								</div>
							</td>
						<tr>
					</tbody>
				</table>
			</form>
		</div>
		<div id="topr" class="panel-2" ng-show="showMain">	
			<table>
				<thead>
					<tr>
						<th ng-repeat="iY in mainData.headerArray.years |orderBy:'-years'" colspan="{{getVal(iY,1)}}" class="tbl-under tbl-rbord" >
							<div ng-repeat="(key,value) in iY" style="text-align:center" >
								{{ key }}
							</div>
						</th>
					</tr>
					<tr>
						<th ng-repeat="iQ in mainData.headerArray.quarters|orderBy:'-quarters'" colspan="{{getVal(iQ,1)}}" class="tbl-under tbl-rbord" >
							<div ng-repeat="(key,value) in iQ" style="text-align:center">
								{{ key }}
							</div>										
						</th>
					</tr>
					<tr>
						<th ng-repeat="iM in mainData.headerArray.months |orderBy:'-months'" class="tbl-under tbl-rbord"> 
							<div ng-repeat="(key,value) in iM" ng-style="{width: value * mainData.headerArray.screen.colWidth + 'px'}" style="text-indent:5%" >
									{{ key }}
							</div>										
						</th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="row in mainData.rows" ng-class-even="'even-row'" ng-class-odd="'odd-row'" >
                 		<td colspan="50" >
                       		<div ng-if="mainData.cells[row] != null" ng-class="'fix-row-h'">
                            	<svg width="100%" height="100%">
                           			<rect
                               			y="2"
                                       	x="0"
                                    	ng-attr-width="{{setGannt('rect1w',row)}}"
                                     	height="26px"
                                   		stroke="black"
                                       	stroke-width="1"
                                   		fill="white"/>
                                	<rect
                                    	y="2"
                                      	ry="2"
                                      	rx="2"
                                      	ng-attr-x="{{setGannt('rect2x',row)}}"
                                      	ng-attr-width="{{setGannt('rect2w',row)}}"
                                      	height="26px"
                                 		stroke="black"
                                      	stroke-width="2"
                                      	fill="blue"/>
                                  	<line ng-if="mainData.deps[row].deps.length  > 0"
                                 		x1="0"
                                    	y1="0"
                                    	x2="0"
                                     	y2="0"
                                    	style="stroke:rgb(255,0,0);stroke-width:2"
                                      	stroke="red"
                                     	stroke-width="2" />
                             		<circle ng-if="mainData.deps[row].deps.length  > 0"
                                    	ng-attr-cx="{{setGannt('c1cx',row)}}"
                                      	text="1"
                                      	cy="15"
                                     	r="7"
                                       	stroke="black"
                                     	stroke-width="1"
                                   		fill="red" />
                        			<rect
                                		y="2"
                                     	ng-attr-x="{{setGannt('rect3x',row)}}"
                                    	ng-attr-width="{{setGannt('rect3w',row)}}"
                                  		height="26px"
                                      	stroke="black"
                                     	stroke-width="1"
										fill="white"/>
                           			<line ng-if="mainData.deps[row].preds.length  > 0"
                               			ng-attr-x1="{{setGannt('pcx',row)}}"
                                 		y1="15"
                                     	ng-attr-x2="{{setGannt('pcx',row)}}"
                                     	y2="30"
                                     	stroke="red"
                                   		stroke-width="2" />
                             		<circle ng-if="mainData.deps[row].preds.length  > 0"
                               			text="1"
                                    	ng-attr-cx="{{setGannt('pcx',row)}}"
                                     	cy="15"
                                  		r="7"
                                  		stroke="black"
                                    	stroke-width="1"
                                   		fill="green" />
                   				</svg>
               				</div>
                  		</td>
                   	</tr>
				</tbody>
			</table>
		</div>
		<div ng-show="showMain"> 
			<svg width="100%" height="100%" version="1.1" xmlns="http://www.w3.org/2000/svg">
				{{createGannt()}}
			</svg>
		</div>		
		<div id="projects" ng-show="showProjects" class="eForm" >
			<table class="table" >
				<thead>
					<tr > 
						<th style="width:60px">ID</th>
						<th style="width:200px">Project Name</th>
						<th style="width:120px">Start Date</th>
						<th style="width:120px">Finish Date</th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="row in projList.rows" ng-class-even="'even-row'" ng-class-odd="'odd-row'"> 
						<td ng-repeat="column in projList.cols">
								<input type="text" class="form-control"
								ng-readonly="false" 
								ng-dblclick="loadProject(row,column,$event)" 
								ng-model="projList.cells[row][column]"/> 
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="test-main" ng-show="showTestScreen" >
			testing
    	</div>
		<div id="status-bar">
			{{auth.submissionMessage}}<br>
		</div>
	</div>
	
	<script type="text/javascript">
		angular.module('myApp', ['ngAnimate','ui.bootstrap']);
		angular.module('myApp').controller('AuthController', function($scope, $log, $http, $document,$sce,$filter) {

			//Set Default Values 
			$scope.showHeader = true;
			$scope.cDay = 86400000;
			$scope.updCount = 0;
			$scope.updArray = [];
			$scope.updArray[0] = [];
			$scope.updArray[1] = [];
			$scope.auth = {
        		email: "",
        		emailn: "",
        		pass: "",
        		pass1: "",
        		pass2: "",
        		submissionMessage: ""
    		};
			$scope.cMaxrow = 29;
			$scope.cNumcol = 8;
			$scope.cPagesize = 10*$scope.cNumcol;
			$scope.cHomeid= 1;
			$scope.cMaxcell = ($scope.cMaxrow*$scope.cNumcol)+$scope.cNumcol;
			$scope.date = {
					opened: false
			}

			$scope.dateOptions = {
				formatYear:'yyyy'

			};
			$scope.svgRow=0;

			$scope.setOldValue = function(value) {

				if(typeof(value) == "undefined") {
					rtn = "";
					//console.log("setOldValue returning " + rtn);
					return rtn;
				}

				//console.log("setOldValue got this = " + value);
				value = new Date(value);
				//console.log("Going to try and return this = " + value);
				return value;
			}
			
			$scope.depAction = function(action) {

				var updList = [];
				var idx = 0;
				
				//create update list 
				for(i=0;i<$scope.mainData.deps.length;i++){
					if($scope.mainData.deps[i]['status'] == true ){
						updList[idx] = i+1;
						idx++;
					}
				}	 
				// nothing selected - Just go back
				if(idx == 0) {
					return;
				}
				
				// turn the list into an object
				var updObj = {
					action:	action.toLowerCase(),
					value:	updList
				};
			
				// create update records - send all records, even the existing,
				// sort it out on the server side
				tArray = {
					row: 	updList[i],
					column:	0,
					value:	updObj,
					recid:	$scope.mainData.cells[updList[0]][0],
				};
				$scope.updArray[0].push(tArray);
				$scope.updCount++;
					
				//reset all the ticks
				console.log("resetting selections");
				for(i=0;i<$scope.mainData.deps.length;i++) {
					$scope.mainData.deps[i]['status'] = false;
				}
			}		


			
			$scope.getMinDate = function($event,row,column) {
				if(column === 5 && $scope.mainData.cells[row]!= null) {
					return $scope.parseDate($scope.mainData.cells[row][4]);
				}
				return;
			}
			
			$scope.openDt = function($event,row,column) {
			    $scope.mainData.status[row][column]= true;
			 };

			$scope.createArray = function(length) {
			    var arr = new Array(length || 0),
			        i = length;
			    if (arguments.length > 1) {
			        var args = Array.prototype.slice.call(arguments, 1);
			        while(i--) arr[length-1 - i] = $scope.createArray.apply(this, args);
			    }
			    return arr;
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
			}

			//gannt type functions
			
			// This one does the position math for Gannt objects
			$scope.setGannt = function(type,row) {
				//console.log("in setGannt with type = " + type + " and row = " + row);
				rtn = 0;
				
				switch(type) {

					case "rect1w":
					case "rect2x": 
					case "c1cx": {
						rtn=$scope.getDiffDate($scope.mainData.headerArray.screen.projStartDate,$scope.mainData.cells[row][4],'l');
						rtn = rtn * $scope.mainData.headerArray.screen.colWidth;
						//console.log(" in rect1w - colWidth = " + $scope.mainData.headerArray.screen.colWidth + " final offset = " + rtn);
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


			}
			
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
			}

			$scope.getVal = function(objIn,num) {
				iV = 1;
				for(val in objIn) {
					if(num === iV) {
						return objIn[val];
					}
					iV++;	
				}
			}

			$scope.keypressed = function(event,row,column) {
				return;
				if(event.which > 64 ) { return; }
				//console.log("Keypressed fired!");
				//console.log(event);
				//console.log("keypressed should be = " + event.keyIdentifier);
				//console.log("Key wich code = " + event.which);
				mtId = (row*$scope.cNumcol)+column;
				//console.log("mtId = "+mtId);
				switch(event.keyIdentifier) {
					case "Right":
					case "Enter": {
						mtId++;
						break;
					}
					case "Left": {
						mtId--;
						break;
					}
					case "Up" : {
						mtId=mtId-$scope.cNumcol;
						break;
					}
					case "Down": {
						mtId=mtId+$scope.cNumcol;
						break;
					}
					case "PageUp": {
						mtId=mtId-$scope.cPagesize;
						if(mtId<$scope.cHomeid) {
							mtId = column;
						}
						break;
					}
					case "PageDown": {
						mtId=mtId+$scope.cPagesize;
						if(mtId>$scope.cMaxcell) {
							mtId=($scope.cMaxrow*$scope.cNumcol)+column;
						}
						break;
					}
					case "Home" : {
						mtId=$scope.cHomeid;
						break;
					}
					case "End" : {
						mtId=$scope.cMaxcell;
						break;
					}
				}
				if((mtId < $scope.cMaxcell) && (mtId >= $scope.cHomeid)) {
					mtStr = ("#mt-" + mtId);
					document.querySelector(mtStr).scrollIntoView();
					document.querySelector(mtStr).focus();
				}
			};
			

			// on page load, get key session object values
			$scope.init = function() {
				$http.get('/status.php').
					success(function(data, status, headers, config) {
						sess = data;
						if(sess.loggedIn) {
							$scope.showUserName = true;
							$scope.showHeader = false;
							$scope.loginUser = sess.loginName;
						} else {
							$scope.showUsername = false;
						}
					}).
					error(function(data,status,headers,config) {
						$scope.auth.submissionMessage = "Initial load failed";
						$scope.loginUser = "failed";
						$scope.showUserName = false;
						$scope.showMain = false;
					});	
			};
		
			
			//Page element control
    		// Reset all displays
		   	$scope.resetFlags = function() {
    			$scope.showLogin = false;
    			$scope.showNewAccount = false;
    			$scope.auth.submissionMessage = "";
    			$scope.showNewProject = false;
    			$scope.showProjects = false;
    			$scope.showMain = false;
    			$scope.showTestScreen = false;
		   	};
		   	//Show Login
    		$scope.toggleLogin = function() {
				$scope.resetFlags();        		
        		$scope.showLogin = !$scope.showLogin;
    		};
    		//Show new Account creation
    		$scope.toggleNewAccount = function() {
        		$scope.resetFlags();
        		$scope.showNewAccount = !$scope.showNewAccount;
    		};
    		// show new project Name
			$scope.getProjName = function () {
				$scope.resetFlags();
				$scope.showNewProject = true;
			};

			// show Test
			$scope.showTest = function () {
				$scope.resetFlags();
				$scope.showTestScreen = true;
			};


    		
    		//Login			
    		$scope.checkLogin = function() {
				$http.post('/login.php', {email:$scope.auth.email,pass:$scope.auth.pass}).
					success(function(data, status, headers, config) {
					    // this callback will be called asynchronously
					    // when the response is available
					    $scope.showMain = false;
					    $scope.showLogin = false;
					    $scope.showHeader = false;
					    $rtnObj = data;
					    $scope.loginUser = $rtnObj.loginName;
						if($rtnObj.loggedIn) {
							var wm = "Welcome ";
							var mess = wm.concat($scope.loginUser); 
							$scope.auth.submissionMessage = mess;
							$scope.showUserName = true;
						} 
						else {
							$scope.showUserName = false;
							$scope.auth.submissionMessage = "Login Failed";
						}     
					}
				);	
    		};
    		$scope.newAccount = function() {
        		$erMessage = "Please enter a password with at least eight characters";
				if(/\S+@\S+\.\S+/.test($scope.auth.emailn)){
					if(!$scope.auth.pass1) {
						$scope.auth.submissionMessage = $erMessage;
					} else {
						if($scope.auth.pass1.length < 8) {
							$scope.auth.submissionMessage = $erMessage;
						} else {
							if( $scope.auth.pass1 === $scope.auth.pass2) {
								$http.post('/new_account.php',{email:$scope.auth.emailn,pass:$scope.auth.pass1,handle:$scope.auth.namen}).
									success(function(data,status,headers,config) {
										if(data === "cool") {
											$scope.auth.submissionMessage = "We will send you an e-mail shortly, please click the link in the e-mail to validate your account";
											$scope.resetFlags();
										} else {
											$scope.auth.submissionMessage = data;
										};
									});
							} else {
								$scope.auth.submissionMessage = "Passwords don't match, please try again";
								$scope.auth.pass1 = "";
								$scope.auth.pass2 = "";
							}
						}
					} 	
				} 
				else {
					$scope.auth.submissionMessage = "Please enter a valid e-mail Address";
				}
    		};
    		//main object functions
    	
    		//Load last project
    		$scope.loadMain = function () {
    			$http.get('/status.php').
				success(function(data, status, headers, config) {
					sess = data;
					if(sess.loggedIn) {
						$scope.showUserName = true;
						$scope.loginUser = sess.loginName;
					} 
					else {
						$scope.showUsername = false;
					}
				}).
				error(function(data,status,headers,config) {
					$scope.auth.submissionMessage = "Initial load failed";
					$scope.loginUser = "failed";
					$scope.showUserName = false;
					$scope.showMain = false;
				});
    			main = {action:"init",winHeight:window.innerHeight,winWidth:innerWidth};
    			$http.post('/main.php', main).
					success(function(data, status, headers, config) {
			   			mainObj = data;
			   			$scope.mainHeader = mainObj.headerObj;
			   			$scope.mainHeaderHtml = $scope.mainHeader.headerHtml;
			   			$scope.mainData = mainObj.dataObj;
			   			$scope.showMain = true;
			   			mtStr = ("#mt-" + $scope.cHomeid);
		    			document.querySelector(mtStr).focus();		
					}).
					error(function(data,status,headers,config) {
						$scope.auth.submissionMessage = "Initial load failed";
						$scope.showMain = false;
					});
				
    		};
		
			//Call create new project
			$scope.loadNew = function() {
				main = {action:"new",name:$scope.projName,winHeight:window.innerHeight,winWidth:innerWidth};
				$http.post('/main.php', main).
				success(function(data, status, headers, config) {
					$scope.resetFlags();
   					mainObj = data;
   					$scope.mainHeader = mainObj.headerObj;
   					$scope.mainData = mainObj.dataObj;
   					//console.log($scope.mainData.headerArray);
   					//$scope.mainHeaderHtml = $sce.trustAsHtml($scope.mainData.headerHtml);
   					$scope.auth.submissionMessage = "New project creation succeded";
   					$scope.showMain = true;
				}).
				error(function(data,status,headers,config) {
					$scope.auth.submissionMessage = "New project creation failed";
					$scope.showMain = false;
				});

			};

			$scope.getProjList = function() {
				main = {action:"getProjectList"};
				$http.post('/main.php',main).
				success(function(data,status,headers,config) {
					$scope.resetFlags();
					$scope.projList = data;
					//console.log($scope.projList);
					$scope.showProjects = true;
				}).
				error(function(data,status,headers,config) {
					$scope.auth.submissionMessage = "No projects saved";
					$scope.showMain = false;
				});
			};
			
			$scope.loadProject = function(row,column,event) {
				$scope.updArray[1].startDate = $scope.projList.cells[row][2];
				$scope.updArray[1].endDate 	 = $scope.projList.cells[row][3];
				main = {action:"loadCurrent",
						winHeight:window.innerHeight,
						winWidth:innerWidth,
						projId:$scope.projList.cells[row][0],
						name:$scope.projList.cells[row][1],
						sdate:$scope.updArray[1].startDate,
						edate:$scope.updArray[1].endDate};
				
				$http.post('/main.php',main).
				success(function(data,status,headers,config) {
					$scope.resetFlags();
   					mainObj = data;
   					$scope.mainHeader = mainObj.headerObj;
   					$scope.mainData = mainObj.dataObj;
   					console.log($scope.mainData);
   					$scope.auth.submissionMessage = "Project load succeded";
   					$scope.showMain = true;
				}).
				error(function(data,status,headers,config) {
					$scope.auth.submissionMessage = "Project load failed";
					$scope.showMain = false;
				});
			};
			//test code for a dialog box
			$scope.updateProjectList = function(row,column,$event){
					//will re-write this with angular-prompt
					  prompt( "Are you beginning to see the possibilities?", "Yes" ).then(
			                    function( response ) {
			                        console.log( "Prompt accomplished with", response );
			                    },
			                    function() {
			                        console.log( "Prompt failed :(" );
			                    }
			                );
					//$scope.projList[row][1] = prompt("Project Name",$scope.projList[row][1]);	
			}	
			$scope.pushHeaderArray = function(value) {
				$scope.headerArray.push(value);
				//console.log($scope.headerArray);
			}
			
			//Update cell
			$scope.updateCell = function(oldValue,row,column,event) {
				console.log("calling update cell with - oldValue = " + oldValue + " row = " + row + " column = " + column + " event = " + event);
				console.log("event object = ");
				console.log(event);
				if(typeof(oldValue) == "undefined") {
					console.log("oldValue is undefined");
					return;
				}

				if(typeof(row) == "undefined") {
					console.log("row is undefined");
					return;
				}
				if(typeof(column) == "undefined") {
					console.log("column is undefined");
					return;
				}
				if( ! $scope.mainData.cells[row][column]){
					console.log("mainData.cells[row][column] is false!");
					return;
				}
				if(oldValue == $scope.mainData.cells[row][column]) { return;}
				switch(column) {
					case 4: 
					case 5: {
						val = $scope.parseDate($scope.mainData.cells[row][column]);
						break;
					}  
					case 2: {
						val = parseInt($scope.mainData.cells[row][column]);
						break;
					}
					default: {
						val = $scope.mainData.cells[row][column];
					}

				}
				console.log("val is = " + val + " and is a " + typeof(val));
				tArray = {
					row: 	row,
					column:	column,
					value: 	val,
					recId:	$scope.mainData.cells[row][0] 
				};
				console.log(tArray);
				$scope.updArray[0].push(tArray);
				$scope.updCount++;
				console.log("count =" + $scope.updCount);
				
				switch(column) {
				case 2:
				case 4: {
					// v1 - d1=moment($scope.mainData.cells[row][4],"DD-MM-YYYY");
					// v2 - d1=$scope.parseDate($scope.mainData.cells[row][4]);
					d1=$scope.parseDate($scope.mainData.cells[row][4]);
					//console.log("in updateCell - d1 = " + d1);
					//$scope.mainData.cells[row][5] = d1.add($scope.mainData.cells[row][2],'d').format('DD-MM-YYYY');
					$scope.mainData.cells[row][5] = d1 + ($scope.mainData.cells[row][2]*$scope.cDay);
					$scope.mainData.cells[row][5] = $scope.parseDate($scope.mainData.cells[row][5]);
					//console.log("To date now = " + $scope.mainData.cells[row][5]);
					fName = "mt".concat(((row*8)+5).toString());
					$scope.mainEntryForm[fName].$setDirty();
					tArray = {
							row: 	row,
							column:	5,
							value: 	$scope.parseDate($scope.mainData.cells[row][5]),
							recId:	$scope.mainData.cells[row][0] 
					};
					$scope.updArray[0].push(tArray);
					$scope.updCount++;
					break;
				}
				case 5:
					//d2=moment($scope.mainData.cells[row][5],"DD-MM-YYYY");
					d2= $scope.parseDate($scope.mainData.cells[row][5]);
					//console.log("case 5: before date sub - from date is = " + $scope.mainData.cells[row][4]);
					$scope.mainData.cells[row][4] = d2 - ($scope.mainData.cells[row][2]*$scope.cDay);
					//console.log("case 5: after date sub - from date is = " + $scope.mainData.cells[row][4]);
					fName = "mt";
					fName = fName.concat(((row*8)+4).toString());
					$scope.mainEntryForm[fName].$setDirty();
					tArray = {
							row: 	row,
							column:	4,
							value: 	$scope.parseDate($scope.mainData.cells[row][4]),
							recId:	$scope.mainData.cells[row][0] 
					};
					$scope.updArray[0].push(tArray);
					$scope.updCount++;
					break;
				}
				
				//console.log("updArray with data");
				//console.log($scope.updArray);
				// batching updates
				if($scope.updCount == 10){		
					$scope.saveMain();
				}

			};
			
			$scope.saveMain = function() {
				//console.log("sending updArray = ");
				console.log($scope.updArray);
				main = {action: "update",uA:$scope.updArray};
				$http.post('/main.php', main).
				success(function(data, status, headers, config) {
   					updateObj = data;
   					console.log("Returned data = " + data);
   					$scope.updCount =  0;
   					//$scope.updArray = [];
   					$scope.updArray.length = 0;
   					$scope.auth.submissionMessage = "Save Succeded";
   					$scope.mainEntryForm.$setPristine();
   			      	$scope.mainEntryForm.$setUntouched();
				}).
				error(function(data,status,headers,config) {
					$scope.auth.submissionMessage = "Save Failed";
				});
			};
			
    		
    		//Get Object
    		
    		
    		//Put Object
    		
    		
    		//DEVELOPER FUNCTIONS - NEED TO BE REMOVED
    		$scope.updateCode = function() {
        		$scope.resetFlags();
				$http.get('/update.php').
				  success(function(data, status, headers, config) {
				    // this callback will be called asynchronously
				    // when the response is available
					$scope.auth.submissionMessage = "UPDATED CODE";
				  }).
				  error(function(data, status, headers, config) {
				    // called asynchronously if an error occurs
				    // or server returns response with an error status.
					    
				  });
			};
	
			$scope.resetDB = function() {
				$scope.resetFlags();
				$http.get('/deploy.php').
				  success(function(data, status, headers, config) {
				    // this callback will be called asynchronously
				    // when the response is available
					//auth.submissionMessage = data;
					  $scope.auth.submissionMessage = "LOADED NEW SCHEMA";
				  }).
				  error(function(data, status, headers, config) {
				    // called asynchronously if an error occurs
				    // or server returns response with an error status.
				  });
			};
			//DEVELOPER FUNCTIONS -- REMOVE 
		});
		
	</script> 
	<?php 	
		//echo '<pre>';
		//echo "Memcache Status<br>";
		//print_r($sess->getMemStats());
		//$sess->showAll();
		//printf("<line><br>Sessions table dump <br>");
		//$result = $dbAccess->db_query("select * from sessions");
		//while($row = $result->fetch_assoc()){
    		//print_r($row);
    		//echo '<br />';
		//}
		//printf("</line><br>");
		//print_r($result->fetch_assoc());
		//echo "number of query rows = {$result->num_rows} <br>";
		//echo "HTTP_USER_AGENT = " . $_SERVER['HTTP_USER_AGENT'] .  "<br>";
		//if(isset($_SERVER['HTTP_REFERER'])) {echo "HTTP_REFERER = " . $_SERVER['HTTP_REFERER'] . "<br>";}
		//echo "REMOTE_ADDR = " . $_SERVER['REMOTE_ADDR'] . "<br>";
		//echo "Session Object dump <br>";
		//print_r($sess_data);
		
		
		
		
		//echo '<br></pre>';
	?>
        
	</body>
</html>


