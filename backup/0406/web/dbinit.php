<?php
error_reporting(E_ALL); 
ini_set('display_errors',1); 

include('check.php');


$databaseName = 'oddeyefactory';
$databaseUser = 'oddeyefactory';
$databasePassword = 'oddeye0504';
$zone = "_ahyun";
// zone value 
$usersdb = "users";
$visitdb = "tb_stat_visit";
/* users_ahyun 수정 필요*/

/*
 * 데이터베이스 생성
 */
$pdoDatabase = new PDO('mysql:host=localhost', $databaseUser, $databasePassword);
$pdoDatabase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//$pdoDatabase->exec('DROP DATABASE IF EXISTS oddeyefactory;');
$pdoDatabase->exec('CREATE DATABASE IF NOT EXISTS oddeyefactory DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');


/*
 * 테이블 생성
 */
$pdo = new PDO('mysql:host=localhost;dbname='.$databaseName, $databaseUser, $databasePassword);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
$pdo->exec('DROP TABLE IF EXISTS '.$usersdb.';');

$pdo->exec('CREATE TABLE `users` (
 `uid` int(11) NOT NULL AUTO_INCREMENT,
 `username` VARCHAR(255) NOT NULL,
 `password` VARCHAR(255) NOT NULL,
 `userprofile` VARCHAR(255),
 `salt` VARCHAR(255) NOT NULL,
 `regtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `is_admin` tinyint(4) NOT NULL DEFAULT 0,
 `activate` tinyint(4) NOT NULL DEFAULT 0,
 PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');


$pdo->exec('CREATE TABLE `tb_stat_visit` (
 `seq` int(11) unsigned not null auto_increment primary key,
 `regdate` datetime not null,
 `regip` varchar(30) null,
 `referer` text null
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');



/*
 * 관리자 계정 admin 생성
 */
$default_password = 'admin';
$salt = bin2hex(openssl_random_pseudo_bytes(32));
$encrypted_password = base64_encode(encrypt($default_password, $salt));

$createAdmin = $pdo->prepare('INSERT INTO '.$usersdb.'
	(username, password, is_admin, activate, salt) VALUES
    ("admin", :password, 1, 1, :salt)');

$createAdmin->bindparam(":password", $encrypted_password);
$createAdmin->bindparam(":salt", $salt);
$createAdmin->execute();





echo "데이터베이스 초기화에 성공했습니다.\n";