<?php

session_start();

define('IS_AJAX_REQUEST', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false));

if (IS_AJAX_REQUEST) {
	/* Default config values */
	$dbHost = isset($_SESSION['dbHost']) ? $_SESSION['dbHost'] : 'localhost';
	$dbUser = isset($_SESSION['dbUser']) ? $_SESSION['dbUser'] : 'root';
	$dbPass = isset($_SESSION['dbPass']) ? $_SESSION['dbPass'] : '';
	$dbName = isset($_SESSION['dbName']) ? $_SESSION['dbName'] : 'test_db';
	$dbTableName = 'test_table';
	$dbCharset = 'utf8';

	$responseData = array();
	if (isset($_GET['action'])) {
		switch ($_GET['action']) {
			case 'setup':
			
			$_SESSION['dbHost'] = (isset($_GET['dbHost']) && !empty($_GET['dbHost'])) ? $_GET['dbHost'] : $dbHost;
			$_SESSION['dbUser'] = (isset($_GET['dbUser']) && !empty($_GET['dbUser'])) ? $_GET['dbUser'] : $dbUser;
			$_SESSION['dbPass'] = (isset($_GET['dbPass']) && !empty($_GET['dbPass'])) ? $_GET['dbPass'] : $dbPass;
			
			$mysqli = @new mysqli($_SESSION['dbHost'], $_SESSION['dbUser'], $_SESSION['dbPass']);
			if ($mysqli->connect_error) {
				$responseData['success'] = false;
				$responseData['html'] = "Connect Error: ($mysqli->connect_errno) $mysqli->connect_error";
			} else {
				$_SESSION['dbName'] = (isset($_GET['dbName']) && !empty($_GET['dbName'])) ? $mysqli->real_escape_string($_GET['dbName']) : $dbName;
				$mysqli->set_charset($dbCharset);
				if ($mysqli->select_db($_SESSION['dbName'])) {
					$mysqli->query('DROP DATABASE ' . $_SESSION['dbName']);
				}
				$sql = "CREATE DATABASE IF NOT EXISTS " . $_SESSION['dbName'] . " DEFAULT CHARACTER SET $dbCharset;
						USE " . $_SESSION['dbName'] . ";
						DROP TABLE IF EXISTS `$dbTableName`;
						CREATE TABLE `$dbTableName` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
						`label` varchar(255) DEFAULT NULL,
						`value` varchar(255) DEFAULT NULL,
						PRIMARY KEY (`id`)
					  ) ENGINE=MyISAM DEFAULT CHARSET=$dbCharset;";
					
				if ($mysqli->multi_query($sql)) {
					$responseData['success'] = true;
					$responseData['html'] = "Database '" . $_SESSION['dbName'] . "' created successfully";
				} else {
					$responseData['success'] = false;
					$responseData['html'] = "($mysqli->errno) $mysqli->error";
				}
				$mysqli->close();
			}
			break;
			case 'save':
			$mysqli = @new mysqli($_SESSION['dbHost'], $_SESSION['dbUser'], $_SESSION['dbPass'], $_SESSION['dbName']);
			if ($mysqli->connect_error) {
				$responseData['success'] = false;
				$responseData['html'] = "Connect Error: ($mysqli->connect_errno) $mysqli->connect_error";
			} else {
				$sql = '';
				$list = isset($_GET['list']) ? json_decode($_GET['list']) : array();
				foreach ($list as $item) {
					$sql .= "INSERT INTO `$dbTableName` (label, value) VALUES ('" . $mysqli->real_escape_string(trim($item[0])) . "', '" . $mysqli->real_escape_string(trim($item[1])) . "');";
				}

				$mysqli->set_charset($dbCharset);
				if ($mysqli->multi_query($sql)) {
					$responseData['success'] = true;
					$responseData['html'] = "List saved successfully";
				} else {
					$responseData['success'] = false;
					$responseData['html'] = "($mysqli->errno) $mysqli->error";
				}
				$mysqli->close();
			}
			break;
			default:
			$responseData['success'] = false;
			$responseData['html'] = 'Invalid action!';
			break;
		}
	} else {
		$responseData['success'] = false;
		$responseData['html'] = 'What do you want to do!';
	}
	
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($responseData);
	exit;
}

header("Content-type: text/html; charset=utf-8"); 

?>

<!DOCTYPE html>
<html lang="en" ng-app="Skills">
 <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Skills Assessment Exercise: PHP, CSS, JavaScript, SQL. Using AngularJS.</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style type="text/css">
	p {
		text-align: center;
		transition: opacity 1s;
	}
	
	p.active {
		background-color: #0f0;
	}
	
	ul {
		list-style: none;
		list-style-image: none;
		padding: 0;
	}

	ul > li {
		display: block;
		background-color: #ccc;
		margin: 20px 0;
		text-align: center;
	}

	ul > li:hover {
		background-color: #6cf;
	}

	ul > li.active {
		background-color: #f00;
	}

	li.fade {
		transition: opacity 1s;
	}
	
	[ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
		display: none !important;
	}
  </style>
 </head>
 <body>
  <p id="setupDatabase" ng-controller="DatabaseCtrl as db"><a ng-click="db.setup(); $event.preventDefault()" href="#">Setup Database</a></p>
<?php

	$arr = array('Fruit' => 'Apple', 'Color' => 'Yellow', 'Number' => 7);
	$arr['Number'] *= 3;

	$json = json_encode($arr);
	$arr = json_decode($json);

	echo '<ul id="list" ng-cloak ng-controller="AppCtrl as app">';
	$i = 1;
	foreach($arr as $key => $value) {
		if ($i == 1) {
		echo '<li ng-click="value' . $i . '=\'Orange\'" ng-init="key' . $i . '=\'' . $key . '\';value' . $i . '=\'' . $value . '\'">{{key' . $i . '}}: {{value' . $i . '}}</li>';
		} else if ($i == 2) {
		echo '<li ng-class="{active: hover}" ng-mouseover="hover = true" ng-mouseleave="hover = false" ng-init="key' . $i . '=\'' . $key . '\';value' . $i . '=\'' . ($key == 'Color' ? strtolower($value) : $value) . '\'">{{key' . $i . '}}: {{value' . $i . '}}</li>';
		} else if ($i == 3) {
		echo '<li id="li3" ng-click="app.action3()" ng-init="key' . $i . '=\'' . $key . '\';value' . $i . '=\'' . $value . '\'">{{key' . $i . '}}: {{value' . $i . '}}</li>';
		}
		$i++;
	}
	echo '</ul>';

?>
  <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.24/angular.min.js"></script>
  <script>
	(function() {
		var app = angular.module('Skills', []);
		
		app.controller('DatabaseCtrl', ['$http', '$timeout', function($http, $timeout) {
			this.setup = function() {
			
				var dbHost = prompt('Please enter the hostname', 'localhost');
				var dbUser = prompt('Please enter the username', 'root');
				var dbPass = prompt('Please enter the password', '');
				var dbName = prompt('Please enter the Database name', 'test_db');
				
				params = {action: 'setup', dbHost: dbHost, dbUser: dbUser, dbPass: dbPass, dbName: dbName};
				
				$http({method: 'GET', url: window.location.pathname, params: params, headers: {'X-Requested-With': 'XMLHttpRequest'}})
				.success(function(data, status) {
					if (data.success) {
						var p = document.getElementById('setupDatabase');
						p.innerHTML = data.html;
						p.classList.add('active');
						$timeout(function() {
							p.style.opacity = 0;
							$timeout(function() {
								angular.element(p).remove();
								
								var p2 = document.createElement('p');
								p2.style.opacity = 0;
								var a = document.createElement('a');
								a.setAttribute('id', 'saveList');
								a.setAttribute('href', '#');
								var text = document.createTextNode('Save List');
								a.appendChild(text);
								p2.appendChild(a);
								document.body.insertBefore(p2, document.getElementById('list'));
								$timeout(function() {
									p2.style.opacity = 1;
									
									a.addEventListener('click', function(e) {
										preventDefault(e);
										
										var list = [];
										var lis = document.querySelectorAll('ul#list li');
										for (i = 0; i < lis.length; i++) {
											list.push(lis[i].textContent.split(':'));
										}
										params = {action: 'save', 'list': JSON.stringify(list)};
										$http({method: 'GET', url: window.location.pathname, params: params, headers: {'X-Requested-With': 'XMLHttpRequest'}})
										.success(function(data, status) {
											if (data.success) {
												p2.innerHTML = data.html;
												p2.classList.add('active');
											} else {
												handleError(data, data.html);
											}
										})
										.error(handleError = function(data, status) {
											alert('Error: ' + status);
										});	
										
									}, false);
								}, 0);
							}, 1000);
						}, 1000);
					} else {
						handleError(data, data.html);
					}
				})
				.error(handleError = function(data, status) {
					alert('Error: ' + status);
				});
			}
		}]);
		
		app.controller('AppCtrl', ['$timeout', function($timeout) {
			this.action3 = function() {
				var li = document.getElementById('li3');
				//li.className = li.className + ' fade';
				li.classList.add('fade');
				li.style.opacity = 0;

				$timeout(function() {
					var ul = document.getElementById('list');
					ul.insertBefore(li, ul.firstChild);
					$timeout(function() {
						li.style.opacity = 1;
					}, 0);
				}, 1000);
			}
		}]);
	})();
	
	function preventDefault(e) {
		var evt = e ? e : window.event;
		if (evt.preventDefault) {
			evt.preventDefault();
		}
		evt.returnValue = false;
		return false;
	}
  </script>
 </body>
</html>

<?php session_write_close(); unset($_SESSION); ?>