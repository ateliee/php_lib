<?php
//============================================
// rss_fetch_config.php
//============================================
        // dcdate��������pubdate�����鎞(FeedBurner�Ƃ�)
        // "Fri, xx Dec 2xxx xx:xx:xx +0900"�݂����ȃt�H�[�}�b�g�ɂȂ��Ă���Magpie��"rss_utils.inc"��
        // �Ή��o����t�H�[�}�b�g�ɕϊ�����("rss_utils.inc"���Q�l�ɂ��Ă܂�)
        $L_DATE_PATTERN = "/^(?:\D{3})\,\s(\d{2})\s(\D{3})\s(\d{4})\s(\d{2}):(\d{2}):(\d{2})\s([-+]\d{4}|\D{3})/";
        
        // ���t�t�H�[�}�b�g�ϊ�
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
                                // "$item['pubdate']"����ŕϊ��o���Ȃ��������͂��̂܂ܕ\��
                                $date = $item['pubdate'];
                        }
                }
                return $date;
        }
        // PR������菜��
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