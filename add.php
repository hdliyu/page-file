<?php
include 'function.php';
$file=$_GET['file']??'';
if(!file_exists($file)) error();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name']??'';
    $url = $_POST['url']??'';
    $handle = fopen($file,'a');
    if(empty($name) || empty($url)) error('请填写完整！');
    fwrite($handle,PHP_EOL.$name.'	'.$url);
    fclose($handle);
    success('index.php?page=9999','成功...');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>添加<?=$_GET['title']??'';?></title>
    <link href="https://cdn.bootcss.com/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="page-header">
        <h1><?=$_GET['title']??'';?> <small>添加</small><a class="btn btn-default pull-right" href="index.php">列表</a>
        </h1>
    </div>
    <form method="post" style="margin-top: 20px;">
        <div class="form-group">
            <label for="exampleInputPassword1">标题</label>
            <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Content" name="url" required>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">内容</label>
            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Title" name="name" required>
        </div>
        <button type="submit" class="btn btn-default">提交</button>
    </form>
</div>
</body>
</html>
