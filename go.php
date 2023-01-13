<?php
$config_file = dirname(__FILE__).'/system/config.inc.php';
include($config_file);

$id = safe_input($_GET['id']);
$conn = @new mysqli($db_config['server'], $db_config['username'], $db_config['password'], $db_config['name'], $db_config['port']);

if ($conn->connect_error) {
    echo '数据库连接失败：' . $conn->connect_error;
    exit(0);
}

$sql = 'SELECT url FROM `fwlink` WHERE id = "' . from62_to10($id) . '";';
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    header('Location: '. $row['url']);
} else {
    echo '查询失败！';
}

$conn->close();

function safe_input($data) {
    $data = trim($data);
    $data = stripcslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function from62_to10($num) {
    $from = 62;
    $num = strval($num);
    $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $len = strlen($num);
    $dec = 0;
    for($i = 0; $i < $len; $i++) {
        $pos = strpos($dict, $num[$i]);
        $dec = bcadd(bcmul($pos, bcpow($from, $len - $i - 1)), $dec);
    }
    return $dec;
}

?>