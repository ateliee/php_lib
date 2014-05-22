<?php
//============================================
// class_html.php
//============================================

//+++++++++++++++++++++++++++++
// タグクラス
//+++++++++++++++++++++++++++++
class class_htmlElement{
      var     $tagName     = '';
      var     $blank       = false;
      var     $attribute   = array();
      var     $childs      = array();
      var     $parent      = NULL;
      // タグの作成
      function create($name,$b = false){
            $this->tagName            = $name;
            $this->blank            = $b;
            $this->attribute      = array();
            $this->childs            = array();
      }
      // 子属性の設定
      function addChild(&$elm){
            if($elm){
                  if(is_a($elm,get_class($this))){
                        $this->childs[] = $elm;
                        if($elm->parent){
                              $elm->parent->popChild($elm);
                        }
                        $elm->parent = $this;
                  }
            }
      }
      // 子属性の削除
      function popChild(&$elm){
            $count = count($this->childs);
            for($i=0;$i<$count;$i++){
                  $e = &$this->childs[$i];
                  if($e === $elm){
                        $e->parent = NULL;
                        array_splice($this->childs,$i,1);
                        return;
                  }
            }
      }
      // 親属性の設定
      function setParent(&$elm){
            if($this->parent){
                  $this->parent->popChild($this);
            }
            $this->parent = $elm;
            if($this->parent && is_a($elm,get_class($this))){
                  $this->parent->addChild(this);
            }
      }
      // 属性値の設定
      function addAttribute($name,$att){
            $this->attribute[$name] = $att;
      }
      // 属性値の取得
      function getAttribute($name){
            return $this->attribute[$name];
      }
      // 属性値の削除
      function deleteAttribute($name){
            $i = 0;
            foreach($this->attribute as $key => $value){
                  if($name === $key){
                        array_splice($this->attribute,$i,1);
                        return;
                  }
                  $i ++;
            }
      }
      // String変換
      function innerText(){
            $tags_str = "";
            // 子の要素を取得
            foreach( $this->childs as $e ){
                  if($e){
                        $tags_str .= $e->toString();
                  }
            }
            return $tags_str;
      }
      function toString(){
            // 開始タグ
            $tags_str = "";
            $tags_str .= "<".$this->tagName;
            foreach( $this->attribute as $key => $val ){ $tags_str .= " ".$key."=\"".$val."\""; }
            if($this->blank == false){
                  $tags_str .= ">\n";
                  $tags_str .= $this->innerText();
                  // 閉じタグ
                  $tags_str .= "</".$this->tagName.">\n";
            }else{
                  $tags_str .= " />\n";
            }
            return $tags_str;
      }
      // html表示
      function printTag(){
            print($this->toString());
      }
}

//+++++++++++++++++++++++++++++
// hmlクラス
//+++++++++++++++++++++++++++++
class class_html{
      // HTMLエレメントを作成
      function createTag($tag_name){
            $elm = new class_htmlElement;
            $elm->create($tag_name);
            return $elm;
      }
      // PHP,htmlのタグを取り除く
      function stripTags($str,$allowable_tags = ''){
            return strip_tags($str,$allowable_tags);
      }
      // タグで囲む
      function wrapTags($str,$tag){
            return '<'.$tag.'>'.$str.'</'.tag.'>';
      }
}
?>