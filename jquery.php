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
				$list = (isset($_GET['list']) && is_array($_GET['list'])) ? $_GET['list'] : array();
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
<html lang="en">
 <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Skills Assessment Exercise: PHP, CSS, JavaScript, SQL</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style type="text/css">
	p {
		text-align: center;
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
  </style>
 </head>
 <body>
  <p><a id="setupDatabase" href="#">Setup Database</a></p>
<?php

$arr = array('Fruit' => 'Apple', 'Color' => 'Yellow', 'Number' => 7);
$arr['Number'] *= 3;

$json = json_encode($arr);
$arr = json_decode($json);

echo '<ul>';
foreach($arr as $key => $value) {
  echo '<li>' . $key . ': ' . ($key == 'Color' ? strtolower($value) : $value) . '</li>';
}
echo '</ul>';

?>  
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script>
	$(document).ready(function() {
		$('li:first').click(function() {
			$(this).text(function() {
				return $(this).text().replace('Apple', 'Orange');
			});
		});
    
		$('ul').on('mouseenter mouseleave', 'li:nth-child(2)', function() {
			$(this).toggleClass('active');
		});
    
		$('ul').on('click', 'li:last', function() {
			var text = $(this).text();
			$(this).fadeOut({duration: 1000, complete: function() {
				$(this).remove();
				var li = $('<li>' + text + '</li>').prependTo('ul').hide().fadeIn(1000);
			}});
		});
		
		$('#setupDatabase').click(function(e) {
			e.preventDefault();
			
			var dbHost = prompt('Please enter the hostname', 'localhost');
			var dbUser = prompt('Please enter the username', 'root');
			var dbPass = prompt('Please enter the password', '');
			var dbName = prompt('Please enter the Database name', 'test_db');
			
			params = {action: 'setup', dbHost: dbHost, dbUser: dbUser, dbPass: dbPass, dbName: dbName};
			ajaxGet(window.location.pathname, $(this).parent(), params);
			
			return false;
		});
		
		$('body').on('click', '#saveList', function(e) {
			e.preventDefault();

			var list = [];
			$('li').each(function() {
				list.push($(this).text().split(':'));
			});
			
			params = {action: 'save', list: list};
			ajaxGet(window.location.pathname, $(this).parent(), params);
			
			return false;
		});
	});
	
	function ajaxGet(url, obj, params) {
		$.get(url, params)
		.done(function(data, status, xhr) {
			if (data.success) {
				obj.html(data.html).addClass('active').delay(1000).fadeOut({duration: 1000, complete: function() {
					$(this).remove();
					var p = $('<p><a id="saveList" href="#">Save List</a></p>').prependTo('body').hide().fadeIn(1000);
				}});
			} else {
				handleError(xhr, 'Error', data.html);
			}
		})
		.fail(handleError = function(xhr, textStatus, errorThrown) {
			alert(textStatus + ': ' + errorThrown);
		});
	}
  </script>
 </body>
</html>

<?php session_write_close(); unset($_SESSION); ?>