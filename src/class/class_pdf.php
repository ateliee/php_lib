<?php
//============================================
// class_pdf.php
//   v.1.1      : テーブル描画追加
//============================================

$LIB_DIR = dirname(__FILE__) . '/../library/';
require_once($LIB_DIR . 'tcpdf/tcpdf.php');
require_once($LIB_DIR . 'fpdi/fpdi.php');

//+++++++++++++++++++++++++++++
// PDFクラス
//+++++++++++++++++++++++++++++
class class_pdf extends FPDI
{
    protected $templates; // 読み込んだテンプレートのリスト
    /*// 好みの初期化を行う
    function myInit() {
        $this->SetMargins(0, 0, 0);		// 用紙の余白を設定
        $this->SetCellPadding(0);		// セルのパディングを設定
        $this->SetAutoPageBreak(false);	// 自動改ページ
           
        $this->setPrintHeader(false);	// ヘッダを使用しない
        $this->setPrintFooter(false);	// フッタを使用しない
    }*/
    // テンプレートPDFファイルをロードする。
    // @param string	$filepath	PDFファイルのパス
    function myLoadTemplate($filepath)
    {
        $page_count = $this->setSourceFile($filepath);
        $template_id = array();
        for ($i = 0; $i < $page_count; $i++) {
            $template_id[] = $this->importPage($i + 1);
        }
        $this->templates = array(
            $filepath => array(
                'page_count' => $page_count,
                'template_id' => $template_id,
            )
        );
    }
    // 指定したPDFファイルの指定したページをテンプレートとして使用する。
    // @param string    $filepath   PDFファイルのパス
    // @param int       $page       ページ番号（1から）
    function myUseTemplate($filepath, $page)
    {
        if (!isset($this->templates[$filepath])) {
            $this->myLoadTemplate($filepath);
        }
        if (1 <= $page && $page <= $this->templates[$filepath]['page_count']) {
            $this->useTemplate($this->templates[$filepath]['template_id'][$page - 1]);
        } else {
            throw new Exception('Template not found');
        }
    }

    function Cell_AutoFontSize($fontSize, $w = 0, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M')
    {
        // 幅によってフォントサイズを6段階で縮小する。
        $size = $fontSize;
        for ($i = 0; $size > 0; $i++) {
            //if ( $w >= ( mb_strlen(trim($txt),'UTF-8')) * ($fontSize - $i) * 0.35 ) {
            if ($w >= (mb_strwidth(trim($txt), 'UTF-8')) * ($fontSize - $i) * 0.228) {
                break;
            }
            $size = $fontSize - $i;
        }
        $this->SetFontSize($size);
        $this->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link, $stretch, $ignore_min_height, $calign, $valign);
    }

    // HEX ⇒ RGB変換
    function convertHEXtoRGB($hex)
    {
        $color = preg_replace("/^#/", '', $hex);
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));
        return array($r, $g, $b);
    }

    // ポイント数値をmmに変換
    function getPtToMM($p)
    {
        return ($p * 0.35);
    }

    function getPtToCM($p)
    {
        return ($p * 0.035);
    }

    function getInchToMM($p)
    {
        return ($p * 25.4);
    }

    function getInchToCM($p)
    {
        return ($p * 2.54);
    }

    function getInchToPt($p)
    {
        return ($p * 2.54 / 0.35);
    }

    function getPtToInch($p)
    {
        return ($p * 0.35 / 2.54);
    }

    // テーブルの書き出し
    function writeTable($table, $settings = array(), $rowsettings = array())
    {
        foreach ($table as $key => $rows) {
            $this->writeTableRow($rows, isset($rowsettings[$key]) ? array_merge($settings, $rowsettings[$key]) : $settings);
        }
    }

    function writeTableRow($rows, $setting = array())
    {
        $max_height = 0;
        $reseth = true;
        $tx = 0;
        // 行の高さを取得(スペースを出力)
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetXY($x, $y);
        $this->MultiCell(0, 0, " ", 0, "L", 0, 0, '', '', true);
        $line_height = $this->getLastH();
        // 行の最大の高さを取得
        foreach ($rows as $k => $value) {
            // 初期設定
            $value["align"] = isset($value["align"]) ? $value["align"] : (isset($setting["align"]) ? $setting["align"] : "L");
            $value["valign"] = isset($value["valign"]) ? $value["valign"] : (isset($setting["valign"]) ? $setting["valign"] : "T");
            $value["fill"] = isset($value["fill"]) ? $value["fill"] : (isset($setting["fill"]) ? $setting["fill"] : 0);
            $value["bgcolor"] = isset($value["bgcolor"]) ? $value["bgcolor"] : (isset($setting["bgcolor"]) ? $setting["bgcolor"] : "#999999");
            $value["padding"] = isset($value["padding"]) ? $value["padding"] : (isset($setting["padding"]) ? $setting["padding"] : 0);
            $value["border"] = isset($value["border"]) ? $value["border"] : (isset($setting["border"]) ? $setting["border"] : 0);
            $value["borderWidth"] = isset($value["borderWidth"]) ? $value["borderWidth"] : (isset($setting["borderWidth"]) ? $setting["borderWidth"] : 0.2);
            $value["borderColor"] = isset($value["borderColor"]) ? $value["borderColor"] : (isset($setting["borderColor"]) ? $setting["borderColor"] : "");
            // 行の高さを取得
            $lh = $this->getNumLines($value["text"], $value["width"] - ($value["padding"] * 2), false, false, 0, 0);
            $h = ($lh * $line_height) + ($value["padding"] * 2);
            // 行数格納
            $value["x"] = $tx;
            $value["line"] = $lh;
            $value["height"] = $h;
            $max_height = max($h, $max_height);
            //$tx += $value["width"] + ($value["padding"] * 2);
            $tx += $value["width"];

            $rows[$k] = $value;
        }
        $i = 0;
        // 書き出し
        foreach ($rows as $value) {
            // 線もしくは塗りつぶしの描画
            if ($value["border"] || isset($value["fill"])) {
                if ($value["borderColor"] != "") {
                    list($r, $g, $b) = $this->convertHEXtoRGB($value["borderColor"]);
                    $this->SetDrawColor($r, $g, $b);
                }
                if (isset($value["bgcolor"])) {
                    list($r, $g, $b) = $this->convertHEXtoRGB($value["bgcolor"]);
                    $this->SetFillColor($r, $g, $b);
                }
                $this->SetLineWidth($value["borderWidth"]);
                $this->SetXY($x + $value["x"], $y);
                $this->MultiCell($value["width"], $max_height, "", $value["border"], $value["align"], $value["fill"], 0, '', '', true);
            }
            // テキスト描画
            $this->SetXY($x + $value["x"] + $value["padding"], $y + (($reseth) ? $value["padding"] : 0));
            $this->MultiCell($value["width"] - ($value["padding"] * 2), 0, $value["text"], 0, $value["align"], 1, 0, '', '', $reseth, 0, false, false, $max_height - ($value["padding"] * 2) + 0.1, $value["valign"]);
            $i++;
        }
        // 次の行を指定しておく
        $this->SetXY($x, $y + $max_height);

    }
}
