<?php
//============================================
// rss_fetch_config.php
//============================================
        // dcdateが無くてpubdateがある時(FeedBurnerとか)
        // "Fri, xx Dec 2xxx xx:xx:xx +0900"みたいなフォーマットになってたらMagpieの"rss_utils.inc"で
        // 対応出来るフォーマットに変換する("rss_utils.inc"を参考にしてます)
        $L_DATE_PATTERN = "/^(?:\D{3})\,\s(\d{2})\s(\D{3})\s(\d{4})\s(\d{2}):(\d{2}):(\d{2})\s([-+]\d{4}|\D{3})/";
        
        // 日付フォーマット変換
        function rss_fetch_date_format($format,$item){
                GLOBAL $L_DATE_PATTERN;
                $date = "";
                if(isset($item['date_timestamp'])) {
                        $date = date($format, $item['date_timestamp']);
                }else if(isset($item['dc']['date'])) {
                        $date = date($format, parse_w3cdtf($item['dc']['date']));
                }else if (isset($item['pubdate'])) {
                        if (preg_match($L_DATE_PATTERN, $item['pubdate'])) {
                                $date = date($format, parse_w3cdtf(conv_date($item['pubdate'])));
                        }else {
                                // "$item['pubdate']"を上で変換出来なかった時はそのまま表示
                                $date = $item['pubdate'];
                        }
                }
                return $date;
        }
        // PR文を取り除く
        function rss_fetch_split_pr($items,$num=0){
                $i = 0;
                $valuelist = array();
                foreach($items as $key => $item){
                        if(($num > 0) && ($i >= $num)){
                                break;
                        }
                        if(preg_match("/^PR:/",$item['title'])){
                                continue;
                        }
                        $valuelist[$key] = $item;
                        $i ++;
                }
                return $valuelist;
        }
?>