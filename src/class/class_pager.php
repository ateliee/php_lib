<?php

/**
 * Class class_pager
 */
class class_pager{
    static $URL_MODE_PARAM = 1;
    static $URL_MODE_URL = 2;

    private $url_mode;
    private $total_count;
    private $visible;
    private $current;
    private $pager_count;
    private $page_param;
    private $base_url;
    private $page_start;
    private $page_end;
    private $page_count;

    function class_pager(){
        $this->url_mode = self::$URL_MODE_PARAM;
        $this->total_count = 0;
        $this->visible = 0;
        $this->current = 0;
        $this->pager_count = 10;
        $this->page_param = 'page';
        $this->base_url = '';
    }

    /**
     * @param $mode
     * @return $this
     */
    public function setUrlMode($mode){
        $this->url_mode = $mode;
        return $this;
    }

    /**
     * @param $base_url
     * @return $this
     */
    public function setBaseUrl($base_url){
        $this->base_url = $base_url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl(){
        return $this->base_url;
    }

    /**
     * @param $visible
     * @param $total_count
     * @return $this
     */
    public function setPageData($visible,$total_count){
        $this->visible = $visible;
        $this->total_count = $total_count;
        return $this;
    }

    /**
     * @param $count
     * @return $this
     */
    public function setPagerCount($count){
        $this->pager_count = $count;
        return $this;
    }

    /**
     * @return int
     */
    public function getPagerCount(){
        return $this->pager_count;
    }

    /**
     * @param $current
     * @return $this
     */
    public function setCurrent($current){
        $this->current = $current;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrent(){
        return $this->current;
    }

    /**
     * @param $page
     * @return string
     */
    public function getPagesUrl($page){
        $url = $this->base_url;
        $params = array();
        if(preg_match('/^(.*)\?(.*)$/',$url,$matchs)){
            $url = $matchs[1];
            if(isset($matchs[2])){
                parse_str($matchs[2], $params);
            }
        }
        if($page != 0){
            if($this->url_mode == self::$URL_MODE_PARAM){
                $params[$this->page_param] = $page;
            }else{
                $filename = basename($url);
                $page_url = $page;
                if($this->page_param != ""){
                    $page_url = $this->page_param.'/'.$page_url;
                }
                if(preg_match('/^(.*)\.(.+)$/',$filename,$mt)){
                    $url = str_replace($filename,$page_url.'/'.$filename,$url);
                }else{
                    if(substr($url,-1) != '/'){
                        $url .= '/';
                    }
                    $url .= $page_url.'/';
                }
            }
        }

        return $url.((count($params) > 0) ? '?'.http_build_query($params) : '');
    }

    /**
     *
     */
    private function initPageCount(){
        $this->page_count = $this->total_count > 0 ? ceil($this->total_count / $this->visible) : 0;
        if(floor($this->page_count / 2) > $this->current){
            $this->page_start = max($this->current - floor($this->pager_count / 2),0);
            $this->page_end = min($this->page_start + $this->pager_count,$this->page_count);
        }else{
            $this->page_end = min($this->current + ceil($this->pager_count / 2),$this->page_count);
            $this->page_start = max($this->page_end - $this->pager_count,0);
        }
    }

    /**
     * @return array
     */
    public function getData(){
        $this->initPageCount();

        $arr = array();
        $arr['count'] = $this->total_count;
        $arr['visible'] = $this->visible;
        $arr['current'] = $this->current;
        $arr['prev'] = max($this->current - 1,0);
        $arr['next'] = max(min($this->current + 1,$this->pager_count),0);
        $arr['page_count'] = $this->page_count;
        $arr['start'] = $this->page_start;
        $arr['end'] = $this->page_end;
        $arr['html'] = $this->getPagerHTML('&lt;&lt;','&gt;&gt;');
        $arr['pager'] = $this;
        return $arr;
    }

    /**
     * @param $prev
     * @param $next
     * @return array|string
     */
    public function getPagerHTML($prev=null,$next=null){
        // ページ送り
        $pager = "";
        $pager_num = 0;
        if(!is_null($prev)){
            if($this->pager_count < $this->page_count){
                if(($this->page_count - 1) >= 0){
                    $pager = '<li class="li_'.$pager_num.'"><a href="'.$this->getPagesUrl($this->current - 1).'">'.$prev.'</a></li>'.$pager;
                    $pager_num ++;
                }else if($this->page_count > 0){
                    //$pager = '<li class="li_'.$pager_num.'"><span>&lt;&lt;</span></li>'.$pager;
                    //$pager_num ++;
                }
            }
        }
        if($this->page_count > 1){
            for($i=$this->page_start;$i<$this->page_end;$i++){
                if($i == $this->current){
                    $pager .= '<li class="li_'.$pager_num.' active"><span>'.($i + 1).'</span></li>';
                    $pager_num ++;
                }else{
                    $pager .= '<li class="li_'.$pager_num.'"><a href="'.$this->getPagesUrl($i).'">'.($i + 1).'</a></li>';
                    $pager_num ++;
                }
            }
        }
        if(!is_null($next)) {
            if ($this->pager_count < $this->page_count) {
                if (($this->current + 1) < $this->page_count) {
                    $pager = $pager . '<li class="li_' . $pager_num . '"><a href="' . $this->getPagesUrl($this->current + 1) . '">' . $next . '</a></li>';
                    $pager_num++;
                } else if ($this->page_count > 0) {
                    //$pager = $pager.'<li class="li_'.$pager_num.'"><span>&gt;&gt;</span></li>';
                    //$pager_num ++;
                }
            }
        }
        return $pager;
    }
}