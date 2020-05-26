<?php
/**
 * Class Page bootstrap分页显示
 */
class Page{
    protected $page;
    protected $totalPage;
    protected $total;
    protected $pagesize;

    public function __construct($total,$pagesize)
    {
        $this->totalPage = ceil($total/$pagesize);
        $this->page = intval(max(min(max($_GET['page']??1,1),$this->totalPage),1));
    }

    /**
     * @param int $before 显示分页前多少页
     * @param int $mid 显示分页前后页数
     * @param int $after 显示分页最后多少页数
     * @return string 分页代码
     */
    public function render($before=1,$mid=1,$after=1)
    {
       list($disable,$prepage) = $this->getPrevPageInfo();
        $page=<<<page
<nav aria-label='Page navigation' class="clearfix">
  <ul class='pagination pull-left' style="margin-top: 0;"> 
    <li{$disable}>
      <a href='{$prepage}' aria-label='Previous'>
        <span aria-hidden='true'>&laquo;</span>
      </a>
    </li>
page;
        /*显示前$before页*/
        for($i=1;$i<=$this->totalPage;$i++){
            if($i>$before) break;
            $active = $this->page == $i?' class="active"':'';
            $pinfo = $this->pageinfo();
            $page.="<li $active><a href='{$pinfo}{$i}'>$i</a></li>";
        }
        if($this->page > $before+$mid+1)  $page.="<li><a href='javascript:;'>...</a></li>";
        /*显示当前页及前后$mid页*/
        for($i=$this->page-$mid;$i<=$this->page+$mid;$i++){
            if($i<$before+1) continue;
            if($i>$this->totalPage-$after) continue;
            $active = $this->page == $i?' class="active"':'';
            $pinfo = $this->pageinfo();
            $page.="<li $active><a href='{$pinfo}{$i}'>$i</a></li>";
        }
        if($this->page < $this->totalPage-$after-$mid)  $page.="<li><a href='javascript:;'>...</a></li>";
        /*显示最后$after页*/
        for($i=$this->totalPage-$after+1;$i<=$this->totalPage;$i++){
            if($this->totalPage==1) break;
            $active = $this->page == $i?' class="active"':'';
            $pinfo = $this->pageinfo();
            $page.="<li $active><a href='{$pinfo}{$i}'>$i</a></li>";
        }
        list($disable,$nextpage) = $this->getNextPageInfo();
        $search = $_GET['s']??'';
        $page.=<<<page
        <li{$disable}>
              <a href='{$nextpage}' aria-label='Next'>
                <span aria-hidden='true'>&raquo;</span>
              </a>
        </li>
    </ul>
    <form class="form-inline pull-left" method="get" action="?page=">
  <div class="form-group" style="padding-left: 15px;">
     <label for="exampleInputEmail2">第</label>
     <input type="hidden" name="s" value="{$search}">
     <input type="text" class="form-control" style="display:inline-block;width:80px;" name="page" placeholder="页码" value="{$this->page}">页
     <button type="submit" class="btn btn-primary">跳转</button>
  </div>
</form>
</nav>
page;
        return $page;
    }

    public function getTotalPage()
    {
        return $this->totalPage;
    }
    public function getPage()
    {
        return $this->page;
    }

    private function getPrevPageInfo()
    {
        if($this->page==1){
            $disable = ' class="disabled"';
            $prepage = '#';
        }else{
            $disable = '';
            $prepage = $this->pageinfo().($this->page-1);
        }
        return [$disable,$prepage];
    }
    private function getNextPageInfo()
    {
        if($this->page==$this->totalPage){
            $disable = ' class="disabled"';
            $nextpage = '#';
        }else{
            $disable = '';
            $nextpage = $this->pageinfo().($this->page+1);
        }
        return [$disable,$nextpage];
    }

    private function pageinfo()
    {
        $reg = '/&?page=[^&]*/';
        $str = preg_replace($reg,'',$_SERVER['QUERY_STRING']);
        return $str?'?'.$str.'&page=':'?page=';
    }

}
