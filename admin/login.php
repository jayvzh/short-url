<?php
$config_file = dirname(__FILE__).'/../system/config.inc.php';
include($config_file);

if (!$db_config) {
    header('Location: ../install/');
    exit(0);
}

session_start();

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: ./index.php');
    exit(0);
}

switch($_GET['step']) {
    default:
        $content = '<form action="?step=login" method="post">';

        $content .= '<div class="mb-3 row">';
        $content .= '<label for="username" class="col-sm-4 col-form-label">管理员用户名：</label>';
        $content .= '<div class="col-sm-6">';
        $content .= '<input type="text" class="form-control" name="username" id="username">';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="mb-3 row">';
        $content .= '<label for="password" class="col-sm-4 col-form-label">管理员密码：</label>';
        $content .= '<div class="col-sm-6">';
        $content .= '<input type="password" class="form-control" name="password" id="password">';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="row">';
        $content .= '<div class="col-3 offset-9">';
        $content .= '<button type="submit" class="btn btn-primary">登陆</button>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '</form>';

        show_page($content);

        break;

    case 'login':
        $username = safe_input($_POST['username']);
        $password = md5($_POST['password']);

        if (!$username || !$password) {
            show_back('输入的信息不完整！');
            exit(0);
        }

        $conn = @new mysqli($db_config['server'], $db_config['username'], $db_config['password'], $db_config['name'], $db_config['port']);

        if ($conn->connect_error) {
            show_back('数据库连接失败：' . $conn->connect_error);
            exit(0);
        }

        $sql = "SELECT `username`, `password` FROM `user` WHERE `username` = '$username' AND `password` = '$password'";
        $result = $conn->query($sql);
        if (!$result) {
            show_back('登陆过程出现错误：' . $conn->error);
            exit(0);
        }

        if ($result->num_rows > 0) {
            $_SESSION['admin'] = true;

            header('Location: ./index.php');
        } else {
            show_back('用户名或密码错误！');
        }
}

$conn->close();

function safe_input($data) {
    $data = trim($data);
    $data = stripcslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function show_page($content) {
    $template = '<!doctype html><html lang="zh-CN"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>短网址服务 - 缩短长链接！</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css"></head><body><div class="container-fluid px-0"><nav class="navbar bg-light"><div class="container-fluid"><span class="navbar-brand mb-0 h1">短网址服务 - 管理面板</span></div></nav><div class="row py-4"><div class="col-3 d-none d-sm-flex"></div><div class="col-12 col-sm-6">{content}</div><div class="col-3 d-none d-sm-flex"></div></div></div></body></html>';

    echo str_replace('{content}', $content, $template);
}

function show_back($text) {
    $content = '<p>' . $text . '</p>';
    $content .= '<div class="row">';
    $content .= '<div class="col-3 offset-9">';
    $content .= '<button type="button" class="btn btn-primary" onclick="history.back();">返回</button>';
    $content .= '</div>';
    $content .= '</div>';

    show_page($content);
}
?>