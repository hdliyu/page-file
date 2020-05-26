<?php
function page_file($file,$title='',$pagesize=10){
    include 'Page.php';
    include 'PageFile.php';
    $pf = new PageFile($file);
    $pf->render($title,$pagesize);
}
function error($msg = 'error...',$second = 1){
    $html =<<<html
    <div class="alert alert-danger" role="alert">$msg</div>
<script>setTimeout(function(){history.back();},{$second}*1000);</script>
html;
    $code = bs_code_start().$html.bs_code_end();
    die($code);
}

function success($url='index.php',$msg='success...',$second = 1){
    $html = <<<html
    <div class="alert alert-success" role="alert">$msg</div>
    <script>setTimeout(function(){location.href='{$url}';},{$second}*1000);</script>
html;
    $code = bs_code_start().$html.bs_code_end();
    die($code);
}
function bs_code_start(){
    $html =<<<html
    <!doctype html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.bootcss.com/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container" style="margin-top: 20px;">
<div class="page-header">
  <h1>提示信息 <small>info</small></h1>
</div>
html;
    return $html;
}

function bs_code_end(){
    $html=<<<html
    </div>
 </body>
</html>
html;
    return $html;
}