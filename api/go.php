<?php
$core_file = dirname(__FILE__).'/../system/core.php';
require_once($core_file);
$MysqliDb_file = dirname(__FILE__).'/../system/MysqliDb.php';
require_once($MysqliDb_file);

$db = new MysqliDb (Array (
    'host' => $db_config['server'],
    'username' => $db_config['username'], 
    'password' => $db_config['password'],
    'db'=> $db_config['name'],
    'port' => $db_config['port']));

$id = $_GET['id'];
$result = $db->where('id', from62_to10($id))->get('fwlink');
if (count($result) === 0) {
    exit("查询失败！");
} else {
    header('Location: '. $result[0]['url']);
}

$db->disconnect();
?>