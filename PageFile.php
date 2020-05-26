<?php
class PageFile{
    protected $file;
    protected $pagesize;
    protected $return;
    protected $searchFile;
    protected $usecache;
    protected $cacheFile;
    protected $cacheTime;

    public function __construct($file,$pagesize=10,$return=false,$searchFile='search.txt',$usecahce=true,$cacheFile='cache.txt',$cacheTime=1800){
        $this->file = $file;
        $this->pagesize = $pagesize;
        $this->return = $return;
        $this->searchFile = $searchFile;
        $this->usecache = $usecahce;
        $this->cacheFile = $cacheFile;
        $this->cacheTime = $cacheTime;
    }

    public function render($title='',$pagesize=10)
    {
        if(!file_exists($this->file))  throw new Exception('文件不存在!');
        $handle = fopen($this->file, 'r');
        $total = 0; //总行数
        $arr[1] = 0; //第一行开始于0
        $i = 2; //第2行开始记录
        $search = $_GET['s']??'';
        if($search){
            $article = '';
            while (!feof($handle)) {
                $row = fgets($handle);
                if(strpos($row,$search)!==false){
                    $article.=$row;
                }
            }
            file_put_contents($this->searchFile,trim($article));
            $handle = fopen($this->searchFile, 'r');
        }
        if(!$search && $this->usecache && file_exists($this->cacheFile) && time()-filemtime($this->cacheFile)<$this->cacheTime ) {
            list($arr,$total) = unserialize(file_get_contents($this->cacheFile));
        }else{
            while (!feof($handle)) {
                $row = fgets($handle);
                if ($row) {
                    $arr[$i] = $arr[$i - 1] + strlen($row); //不需要+1  记录每行第一个字节的位置 = 前一行+当前行长度
                    $total++;
                    $i++;
                }
            }
            if(!$search && $this->usecache)  file_put_contents($this->cacheFile,serialize([$arr,$total]));
        }
        fseek($handle, 0);
        $page = new Page($total, $pagesize);
        $pagestr = $page->render();
        $curpage = $page->getPage();
        $totalPage = $page->getTotalPage();
        $offset = ($curpage - 1) * $pagesize + 1; //文件行便宜
        fseek($handle, $arr[$offset]);
        $code = $this->getBaseHtml($title,$search);
        $count = 1;
        $reg='/^(.*?)\s+(.*)$/i';
        while (!feof($handle)) {
            if ($count == $pagesize + 1) break;
            $row = fgets($handle);
            if($search && strpos($row,$search)===false) continue;
            $line = $offset+$count-1;
            preg_match($reg, $row, $match);
            //if(empty($match)) continue;//空行是否跳过
            $url = $match[1]??''?:'0'; //保证空行不报错
            $name = $match[2]??''?:'&nbsp;';//保证空行不报错
            if($search){
                $name = str_replace($search,'<font color="red">'.$search.'</font>',$name);
            }
            $code .= <<<html
     <li class="list-group-item">
   <!-- <span class="badge pull-left" style="margin-right: 5px;">{$line}</span>--> <a class="badge" href="http://{$url}" target="_blank">{$url}</a><a href="javascript:;" target="_blank" class="text-warning">{$name}</a>
     </li>
html;
            $count++;
        }
        $code .= '</ul></div></div>';
        fclose($handle);
        if($total==0) {
            $none = $code.'暂无数据</div></body></html>';
            if($this->return) return $none;
            echo $none;
            return;
        }
        $html = $code.$pagestr.'当前页：' . $curpage . ',总页数：' . $totalPage . ',记录数：'.$total.'</div></body></html>';
        if($this->return) return $html;
        echo $html;
    }

    protected function getBaseHtml($title='',$search='')
    {
        $code = <<<code
    <!doctype html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>$title</title>
    <link href="https://cdn.bootcss.com/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container" style="margin-top: 20px;">
    <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <form class="form-inline" method="get">
  <div class="form-group">
    <div class="input-group">
      <input type="text" class="form-control" name="s" placeholder="请输入名称" value="$search">
    </div>
  </div>
  <button type="submit" class="btn btn-primary">搜索</button>
  <a href="index.php" class="btn btn-default">重置</a>
  <a href="add.php?file={$this->file}&title={$title}" class="btn btn-default pull-right">添加</a>
</form>
</div>
</div>
 <div class="row" style="margin-top: 20px;">
 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <ul class="list-group">
code;
        return $code;
    }

}
