<?php
//============================================
// class_rss.php
//============================================

//+++++++++++++++++++++++++++++
// RSSクラス
//+++++++++++++++++++++++++++++
class class_rss
{
    var $encode = 'UTF-8';
    var $xml_version = '1.0';
    var $rss_version = '2.0';
    //-----------------------------
    // RSSを作成
    //-----------------------------
    function createRSS($list, $rssdata = NULL)
    {
        // 出力するRSSデータを設定
        $rss = '';
        $rss .= '<?xml version="' . $this->xml_version . '" encoding="' . $this->encode . '"?>' . "\r\n";
        $rss .= '<rss version="' . $this->xml_version . '">' . "\r\n";
        $rss .= '<channel>' . "\r\n";
        if (is_array($rssdata)) {
            $rss .= '<title>' . $rssdata['title'] . '</title>' . "\r\n"; // HPのタイトル
            $rss .= '<link>' . $rssdata['link'] . '</link>' . "\r\n"; // HPのアドレス
            $rss .= '<description>' . $rssdata['description'] . '</description>' . "\r\n"; // HP全体のコメント
            $rss .= '<lastBuildDatetle>' . $rssdata['date'] . '</lastBuildDate>' . "\r\n"; // 更新日時
            $rss .= '<language>' . $rssdata['language'] . '</language>' . "\r\n"; // 言語
        }
        if (is_array($list)) {
            foreach ($list as $value) {
                $rss .= '<item>' . "\r\n";
                $rss .= '<title>' . $value['title'] . '</title>' . "\r\n"; // タイトル
                $rss .= '<link>' . $value['link'] . '</link>' . "\r\n"; // リンク先
                $rss .= '<description>' . $value['description'] . '</description>' . "\r\n"; // 簡単なコメント
                $rss .= '<pubDate>' . $value['date'] . '</pubDate>' . "\r\n"; // 更新日
                $rss .= '</item>' . "\r\n";
            }
        }
        $rss .= '</channel>' . "\r\n";
        $rss .= '</rss>';
        return $rss;
    }

    function _readRSSArrayRoop(&$output, $i, $valuelist)
    {
        if (isset($valuelist[$i])) {
            $data = $valuelist[$i];
            if (isset($data['tag'])) {
                $data["tag"] = strtolower($data["tag"]);
                if ($data['type'] == 'open') {
                    $val = array();
                    $val["_value"] = array();
                    $count = count($valuelist);
                    for ($i += 1; $i < $count; $i++) {
                        $item = $valuelist[$i];
                        $item["tag"] = strtolower($item["tag"]);
                        if ($item['tag'] == $data["tag"] && $item['type'] == 'close') {
                            break;
                        } else {
                            $i = $this->_readRSSArrayRoop($val["_value"], $i, $valuelist);
                        }
                    }
                    if (isset($data["attributes"])) {
                        $val["_attributes"] = array();
                        foreach ($data["attributes"] as $k => $v) {
                            $val["_attributes"][strtolower($k)] = $v;
                        }
                    }
                    if ($data['tag'] == "item" || $data['tag'] == "entry") {
                        if (!isset($output[$data['tag']])) {
                            $output[$data['tag']] = array();
                        }
                        $output[$data['tag']][] = $val;
                    } else {
                        $output[$data['tag']] = $val;
                    }
                } else {
                    $output[$data['tag']] = array();
                    $output[$data['tag']]["_value"] = "";
                    if (isset($data["value"])) {
                        $output[$data['tag']]["_value"] = $data["value"];
                    }
                    if (isset($data["attributes"])) {
                        $output[$data['tag']]["_attributes"] = array();
                        foreach ($data["attributes"] as $k => $v) {
                            $output[$data['tag']]["_attributes"][strtolower($k)] = $v;
                        }
                    }
                }
            }
        }
        return $i;
    }

    function getXMLparse($url)
    {
        /*$def_user_agent = ini_get('user_agent');
        ini_set('user_agent', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');
        $contents = false;
        if( function_exists( 'file_get_contents' ) ){
              $contents = @file_get_contents( $url );
        }else{
              $fp = @fopen( $url, 'r' );
              if( $fp !== FALSE ){
                    $contents = "";
                    while( !feof( $fp ) ){
                          $contents .= fread( $fp, 1024 );
                    }
                    fclose( $fp );
              }
        }
        ini_set('user_agent',$def_user_agent);*/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3");
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $contents = curl_exec($ch);
        curl_close($ch);

        if ($contents === false) {
            return false;
        }
        // XMLをパースして構造体に入れる
        $parser = xml_parser_create($this->encode);
        xml_parse_into_struct($parser, $contents, $valuelist);
        xml_parser_free($parser);
        return $valuelist;
    }

    function readRSSArray($url)
    {
        $valuelist = $this->getXMLparse($url);
        // データ
        $datalist = array();
        // 連想配列から値を取得
        if ($valuelist) {
            $count = count($valuelist);
            for ($i = 0; $i < $count; $i++) {
                $i = $this->_readRSSArrayRoop($datalist, $i, $valuelist);
            }
        }
        return $datalist;
    }

    function readRSS($url)
    {
        $list = $this->readRSSArray($url);
        $datalist = array();
        if (isset($list["rss"]) && isset($list["rss"]["_value"]["channel"]) && isset($list["rss"]["_value"]["channel"]["_value"]["item"])) {
            // 整形
            foreach ($list["rss"]["_value"]["channel"]["_value"]["item"] as $key => $val) {
                $item = array();
                foreach ($val["_value"] as $k => $v) {
                    $item[$k] = $v["_value"];
                }
                $datalist[$key] = $item;
            }
        }
        return $datalist;
    }
    //-----------------------------
    // ブログRSS読み込み
    //-----------------------------
    function loadBlogRSS($url, $type, $count = 0)
    {
        $blog_data = array();
        $i = 0;
        // RSS読み込み
        if ($rss = $this->readRSS($url)) {
            foreach ($rss as $item) {
                if (($count > 0) && ($count <= $i)) {
                    break;
                }
                // AmebaBlog
                if ($type == SYSTEM_BLOG_AMEBA) {
                    // 広告を飛ばす
                    if (isset($item["title"]) && mb_substr($item["title"], 0, 3) == 'PR:') {
                        continue;
                    }
                }
                $data = array();
                $data["link"] = strval(isset($item["link"]) ? $item["link"] : "");
                $data["title"] = strval($item["title"] ? $item["title"] : "");
                $data["pubDate"] = strval($item["pubdate"] ? $item["pubdate"] : "");
                $data["date"] = strtotime(strval($data["pubDate"]));
                if (isset($item["content:encoded"])) {
                    $data["description"] = strval($item["content:encoded"]);
                } else {
                    $data["description"] = strval($item["description"]);
                }
                $blog_data[] = $data;
                $i++;
            }
            /*if(isset($rss["items"])){
                    foreach($rss["items"] as $item) {
                          if(($count > 0) && ($count <= $i)){
                                break;
                          }
                          // AmebaBlog
                          if($type == SYSTEM_BLOG_AMEBA){
                                  // 広告を飛ばす
                                  if(isset($item["title"]) && mb_substr($item["title"],0,3) == 'PR:'){
                                        continue;
                                  }
                          }
                          $data = array();
                          $data["link"] = strval(isset($item["link"]) ? $item["link"] : "");
                          $data["title"] = strval($item["title"] ? $item["title"] : "");
                          $data["pubDate"] = strval($item["pubdate"] ? $item["pubdate"] : "");
                          $data["date"] = strtotime(strval($data["pubDate"]));
                          if($type == SYSTEM_BLOG_WORDPRESS){
                                  $data["description"] =strval($item["content:encoded"]);
                          }else{
                                  $data["description"] =strval($item["description"]);
                          }
                          $blog_data[] = $data;
                          $i ++;
                    }
            }*/
        }
        /*if($rss = simplexml_load_file($url)){
              foreach($rss->channel->item as $item) {
                    if(($count > 0) && ($count <= $i)){
                          break;
                    }
                    // AmebaBlog
                    if($type == SYSTEM_BLOG_AMEBA){
                            // 広告を飛ばす
                            if(mb_substr($item->title,0,3) == 'PR:'){
                                  continue;
                            }
                    }
                    $data = array();
                    $data["link"] = strval($item->link);
                    $data["title"] = strval($item->title);
                    $data["pubDate"] = strval($item->pubDate);
                    $data["date"] = strtotime(strval($item->pubDate));
                    if($type == SYSTEM_BLOG_WORDPRESS){
                            $data["description"] =strval($item->content);
                    }else{
                            $data["description"] =strval($item->description);
                    }
                    $blog_data[] = $data;
                    $i ++;
              }
        }
        */
        return $blog_data;
    }
}

?>