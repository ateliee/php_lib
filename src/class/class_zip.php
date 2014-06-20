<?php

//+++++++++++++++++++++++++++++
// zip�N���X(phpmyadmin��zip.lib.php���g�p)
//+++++++++++++++++++++++++++++
class class_zip
{
    var $datasec = array();
    var $ctrl_dir = array();
    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
    var $old_offset = 0;

    function unix2DosTime($unixtime = 0)
    {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);
        if ($timearray['year'] < 1980) {
            $timearray['year'] = 1980;
            $timearray['mon'] = 1;
            $timearray['mday'] = 1;
            $timearray['hours'] = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        } // end if
        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
        ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    }

    function addFile($data, $name, $time = 0)
    {
        $name = str_replace('\\', '/', $name);
        $dtime = dechex($this->unix2DosTime($time));
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
            . '\x' . $dtime[4] . $dtime[5]
            . '\x' . $dtime[2] . $dtime[3]
            . '\x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');
        $fr = "\x50\x4b\x03\x04";
        $fr .= "\x14\x00"; // ver needed to extract
        $fr .= "\x00\x00"; // gen purpose bit flag
        $fr .= "\x08\x00"; // compression method
        $fr .= $hexdtime; // last mod time and date
        // "local file header" segment
        $unc_len = strlen($data);
        $crc = crc32($data);
        $zdata = gzcompress($data);
        $zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
        $c_len = strlen($zdata);
        $fr .= pack('V', $crc); // crc32
        $fr .= pack('V', $c_len); // compressed filesize
        $fr .= pack('V', $unc_len); // uncompressed filesize
        $fr .= pack('v', strlen($name)); // length of filename
        $fr .= pack('v', 0); // extra field length
        $fr .= $name;
        // "file data" segment
        $fr .= $zdata;
        // "data descriptor" segment (optional but necessary if archive is not
        // served as file)
        // nijel(2004-10-19): this seems not to be needed at all and causes
        // problems in some cases (bug #1037737)
        //$fr .= pack('V', $crc);                 // crc32
        //$fr .= pack('V', $c_len);               // compressed filesize
        //$fr .= pack('V', $unc_len);             // uncompressed filesize
        // add this entry to array
        $this->datasec[] = $fr;
        // now add to central directory record
        $cdrec = "\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00"; // version made by
        $cdrec .= "\x14\x00"; // version needed to extract
        $cdrec .= "\x00\x00"; // gen purpose bit flag
        $cdrec .= "\x08\x00"; // compression method
        $cdrec .= $hexdtime; // last mod time & date
        $cdrec .= pack('V', $crc); // crc32
        $cdrec .= pack('V', $c_len); // compressed filesize
        $cdrec .= pack('V', $unc_len); // uncompressed filesize
        $cdrec .= pack('v', strlen($name)); // length of filename
        $cdrec .= pack('v', 0); // extra field length
        $cdrec .= pack('v', 0); // file comment length
        $cdrec .= pack('v', 0); // disk number start
        $cdrec .= pack('v', 0); // internal file attributes
        $cdrec .= pack('V', 32); // external file attributes - 'archive' bit set
        $cdrec .= pack('V', $this->old_offset); // relative offset of local header
        $this->old_offset += strlen($fr);
        $cdrec .= $name;
        // optional extra field, file comment goes here
        // save to central directory
        $this->ctrl_dir[] = $cdrec;
    }

    function file()
    {
        $data = implode('', $this->datasec);
        $ctrldir = implode('', $this->ctrl_dir);
        return
            $data .
            $ctrldir .
            $this->eof_ctrl_dir .
            pack('v', sizeof($this->ctrl_dir)) . // total # of entries "on this disk"
            pack('v', sizeof($this->ctrl_dir)) . // total # of entries overall
            pack('V', strlen($ctrldir)) . // size of central dir
            pack('V', strlen($data)) . // offset to start of central dir
            "\x00\x00"; // .zip file comment length
    }

}

/*
require_once('zip.lib.php');
//zip�I�u�W�F�N�g�쐬
$zipFile = new zipfile();

//windows�̓o�C�i�����[�h�I���I
$handle = fopen("./hoge".$fileName, "rb");
$targetFile = fread($handle,filesize("./hoge".$fileName));
fclose($handle);
//�t�@�C�����A�[�J�C�u�ɒǉ�
$zipFile -> addFile($targetFile, "./files/".$fileName);

addFile�̑���Ƀt�@�C�����̂ݎw�肷��ƁA�𓀌�Ƀt�H���_�����œW�J�����̂ŁA�����ł̓t�@�C�������p�X�w�肵�ăt�H���_�ɓW�J�����悤�ɂ��Ă���B
�� ��
�t�@�C���Ƃ��ĕۑ����� Edit

//���k���ꂽ���̂��o�C�g��Ŏ��o��
$zipCompByte = $zipFile->file();

//�t�@�C���Ƀo�C�g��Ƃ��ď�������
$handle = fopen("hoge.zip", "wb");
fwrite($handle, $zipCompByte);
fclose($handle);

�� ��
�X�g���[���Ƃ��ă_�E�����[�h������ Edit

//���k���ꂽ���̂��o�C�g��Ŏ��o��
$zipCompByte = $zipFile->file();

//�w�b�_�[�w��œf���o��
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"hoge.zip\"");
print $zipCompByte
*/
?>
