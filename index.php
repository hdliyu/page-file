<?php
header('Content-type:text/html;charset=utf-8');
include 'function.php';
try{
    page_file('note.txt','网站列表',10);
}catch (Exception $e){
    die($e->getMessage());
}
