<?php
//============================================
// class_calender.php
//============================================

//+++++++++++++++++++++++++++++
// カレンダークラス
//+++++++++++++++++++++++++++++
class class_calender{
      var      $year;
      var      $month;
      var      $day;
      var $week_str = array(
            array( 'week' => 'sun' , 'date' => '日' , 'abbr' => '日曜日' ),
            array( 'week' => 'mon' , 'date' => '月' , 'abbr' => '月曜日' ),
            array( 'week' => 'tue' , 'date' => '火' , 'abbr' => '火曜日' ),
            array( 'week' => 'wed' , 'date' => '水' , 'abbr' => '水曜日' ),
            array( 'week' => 'fri' , 'date' => '木' , 'abbr' => '木曜日' ),
            array( 'week' => 'thu' , 'date' => '金' , 'abbr' => '金曜日' ),
            array( 'week' => 'sta' , 'date' => '土' , 'abbr' => '土曜日' ),
      );
      var      $days = array();
      var      $prev = "";
      var      $next = "";
// var      $cal = CAL_GREGORIAN;
      // 日付を設定
      function set($y,$m,$d){
            $this->year = $y;
            $this->month = $m;
            $this->day = $d;
      }
      // カレンダー情報を設定
      function setCal($info){
            $this->cal = $info;
      }
      // 日付の中身を設定
      function setDayInfo($day,$text){
            $this->days[$day] = $text;
      }
      // カレンダーを取得
      function getCalender($week_start = 0){
            $c_str = "";
            $day = 1;
            // カレンダーの情報を設定する
            if($this->cal == CAL_GREGORIAN){
                  $time = mktime(0,0,0,$this->month,$day,$this->year);
                  $date = date('Y/m/d',$time);
                  $week = date('w',$time);
                  $month_end = date('t',$time);
            }else if($this->cal == CAL_JULIAN){
                  $time = cal_to_jd(CAL_GREGORIAN,$this->month,$this->day,$this->year);
                  $date = cal_from_jd($time, CAL_GREGORIAN);
                  $date = $date['year'].'/'.sprintf('%02d',$date['month']).'/'.sprintf('%02d',$date['day']);
                  $week = jddayofweek(cal_to_jd(CAL_GREGORIAN, $this->month,$day, $this->year),0);
                  $month_end = cal_days_in_month(CAL_GREGORIAN,$this->month,$this->year);
            }else{
                  return NULL;
            }
            // カレンダーを作成
            $c_str .= '<table class="calenar_table" cellpadding="0" cellspacing="1" summary="カレンダー">'."\n";
            $c_str .= '<thead><tr>'."\n";
            foreach($this->week_str as $key => $val){
                  $c_str .= '<th class="'.$val['week'].'" abbr="'.$val['abbr'].'">'.$val['date'].'</th>'."\n";
            }
            // 曜日を作成
            $c_str .= '</tr></thead>'."\n";
            // 日付を設定
            $c_str .= '<tbody>'."\n";
            $c_str .= '<tr>'."\n";
            if($week > 0){
                  $c_str .= '<td class="'.$this->week_str[$week]['week'].'" colspan="'.$week.'">&nbsp;</td>'."\n";
            }
            for($day = 1; $day <= $month_end; $day++,$week++ ){
                  if($week >= 7){
                        $week = $week % 7;
                        $c_str .= '</tr>'."\n".'<tr>'."\n";
                  }
                  $text = $day;
                  if(isset($this->days[$day])){
                        $text = $this->_replaseDay($this->year,$this->month,$day,$this->days[$day]);
                  }
                  $class = $this->week_str[$week]['week'];
                  if($this->day == $day){
                        $class .= " today";
                  }
                  $colspan = '';
                  if($day == $month_end){
                        $colspan = ' colspan="'.(7 - $week).'"';
                  }
                  $c_str .= '<td class="'.$class.'"'.$colspan.'>'.$text.'</td>'."\n";
            }
            $c_str .= '</tr>'."\n";
            $c_str .= '</tbody>'."\n";
            // リンクを設定
            $c_str .= '<tfoot>'."\n";
            $c_str .= '<td class="foot_left" colspan="1">'.$prev.'</td>'."\n";
            $c_str .= '<td class="foot_center" colspan="5">'.$date.'</td>'."\n";
            $c_str .= '<td class="foot_left" colspan="1">'.$next.'</td>'."\n";
            $c_str .= '</tfoot>'."\n";
            
            $c_str .= '</table>'."\n";
            return $c_str;
      }
      // 内部関数(日付表示を変換)
      function _replaseDay($y,$m,$d,$text){
            $rep = array(
                  '%Y' =>sprintf( "%04d", $y ),
                  '%y' =>sprintf( "%02d", $y ),
                  '%m' =>sprintf( "%02d", $m ),
                  '%M' =>sprintf( "%2d", $m ),
                  '%d' =>sprintf( "%02d", $d ),
                  '%j' =>sprintf( "%2d", $d ),
            );
            if(!isset($text))      return $text;
            foreach($rep as $key => $val){
                  $text = preg_replace('/('.$key.')/',$val,$text);
            }
            return $text;
      }
}

?>