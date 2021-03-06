<?php

require_once("database_conf.php");
require_once("install_conf.php");
if (!is_array($Campsite)) {
	echo "Invalid configuration file(s)";
	exit(1);
}

$db_name = $Campsite['DATABASE_NAME'];
$db_user = $Campsite['DATABASE_USER'];
$db_passwd = $Campsite['DATABASE_PASSWORD'];
$db_host = $Campsite['DATABASE_SERVER_ADDRESS'];
$templates_dir = $Campsite['WWW_DIR'] . "/$db_name/html/look";

if (!mysql_connect($db_host, $db_user, $db_passwd)) {
	die("Unable to connect to the database.\n");
}

if (!mysql_select_db($db_name)) {
	die("Unable to use the database " . $db_name . ".\n");
}
mysql_query("SET NAMES 'utf8'");

// 
// populate the ATM table
//
$sql = "SHOW TABLES LIKE 'X%'";
if (!($res = mysql_query($sql))) {
	die("Unable to read from the database.\n");
}

while ($row = mysql_fetch_array($res)) {
	$sql = "INSERT INTO ArticleTypeMetadata (type_name, field_name) VALUES ('". substr($row[0], 1) ."', 'NULL')";
	mysql_query($sql);

	$sql = "SHOW COLUMNS FROM ". $row[0] ." LIKE 'F%'";
	$res2 = mysql_query($sql);
	$weight = 1;
	while ($row2 = mysql_fetch_array($res2, MYSQL_ASSOC)) {
		if (stristr($row2['Type'], 'int') != '') {
			$sql = "SELECT RootTopicId FROM TopicFields WHERE ArticleType='". substr($row[0], 1) ."' AND FieldName='". substr($row2['Field'], 1) ."'";
			$res3 = mysql_query($sql);
			if ($topics = mysql_fetch_array($res3, MYSQL_ASSOC)) {
				if (count($topics) > 0) $coltype = 'topic';
				else $coltype = 'unknowntopic';
			} else {
				$coltype = 'unknowntopic';
			}
		}
		else {
			$coltype = strtolower($row2['Type']);
		}
		
		switch ($coltype) {
			case 'mediumblob':
				$type = 'body';
				break;
			case 'varchar(255)':
			case 'varbinary(255)':
				$type = 'text';
				break;
			case 'date':
				$type = 'date';
				break;
			case 'topic':
				$type = 'topic';
				break;
			default:
				$type = 'unknown';
				break;
		}
		
		$sql = "INSERT INTO ArticleTypeMetadata (type_name, field_name, field_type, field_weight) VALUES ('". substr($row[0], 1) ."', '". substr($row2['Field'], 1) ."', '$type', $weight)";
		$weight++;
		$insres = mysql_query($sql);
	}	
	
}

?>