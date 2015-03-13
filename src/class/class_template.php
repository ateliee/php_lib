<?php
/**
 * Class Template v1
 * support mysql
 */
# TODO : PHP template convert
# TODO : cache create

class TemplateException extends Exception
{
}
/**
 * Class TemplateVarNode
 */
class TemplateVarNode{
    static $TYPE_FUNCTION = 'function';
    static $TYPE_VAR = 'var';
    static $TYPE_VARLIST = 'varlist';
    static $TYPE_ARRAY = 'array';
    static $TYPE_CALCULATION = 'calculation';

    private $type;
    private $name;
    private $num;
    private $params;
    private $next;

    function  __construct($type,$name=null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->num = 0;
        $this->params = array();
        $this->next = null;
    }

    /**
     * @return null
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getType(){
        return $this->type;
    }

    /**
     * @param $p
     */
    public function addParam($p){
        $this->params[] = $p;
        if(count($this->params) > 1){
            $this->params[count($this->params)-2]->setNext($this->params[count($this->params)-1]);
        }
    }

    /**
     * @return array
     */
    public function getParams(){
        return $this->params;
    }

    /**
     * @param $n
     * @return mixed
     */
    public function getParam($n){
        return $this->params[$n];
    }

    /**
     * @param $key
     * @param $p
     */
    public function addKeyParam($key,$p){
        $this->params[$key] = $p;
    }

    /**
     * @param TemplateVarNode $n
     */
    public function setNext(TemplateVarNode &$n){
        $this->next = $n;
    }

    /**
     * @return TemplateVarNode
     */
    public function getNext(){
        return $this->next;
    }
}

/**
 * Class TemplateVarParser
 */
class TemplateVarParser{
    private $original_str;
    private $nodes;
    private $encoding;

    function  __construct($str,$encoding)
    {
        $this->original_str = $str;
        $this->encoding = $encoding;

        $params = array();
        // string to convert
        $tmp = "";
        $str_length = mb_strlen($str, $this->encoding);
        $wrap_count = 0;
        for($i = 0; $i < $str_length; $i++){
            $s = mb_substr($str,$i,1,$this->encoding);
            if(in_array($s,array("'","\""))){
                $tmp .= $s;
                $sp = $s;
                $bs = null;
                for($i++; $i < $str_length; $i++){
                    $s = mb_substr($str,$i,1,$this->encoding);
                    $tmp .= $s;
                    if($s == $sp && (($sp != '"') || (($bs.$s) != '\"'))){
                        break;
                    }
                    $bs = $s;
                }
                if($i >= $str_length){
                    $this->error('Bat Format Template "'.$str.'"');
                }
                $params[] = $tmp;
                $tmp = '';
            }else if(in_array($s,array("[","]"))){
                if(($tmp == '') && ($s == '[')){
                    $wrap_count ++;
                }
                if($wrap_count <= 0){
                    $tmp .= $s;
                }else{
                    if($tmp != ""){
                        $params[] = $tmp;
                        $tmp = "";
                    }
                    $params[] = $s;
                    if($s == ']'){
                        $wrap_count --;
                    }
                }
            }else if(in_array($s,array("(",")",",",":"))){
                if($tmp != ""){
                    $params[] = $tmp;
                    $tmp = "";
                }
                $params[] = $s;
            }else if(in_array($s,array("=","<",">","*","!","+","-","/","%","&"))){
                $s3 = mb_substr($str,$i,3,$this->encoding);
                $s2 = mb_substr($str,$i,2,$this->encoding);
                if($tmp != ""){
                    $params[] = $tmp;
                    $tmp = "";
                }
                if(in_array($s3,array("==="))) {
                    $params[] .= $s3;
                    $i += 2;
                }else if(in_array($s2,array("<=",">=","==","!=","||","&&"))){
                    $params[] .= $s2;
                    $i ++;
                }else{
                    $params[] .= $s;
                }
            }else if(in_array($s,array(" "))){
            }else{
                $tmp .= $s;
            }
        }
        if($tmp != ""){
            $params[] = $tmp;
            $tmp = "";
        }
        $this->createNode($params);

    }

    /**
     * @param $params
     */
    private function createNode($params){
        $num = 0;
        $this->nodes = array();

        for($num=0;$num<count($params);$num++){
            $n = $this->_createNodePointer($params,$num);
            if($n){
                $this->addNode($n);
            }
        }
    }

    /**
     * @param $params
     * @param $num
     * @return null|TemplateVarNode
     */
    private function _createNodePointer(&$params,&$num){
        if(count($params) <= $num){
            return null;
        }
        $s = $params[$num];
        $ns = (count($params) > ($num + 1)) ? $params[$num+1] : null;

        // inner var
        if($s == '('){
            $success = false;
            $o = new TemplateVarNode(TemplateVarNode::$TYPE_VARLIST);
            $skip = 0;
            for($num+=1;$num<count($params);$num++){
                $ss = $params[$num];
                if($ss == ')'){
                    $skip --;
                    if($skip < 0){
                        if(count($o->getParams()) > 0){
                            $success = true;
                        }
                        break;
                    }
                }
                $p = $this->_createNodePointer($params,$num);
                if($p){
                    $o->addParam($p);
                }
            }
            if(!$success){
                $this->error('Parse Error VarList '.$this->original_str);
            }
            return $o;
            // array
        }else if($s == '['){
            $success = false;
            $o = new TemplateVarNode(TemplateVarNode::$TYPE_ARRAY);
            for($num++;$num<count($params);$num++){
                $ss = $params[$num];
                if($ss == ']'){
                    $success = true;
                    break;
                }else if($ss == ','){
                    continue;
                }
                $p1 = $this->_createNodePointer($params,$num);
                $num2 = $num + 1;
                $p2 = $this->_createNodePointer($params,$num2);
                $num3 = $num2 + 1;
                $p3 = $this->_createNodePointer($params,$num3);
                if($p1){
                    if($p2->getName() == ':'){
                        if($p3){
                            $num = $num3;
                            for($num++;$num<count($params);$num++){
                                $ss3 = $params[$num];
                                if($ss3 == ']'){
                                    $success = true;
                                    break;
                                }else if($ss3 == ','){
                                    break;
                                }
                                $pp3 = $this->_createNodePointer($params,$num);
                                $p3->addParam($pp3);
                            }
                            $o->addKeyParam($p1->getName(),$p3);
                            if($success){
                                break;
                            }
                        }else{
                            $this->error('Parse Error Array '.$this->original_str);
                        }
                    }else{
                        $o->addParam($p1);
                    }
                }
            }
            if(!$success){
                $this->error('Parse Error Array '.$this->original_str);
            }
            return $o;
        }else if(in_array($s,array('===','!=','==','<=','>=','<','>','-','+','*','/','%','!','||','&&'))){
            return new TemplateVarNode(TemplateVarNode::$TYPE_CALCULATION,$s);
        }else if($ns == '('){
            $success = false;
            $o = new TemplateVarNode(TemplateVarNode::$TYPE_FUNCTION,$s);
            $vo = null;
            $skip = 0;
            for($num+=2;$num<count($params);$num++){
                $ss = $params[$num];
                if($ss == '('){
                    $vo = $this->_createNodePointer($params,$num);
                    $o->addParam($vo);
                    continue;
                    //$skip ++;
                }else if($ss == ')'){
                    $skip --;
                    if($skip < 0){
                        $success = true;
                        break;
                    }
                }else if($ss == ',') {
                    $vo = null;
                    continue;
                }else{
                    if(!$vo){
                        $vo = new TemplateVarNode(TemplateVarNode::$TYPE_VARLIST);
                        if($vo){
                            $o->addParam($vo);
                        }
                    }
                }
                $p = $this->_createNodePointer($params,$num);
                if($p){
                    $vo->addParam($p);
                }
            }
            if(!$success){
                $this->error('Parse Error '.$this->original_str);
            }
            return $o;
        }
        return new TemplateVarNode(TemplateVarNode::$TYPE_VAR,$s);
        /*for($i=$num+1;$i<count($params);$i++){
            if(!$this->_createNodePointer($params,$i)){
                break;
            }
        }*/
    }

    /**
     * @param TemplateVarNode $n
     */
    public function addNode(TemplateVarNode $n){
        $this->nodes[] = $n;
        if(count($this->nodes) > 1){
            $this->nodes[count($this->nodes)-2]->setNext($this->nodes[count($this->nodes)-1]);
        }
    }

    /**
     * @param $str
     */
    private function error($str){
        throw new TemplateException('Template Parser : '.$str);
    }

    /**
     * @return int
     */
    public function count(){
        return count($this->nodes);
    }

    /**
     * @param $n
     * @return TemplateVarNode
     */
    public function get($n){
        return $this->nodes[$n];
    }

    /**
     * @return null|TemplateVarNode
     */
    public function getFirst(){
        if($this->count() > 0){
            return $this->get(0);
        }
        return null;
    }
}

/**
 * Class TemplateBlock
 */
class TemplateBlock{
    private $parent;
    private $html;

    function  __construct($html,$parent=null)
    {
        $this->html = $html;
        $this->parent = $parent;
    }

    /**
     * @param $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return TemplateBlock
     */
    public function getParent(){
        return $this->parent;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->html;
    }
}

/**
 * Class class_template
 */
class class_template {

    private $Template;
    private $Include;
    private $Import;
    private $Block;
    private $Vars;
    private $outputVars;
    private $TemplateList;
    private $left_delimiter;
    private $right_delimiter;
    private $html_Encoding;
    private $system_Encoding;
    private $default_modifiers;
    private $base_filename;
    private $auto_header;
    private $headers;

    static private $Functions = array();

    function  __construct()
    {
        $this->resetTemplateData();
        $this->clear_all_assign();
        $this->html_Encoding = mb_internal_encoding();
        $this->system_Encoding = mb_internal_encoding();
        $this->default_modifiers = "";
        $this->setDelimiter("<?","?>");
        $this->auto_header = false;
        $this->headers = array('Content-type' => 'text/html; charset='.$this->html_Encoding);
    }

    /**
     * @return bool
     */
    private function isDebugMode()
    {
        return (ini_get('display_errors') ? true : false);
    }

    /**
     * @param $enable
     * @return mixed
     */
    public function setAutoHeader($enable){
        return ($this->auto_header = $enable);
    }

    /**
     * @return bool
     */
    public function getAutoHeader(){
        return $this->auto_header;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param $key
     * @param $val
     */
    public function addHeader($key,$val)
    {
        $this->headers[$key] = $val;
    }

    /**
     * @param $str
     */
    private function error($str){
        throw new TemplateException('Template : '.$str);
    }

    /**
     * @param $message
     * @param int $error_type
     * @throws TemplateException
     */
    private function notice($message,$error_type=E_USER_NOTICE){
        trigger_error('Template : '.$message,$error_type);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function setDefaultModifiers($name)
    {
        $before = $this->default_modifiers;
        $this->default_modifiers = $name;
        return $before;
    }

    /**
     * @param $path
     * @return base_path
     */
    public function setBasePath($path){
        return ($this->base_filename = $path);
    }

    /**
     * @param $start
     * @param $end
     * @return $this
     */
    public function setDelimiter($start,$end){
        $this->left_delimiter = $start;
        $this->right_delimiter = $end;
        return $this;
    }

    /**
     * set html encoding
     * @param string $encode encodetype(default internal encoding)
     * @return encodetype
     */
    public function setHtmlEncoding($encode)
    {
        return ($this->html_Encoding = $encode);
    }

    /**
     * set php encoding
     * @param string $encode encodetype(default internal encoding)
     * @return encodetype
     */
    public function setSystemEncoding($encode)
    {
        return ($this->system_Encoding = $encode);
    }

    /**
     * get set template variable
     * set $name is namespace for set variable
     * @param object $name
     * @return value
     */
    public function get_template_var($name = null)
    {
        if (isset($name)) {
            return $this->Vars[$name];
        }
        return $this->Vars;
    }

    /**
     *
     */
    private function resetTemplateData(){
        $this->Template = "";
        $this->TemplateList = array();
        $this->Include = array();
        $this->Import = array();
        $this->Block = array();
        $this->base_filename = "";
    }
    /**
     * set load html file
     * @param bool $filename (path)
     * @param bool $path_set
     * @return bool
     */
    public function load($filename,$path_set=true)
    {
        $this->resetTemplateData();
        // load tempate file
        if (file_exists($filename) && $fp = @fopen($filename, 'rb')) {
            $this->Template = "";
            while (!feof($fp)) {
                $this->Template .= fread($fp, 1024);
            }
            fclose($fp);

            // set path
            if($path_set){
                $this->base_filename = dirname($filename);
                $this->base_filename .= ($this->base_filename != "") ? "/" : "";
            }
            return true;
        }else{
            $this->error('Load Template Error.('.$filename.')');
        }
        return false;
    }

    /**
     * @param $str
     * @return $str
     */
    public function setTemplateStr($str){
        return ($this->Template = $str);
    }

    /**
     * set template and get html for string
     * @param bool $set
     * @return string
     */
    public function get_display_template($set = true)
    {
        $this->clearOutputVars();
        $template = $this->Template;
        if ($set) {
            // include setting
            $template = $this->setInclude($template);
            // import setting
            $template = $this->setImportTemplate($template);
            // strip setting
            $template = $this->setStripTemplate($template);
            // set template vars
            $template = $this->setTemplatesVars($template);
        }
        return $template;
    }

    /**
     *
     */
    private function clearOutputVars(){
        $this->outputVars = array();
    }

    /**
     * set template and get html for string and print
     * @see get_display_template
     * @param bool $set
     * @return null
     */
    public function display($set = true)
    {
        if($this->auto_header){
            foreach($this->headers as $key => $val){
                header($key.': '.$val);
            }
        }
        print $this->get_display_template($set);
    }

    /**
     * load inclue file
     *
     * @param $id
     * @param $filename
     * @return bool
     */
    function setIncludeFile($id,$filename){
        $this->notice('"inclue file="*"" function is deprecation. please use "extend" function.');
        if( !($fp = @fopen($filename,'rb')) ){
            return false;
        }
        $length = max(1000,filesize($filename));
        $template= fread($fp,$length);
        fclose($fp);

        $this->Include[$id] = $template;
        return true;
    }

    /**
     * set template include file
     * @param  string $filename
     * @return $template
     */
    private function loadTemplatePartsFile($filename)
    {
        $file = $this->base_filename.$filename;
        if (file_exists($file) && $fp = @fopen($file, 'rb')) {
            $template = "";
            while (!feof($fp)) {
                $template .= fread($fp, 1024);
            }
            fclose($fp);

            return $template;
        }else{
            throw new TemplateException('file not load '.$file);
        }
        return false;
    }

    /**
     * set template variable
     * @param string $id (namespace)
     * @return null
     */
    public function assign($id, $val)
    {
        $this->Vars[$id] = $val;
    }

    /**
     * set template variable
     * @see assign
     * @param array $value
     * @return null
     */
    public function assign_vars($value)
    {
        foreach ($value as $key => $val) {
            $this->assign($key, $val);
        }
    }

    /**
     * clear template variable
     * @return null
     */
    public function clear_all_assign()
    {
        $this->Vars = array();
    }

    /**
     * @param $id
     * @param $val
     */
    static public function filter($id, $val)
    {
        self::$Functions[$id] = $val;
    }

    /**
     *
     */
    static public function clear_all_filter(){
        self::$Functions = array();
    }

    /**
     * get attribe
     * @return srting
     * @access private
     */
    public function getAttr(&$attr, $str)
    {
        $attr = array();
        // key=value convert
        $preg_str = "/(\S+)\s*=\s*(\S+)/";
        if (preg_match_all($preg_str, $str, $matchs)) {
            $cnt = count($matchs[0]);
            for ($key = 0; $key < $cnt; $key++) {
                $name = strtoupper($matchs[1][$key]);
                $val = $matchs[2][$key];
                $val = $this->evaString($val);
                $attr[$name] = $val;
            }
        }
        return $attr;
    }

    /**
     * @param TemplateVarNode $node
     * @param $loop
     * @return array|bool|null|string
     */
    private function convertTemplateVar(TemplateVarNode $node,$loop)
    {
        $loop_count = 0;
        $result = null;
        while($node){
            // function
            if($node->getType() == TemplateVarNode::$TYPE_FUNCTION){
                $result = $this->convertNodeToFunction($node);
            }else if($node->getType() == TemplateVarNode::$TYPE_ARRAY){
                $result = $this->convertNodeToArray($node);
            }else if(in_array($node->getType(),array(TemplateVarNode::$TYPE_VAR,TemplateVarNode::$TYPE_VARLIST))){
                $param_count = count($node->getParams());
                $param_num = 0;
                if($node->getType() == TemplateVarNode::$TYPE_VARLIST){
                    if($param_count > 0){
                        $result = $this->convertTemplateVar($node->getParam($param_num),false);
                        $param_num ++;
                    }else{
                        throw new TemplateException();
                    }
                }else{
                    $result = $this->convertNodeToVar($node,$this->Vars,true);
                }

                if($param_count > 0){
                    for(;$param_num<$param_count;$param_num+=2){
                        $c = $node->getParam($param_num);
                        if($c->getType() == TemplateVarNode::$TYPE_CALCULATION && (($param_num + 1) < $param_count)){
                            $p2 = $this->convertTemplateVar($node->getParam($param_num+1),false);
                            $result = $this->convertToCalculation($result,$c->getName(),$p2);
                        }else{
                            throw new TemplateException();
                        }
                    }
                }
            }else if($node->getType() == TemplateVarNode::$TYPE_CALCULATION){
                $c = $node;
                if($loop_count){
                    $node = $node->getNext();
                    $p2 = $this->convertTemplateVar($node,false);
                    $result = $this->convertToCalculation($result,$c->getName(),$p2);
                }else{
                    if(($node = $node->getNext()) && ($node->getType() == TemplateVarNode::$TYPE_VAR) && is_numeric($node->getName())){
                        $p2 = $this->convertTemplateVar($node,false);
                        $result = $this->convertToCalculation(0,$c->getName(),$p2);
                    }else{
                        throw new TemplateException();
                    }
                }
            }else{
                throw new TemplateException();
            }
            if(!$loop){
                break;
            }
            $node = $node->getNext();
            $loop_count ++;

        }
        return $result;
    }

    /**
     * @param $p1
     * @param $calc
     * @param $p2
     */
    private function convertToCalculation($p1,$calc,$p2)
    {
        switch ((string)$calc) {
            case '===':
                $item = ($p1 === $p2);
                break;
            case '==':
                $item = ($p1 == $p2);
                break;
            case '<=':
                $item = ($p1 <= $p2);
                break;
            case '>=':
                $item = ($p1 >= $p2);
                break;
            case '<':
                $item = ($p1 < $p2);
                break;
            case '>':
                $item = ($p1 > $p2);
                break;
            case '!=':
                $item = ($p1 != $p2);
                break;
            case '+':
                if(is_string($p1) || is_string($p2)){
                    $item = ($p1.$p2);
                }else{
                    $item = ($p1 + $p2);
                }
                break;
            case '-':
                $item = ($p1 - $p2);
                break;
            case '*':
                $item = ($p1 * $p2);
                break;
            case '/':
                $item = ($p1 / $p2);
                break;
            case '%':
                $item = ($p1 % $p2);
                break;
            case '&&':
                $item = ($p1 && $p2);
                break;
            case '||':
                $item = ($p1 || $p2);
                break;
            default:
                $this->error('Error Template Param '.$calc);
                break;
        }
        return $item;
    }


    /**
     * @param $delimiter
     * @param $left_wrap
     * @param $right_wrap
     * @param $str
     * @return array
     */
    private function explodeWrap($delimiter,$left_wrap,$right_wrap,$str)
    {
        $delimiter_len = mb_strlen($delimiter, $this->system_Encoding);
        $left_wrap_len = mb_strlen($left_wrap, $this->system_Encoding);
        $right_wrap_len = mb_strlen($right_wrap, $this->system_Encoding);
        $arr = array();
        $tmp = "";
        $str_length = mb_strlen($str, $this->system_Encoding);
        for($i = 0; $i < $str_length; $i++){
            $tmp .= mb_substr($str,$i,1,$this->system_Encoding);
            $tmp_len = mb_strlen($tmp, $this->system_Encoding);
            if(mb_substr($tmp,-1 * $left_wrap_len) == $left_wrap){
                $count = 0;
                for($i++; $i < $str_length; $i++){
                    $tmp .= mb_substr($str,$i,1,$this->system_Encoding);
                    $tmp_len = mb_strlen($tmp, $this->system_Encoding);
                    if(mb_substr($tmp,-1 * $right_wrap_len) == $right_wrap){
                        if($count > 0){
                            $count --;
                        }else{
                            break;
                        }
                    }else if(mb_substr($tmp,-1 * $left_wrap_len) == $left_wrap){
                        $count ++;
                    }
                }
            }else if(mb_substr($tmp,-1 * $delimiter_len) == $delimiter){
                if($tmp_len > $delimiter_len){
                    $arr[] = mb_substr($tmp,0,$tmp_len - $delimiter_len,$this->system_Encoding);
                }
                $tmp = "";
            }
        }
        if($tmp != ""){
            $arr[] = $tmp;
        }
        return $arr;
    }

    /**
     * @param $str
     * @param $variables
     * @param bool $set_output
     * @return null
     */
    private function getStringVariable($str,&$variables,$set_output=true)
    {
        // , explode
        $vlist = $this->explodeWrap(".","[","]", $str);
        $tmp_output = null;
        foreach ($vlist as $v) {
            // array inside
            if (preg_match("/^([_a-zA-Z0-9]+)(\[(.+)\])?$/", $v, $m)) {
                $key = $m[1];
                if (isset($result)) {
                    if(is_object($result)) {
                        $result = $result->$key;
                    }else if (!array_key_exists($key, $result)) {
                        $this->error("not found [" . $str . "] value;");
                        $result = NULL;
                    } else {
                        //$tmp_output[$key] = (is_array($value[$key])) ? array() : true;
                        if($set_output){
                            if(!isset($tmp_output[$key])){
                                if(!is_array($tmp_output)){
                                    $tmp_output = array();
                                }
                                $tmp_output[$key] = (is_array($result[$key])) ? array() : true;
                            }
                            $tmp_output = &$tmp_output[$key];
                        }
                        $result = $result[$key];
                    }
                } else {
                    if (array_key_exists($key,$variables)) {
                        $result = $variables[$key];
                    }else{
                        $this->error("not found value ".$str." in [" . $key . "] value;");
                    }
                    if(isset($variables[$key]) && $set_output){
                        if(!isset($this->outputVars[$key])){
                            $this->outputVars[$key] = (is_array($variables[$key])) ? array() : true;
                        }
                        $tmp_output = &$this->outputVars[$key];
                    }
                }
                if (isset($m[3])) {
                    $key = $this->evaString($m[3]);
                    $result = $result[$key];
                }
            }else{
                $this->error('not found format '.$str.'.');
            }
        }
        return $result;
    }

    /**
     * @param TemplateVarNode $node
     */
    private function convertNodeToVar(TemplateVarNode $node,&$variables,$set_output=true)
    {
        // string
        if (preg_match("/^\"([\s\S]*)\"$/", $node->getName(), $matchs)) {
            $result = (string)$matchs[1];
            $result = str_replace('\"','"',$result);
        }else if (preg_match("/^'([\s\S]*)'$/", $node->getName(), $matchs)) {
            $result = (string)$matchs[1];
            // array
        } elseif (preg_match("/^\\\$([\[\]_a-zA-Z0-9\.\\\$]+)$/", $node->getName(), $matchs)) {
            $result = $this->getStringVariable($matchs[1],$variables,$set_output);
        } elseif (is_numeric($node->getName())) {
            $result = intval($node->getName());
        } elseif (strtoupper($node->getName()) == "TRUE") {
            $result = true;
        } elseif (strtoupper($node->getName()) == "FALSE") {
            $result = false;
        } elseif (strtoupper($node->getName()) == "NULL") {
            $result = NULL;
        } else {
            $this->notice("not found value \"".$node->getName()."\" value.if string is \" or ' wrap string. ");
            $result = $node->getName();
            //$this->error("not found value ".$node->getName()." value;");
        }
        return $result;
    }

    /**
     * @param TemplateVarNode $node
     * @return array
     */
    private function convertNodeToArray(TemplateVarNode $node)
    {
        $result = array();
        foreach($node->getParams() as $key => $p){
            $key = $this->evaString($key);
            $result[$key] = $this->convertTemplateVar($p,false);
        }
        return $result;
    }

    /**
     * @param $str
     * @param bool $output_html
     * @return array|bool|null|string
     * @throws TemplateException
     */
    public function evaString($str,$output_html=false)
    {
        $parser = new TemplateVarParser($str,$this->system_Encoding);
        try{
            if($parser->getFirst()) {
                $result = $this->convertTemplateVar($parser->getFirst(), true);
                if ($output_html && ($this->default_modifiers != "")) {
                    if ($parser->getFirst()->getType() != TemplateVarNode::$TYPE_FUNCTION) {
                        $node = new TemplateVarNode(TemplateVarNode::$TYPE_FUNCTION, $this->default_modifiers);
                        $node->addParam(new TemplateVarNode(TemplateVarNode::$TYPE_VAR, '"' . str_replace('"', '\"', $result) . '"'));
                        $result = $this->convertNodeToFunction($node, false);
                    }
                }
            }else{
                $this->notice('not found string var "'.$str.'"');
                $result = null;
            }
        }catch (TemplateException $e){
            $this->error($e->getMessage());
        }
        return $result;
    }

    /**
     * @return mixed
     */
    static public function callFilter()
    {
        if(func_num_args() > 0){
            $name = func_get_arg(0);
            $params = array();
            for($i=1;$i<func_num_args();$i++){
                $params[] = func_get_arg($i);
            }
            if ( is_callable( self::$Functions[$name] ) ) {
                try{
                    $result = call_user_func_array(self::$Functions[$name],$params);
                }catch (TemplateException $e){
                    new TemplateException('Template : Error Functions '.$name.'('.implode(",",$params).')');
                }
            }else{
                new TemplateException('Template : Error Functions '.$name.'('.implode(",",$params).')');
            }
        }else{
            new TemplateException('Template : Error Functions Must Be paramater.');
        }
        return $result;
    }

    /**
     * @param $func
     * @param $val
     * @return bool
     */
    private function convertNodeToFunction(TemplateVarNode $node)
    {
        $params = array();
        $error = null;
        foreach($node->getParams() as $val){
            try{
                $params[] = $this->convertTemplateVar($val,false);
            }catch (TemplateException $e){
                $params[] = null;
                $error = $e;
            }
        }
        if($node->getName() != 'isset'){
            if($error){
                throw $error;
            }
        }
        // if object
        if (preg_match("/^\\\$([\[\]_a-zA-Z0-9\.\\\$]+)$/", $node->getName(), $matchs)) {
            $vlist = explode('.',$matchs[1]);
            if(count($vlist) > 1){
                $str = array_slice($vlist,0,-1);
                $func = $vlist[count($vlist) - 1];
                $obj = $this->getStringVariable(implode('.',$str),$this->Vars,false);
                if(is_object($obj)){
                    $func = 'get'.ucfirst($func);
                    $result = call_user_func_array(array($obj,$func),$params);
                }else{
                    $this->error('not found object function '.$node->getName());
                }
            }else{
                $this->error('parse Error function '.$node->getName());
            }
        }else if(isset(self::$Functions[$node->getName()])){
            if($error){
                throw $error;
            }
            if ( is_callable( self::$Functions[$node->getName()] ) ) {
                try{
                    $result = call_user_func_array(self::$Functions[$node->getName()],$params);
                }catch (TemplateException $e){
                    $this->error('Error Functions '.$node->getName().'('.implode(",",$node->getParams()).')');
                }
            }else{
                $this->error('Error Functions '.$node->getName().'('.implode(",",$node->getParams()).')');
            }
        }else{
            switch ($node->getName()) {
                case 'isset':
                    $result = ($params[0] ? true : false);
                    $error = null;
                    break;
                case 'is_array':
                    $result = is_array($params[0]);
                    break;
                case 'is_numeric':
                    $result = is_numeric($params[0]);
                    break;
                case 'is_string':
                    $result = is_string($params[0]);
                    break;
                case 'in_array':
                    $result = in_array($params[0],$params[1]);
                    break;
                case 'boolval':
                    $result = boolval($params[0]);
                    break;
                case 'intval':
                    $result = intval($params[0]);
                    break;
                case 'strval':
                    $result = strval($params[0]);
                    break;
                case 'floatval':
                    $result = floatval($params[0]);
                    break;
                case 'implode':
                    $result = implode($params[0],$params[1]);
                    break;
                case 'explode':
                    $result = explode($params[0],$params[1]);
                    break;
                case 'array_filter':
                    if(isset($params[1])){
                        $result = array_filter($params[0],$params[1]);
                    }else{
                        $result = array_filter($params[0]);
                    }
                    break;
                case 'upper':
                    $result = strtoupper($params[0]);
                    break;
                case 'lower':
                    $result = strtolower($params[0]);
                    break;
                case 'ucfirst':
                    $result = ucfirst($params[0]);
                    break;
                case 'lcfirst':
                    $result = lcfirst($params[0]);
                    break;
                case 'escape':
                    if(!is_object($params[0])){
                        $result = htmlspecialchars($params[0], ENT_QUOTES, $this->system_Encoding);
                    }else{
                        $result = null;
                    }
                    break;
                case 'htmlentities':
                    if(!is_object($params[0])){
                        $result = htmlentities($params[0], ENT_COMPAT, $this->system_Encoding);
                    }else{
                        $result = null;
                    }
                    break;
                case 'htmlspecialchars':
                    if(!is_object($params[0])){
                        $result = htmlspecialchars($params[0], ENT_COMPAT, $this->system_Encoding);
                    }else{
                        $result = null;
                    }
                    break;
                case 'escape_br':
                case 'nl2br':
                    if(!is_object($params[0])){
                        $result = htmlentities($params[0], ENT_COMPAT, $this->system_Encoding);
                        $result = nl2br($result);
                    }else{
                        $result = null;
                    }
                    break;
                case 'strip_tags':
                    $result = strip_tags($params[0],(array_key_exists(1,$params) ? $params[1] : ""));
                    break;
                case 'print_r':
                    $result = print_r($params[0], true);
                    break;
                case 'dump':
                    ob_start();
                    print '<pre>';
                    var_dump($params[0]);
                    print '</pre>';
                    $result = ob_get_contents();
                    ob_end_clean();
                    break;
                case 'quotes':
                    if(!is_object($params[0])){
                        $result = preg_replace("/\"/", "\\\"", $params[0]);
                    }else{
                        $result = null;
                    }
                    break;
                case 'urlencode':
                    if(!is_object($params[0])){
                        $result = urlencode($params[0]);
                    }else{
                        $result = null;
                    }
                    break;
                // format
                case 'number_format':
                    $result = number_format($params[0]);
                    break;
                case 'strimw':
                    $str = $params[0];
                    $start = $params[1];
                    $width = $params[2];
                    $append = (array_key_exists(3,$params) ? $params[3] : "");
                    $result = mb_strimwidth($str,$start,$width,$append,$this->system_Encoding);
                    break;
                case 'count':
                    $result = count($params[0]);
                    break;
                case 'set':
                    if(!is_object($params[0]) && (count($params) > 1)){
                        $this->assign($params[0],$params[1]);
                    }else{
                        $this->error('error set() paramater "'.$params[0].'" or paramater count.');
                    }
                    $result = "";
                    break;
                case 'e':
                    $result = __($params[0]);
                    break;
                case 'nofilter':
                    $result = $params[0];
                    break;
                case 'rest':
                    $result = '';
                    if($node && ($node->getParam(0)) && ($node->getParam(0)->getParam(0))){
                        $output = $this->convertNodeToVar($node->getParam(0)->getParam(0),$this->outputVars,false);
                    }else{
                        $this->error('rest() paramater not found');
                    }
                    if(is_array($params[0])){
                        foreach($params[0] as $k => $v){
                            if(!is_array($output) || !isset($output[$k])){
                                $result .= $v;
                            }
                        }
                    }else{
                        if(!$output){
                            $result .= $params[0];
                        }
                    }
                    break;
                case 'date':
                    if(array_key_exists(1,$params)){
                        if(is_numeric($params[1])){
                            $time = $params[1];
                        }else{
                            $time = strtotime((string)$params[1]);
                        }
                    }else{
                        $time = time();
                    }
                    if($time <= 0){
                        $this->error('date() paramater is string or number.'.$params[1].' given.');
                    }
                    $result = date($params[0],$time);
                    break;
                case 'time':
                    $result = time();
                    break;
                default:
                    $this->error('Error Functions '.$node->getName());
                    break;
            }
            if($error){
                throw $error;
            }
        }
        return $result;
    }

    /**
     * set template include
     * @return srting $template
     * @access private
     */
    public function setImportTemplate($template)
    {
        // load extend file
        $template = $this->_setExtendTemplate($template);
        // replace block
        $template = $this->_setBlockTemplate($template);
        // load include file
        $preg_str = "/" . preg_quote($this->left_delimiter, "/") . "IMPORT\s+(.+?)" . preg_quote($this->right_delimiter, "/") . "/i";
        $template = preg_replace_callback($preg_str, array($this, '_setImportCallback'), $template);

        return $template;
    }

    /**
     * @param $template
     * @return mixed
     */
    private function _setExtendTemplate($template)
    {
        $preg_str = "/^([\s\S]*?" . preg_quote($this->left_delimiter, "/") . "EXTEND\s+(.+?)" . preg_quote($this->right_delimiter, "/") . "[\s\S]*?)$/i";
        $tmp = preg_replace_callback($preg_str, array($this, '_setExtendCallback'), $template);

        // load block file
        $tmp2 = $this->_setBlockData($template);
        if(!preg_match($preg_str,$template)){
            $tmp = $tmp2;
        }
        return $tmp;
    }

    /**
     * @param $args
     * @return string
     */
    private function _setExtendCallback($args)
    {
        $tmp_base = $args[1];

        $var = $args[2];
        // 属性値を取得
        $attr = $this->getAttr($attr, $var);
        // 属性から値設定
        $tmp = "";
        foreach ($attr as $name => $val) {
            switch ($name) {
                case "FILE":
                    $tmp = $this->loadTemplatePartsFile($val);
                    // extend
                    $tmp = $this->_setExtendTemplate($tmp);
                    break;
            }
        }

        return $tmp;
    }

    /**
     * @param $template
     */
    private function _setBlockData($template){

        $preg_str = "/" . preg_quote($this->left_delimiter, "/") . "(\/?block([\s]+[^\s\/]+?)?)" . preg_quote($this->right_delimiter, "/") . "/i";
        // explode
        $matchs = preg_split($preg_str, $template, 0, PREG_SPLIT_DELIM_CAPTURE);
        //$this->TemplateList = $matchs;
        $cnt = count($matchs);
        // テンプレートを評価
        $tmp = $matchs[0];

        $t = "";
        $level = 0;
        for ($key = 1; $key < $cnt;) {
            $tmp .= $this->_setBlockTags(false, $t, $key, $matchs, $level);
        }
        if($level != 0){
            $this->error('Error Block');
        }
        return $tmp;
    }

    /**
     * @param $template
     * @return mixed
     */
    private function setStripTemplate($template){

        $tag_id = "strip";
        $preg_str = "/".preg_quote($this->left_delimiter,"/")."(\/?".$tag_id.")".preg_quote($this->right_delimiter,"/")."/";
        // 文字列の分割
        $matchs = preg_split($preg_str, $template, 0, PREG_SPLIT_DELIM_CAPTURE);

        $tpl = "";
        $cnt = count($matchs);
        $lebel = 0;
        for($i=0;$i<$cnt;$i+=2){
            if($lebel > 0){
                $t = preg_replace_callback(
                    "/(" . preg_quote($this->left_delimiter, "/") . "\/?[^" . preg_quote($this->right_delimiter, "/") . "]+" . preg_quote($this->right_delimiter, "/") . ")".
                    "|".
                    "([^" . preg_quote($this->left_delimiter, "/") . "]+)/",
                    function($mt){
                        if(isset($mt[2])){
                            $t = $mt[2];
                            $t = preg_replace("/[\r\n\t]/","",$t);
                            $t = preg_replace("/(\s){2,}/","$1",$t);
                            return $t;
                        }
                        return $mt[1];
                    },
                    $matchs[$i]);
                $tpl .= $t;
            }else{
                $tpl .= $matchs[$i];
            }
            if(($i + 1) >= $cnt){
                continue;
            }
            if($matchs[$i + 1] == $tag_id){
                $lebel ++;
            }else if($matchs[$i + 1] == "/".$tag_id){
                $lebel = max($lebel - 1,0);
            }
        }
        return $tpl;
    }

    /**
     * @param $matchs
     * @return mixed
     */
    private $current_block;
    private function _setBlockParentCallback($matchs){
        if(isset($this->Block[$this->current_block])){
            return $this->Block[$this->current_block];
        }
        $this->error('not found parent() block "'.$this->current_block.'".');
    }

    /**
     * @param $block_key
     * @param $tmp
     * @param $key
     * @param $list
     * @param $level
     * @return string
     */
    private function _setBlockTags($block_key, $tmp, &$key, &$list, &$level)
    {
        if(count($list) <= $key){
            return "";
        }
        $type = $list[$key];
        $template = "";
        if(preg_match("/^block\s+(.+)$/i",$type,$matchs)){
            $block_key = trim($matchs[1]);
            $block_tmp = $list[$key + 2];
            $c_level = $level + 1;
            $key += 3;

            $this->current_block = $block_key;
            $preg_str = "/".preg_quote($this->left_delimiter,"/")."\s*parent\(\s*?\)\s*".preg_quote($this->right_delimiter,"/")."/";
            $block_tmp = preg_replace_callback($preg_str,array($this,'_setBlockParentCallback'),$block_tmp);

            $template .= $this->left_delimiter.$type.$this->right_delimiter;
            while($key < count($list)){
                if($c_level == $level){
                    break;
                }
                $block_tmp .= $this->_setBlockTags($block_key,$block_tmp,$key,$list,$c_level);
            }
        }else if($type == "/block"){
            if($block_key != ""){
                if(isset($this->Block[$block_key])){
                    $this->Block[$block_key] = new TemplateBlock($tmp,$this->Block[$block_key]);
                }else{
                    $this->Block[$block_key] = new TemplateBlock($tmp);
                }
            }else{
                $this->error('not input block key.');
            }
            $level --;
            $key ++;
        }else{
            $template .= $type;
            $key ++;
        }
        return $template;
    }
    /**
     * @param $template
     * @return string
     */
    private function _setBlockTemplate($template){
        $preg_str = "/" .preg_quote($this->left_delimiter, "/")."BLOCK[\s]+(.+?)" . preg_quote($this->right_delimiter, "/")."/i";
        return preg_replace_callback($preg_str, array($this, '_setBlockCallback'), $template);
    }

    /**
     * @param $args
     * @return string
     */
    private function _setBlockCallback($args)
    {
        $var = $args[1];
        if(isset($this->Block[$var])){
            return $this->_setBlockTemplate($this->Block[$var]);
        }
        $this->error('not found block "'.$var.'".');
        return "";
    }

    /**
     * @param $args
     * @return string
     */
    private function _setImportCallback($args)
    {
        $var = $args[1];
        // 属性値を取得
        $attr = $this->getAttr($attr, $var);
        // 属性から値設定
        $tmp = "";
        foreach ($attr as $name => $val) {
            switch ($name) {
                case "FILE":
                    $tmp = $this->loadTemplatePartsFile($val);
                    break;
            }
        }
        return $tmp;
    }

    /**
     * @param $template
     * @return mixed
     */
    private function setInclude($template)
    {
        //$preg_str = "/([\s\S]*?)".preg_quote($this->left_delimiter)."INCLUDE\s+([\s\S]+?)".preg_quote($this->right_delimiter)."([\s\S]*?)/i";
        $preg_str = "/".preg_quote($this->left_delimiter,"/")."INCLUDE\s+([\s\S]+?)".preg_quote($this->right_delimiter,"/")."/i";
        $template = preg_replace_callback( $preg_str , array($this, '_setIncludeCallback') , $template);
        return $template;
    }

    /**
     * @param $args
     * @return string
     */
    private function _setIncludeCallback($args)
    {
        //$tp_header = $args[1];
        //$var = $args[2];
        //$tp_footer = $args[3];
        $var = $args[1];
        // 属性値を取得
        $attr = $this->getAttr($attr,$var);
        // 属性から値設定
        $tmp = "";
        foreach($attr as $name => $val){
            switch($name){
                case "FILE":
                    $tmp = $this->Include[$val];
                    break;
            }
        }
        //$template = $tp_header.$tmp.$tp_footer;
        //return $template;
        return $tmp;
    }

    /**
     * set template value
     * @return srting $template
     * @access private
     */
    public function setTemplatesVars($template)
    {
        $preg_str = "/" . preg_quote($this->left_delimiter, "/") . "(.+?)" . preg_quote($this->right_delimiter, "/") . "/";
        // 文字列の分割
        $matchs = preg_split($preg_str, $template, 0, PREG_SPLIT_DELIM_CAPTURE);
        $this->TemplateList = $matchs;
        $cnt = count($matchs);
        // テンプレートを評価
        $tmp = $matchs[0];
        $level = 0;
        for ($key = 1; $key < $cnt; $key += 2) {
            $this->_setTemplateTags($tmp, $key, $matchs, $level);
        }
        return $tmp;
    }

    /**
     * @param $tmp
     * @param $key
     * @param $list
     * @param $level
     * @param bool $check
     * @param bool $skip
     * @return bool
     */
    private function _setTemplateTags(&$tmp, &$key, &$list, &$level, $check = false, $skip = false)
    {
        // 式を取得
        $ptn = $list[$key];
        // comment
        if (preg_match("/^#(.*)#$/i", $ptn, $m)) {
            $tmp .= $list[$key + 1];
            // literal
        }else if (preg_match("/^LITERAL$/i", $ptn, $m)) {
            $tmp .= $list[$key + 1];
            // tag skip
            $this->_skipLiteralTags($tmp, $key, $list, $level);
            // foreach
        }else if (preg_match("/^FOREACH\s+(.+)$/i", $ptn, $m)) {
            $level++;
            if ($skip == false) {
                // loop
                $this->_setForeachLoop($m[1], $tmp, $key, $list, $level);
            } else {
                // tag skip
                $this->_skipIfTags($tmp, $key, $list, $level);
            }
        } else if (preg_match("/^\/FOREACH$/i", $ptn, $m)) {
            $level--;
            // for文
        } else if (preg_match("/^FOR\s+(.+)$/i", $ptn, $m)) {
            $level++;
            if ($skip == false) {
                // loop
                $this->_setForLoop($m[1], $tmp, $key, $list, $level);
            } else {
                // tag skip
                $this->_skipIfTags($tmp, $key, $list, $level);
            }
        } else if (preg_match("/^\/FOR$/i", $ptn, $m)) {
            $level--;
            // if文
        } else if (preg_match("/^IF\s*\(\s*(.+)\s*\)\s*$/i", $ptn, $m)) {
            $level++;
            if ($skip == false) {
                // IF処理
                $this->_setIf($m[1], $tmp, $key, $list, $level);
            } else {
                // tag skip
                $this->_skipIfTags($tmp, $key, $list, $level);
            }
            // if end
        } else if (preg_match("/^\/IF$/i", $ptn, $m)) {
            $level--;
            // 無効な値は無視
        } else {
            if ($skip == false) {
                $var = $this->evaString($ptn,true);
                $tmp .= $var;
                $tmp .= $list[$key + 1];
            }
        }
        return $check;
    }

    /**
     * @param $tmp
     * @param $key
     * @param $list
     * @param $level
     */
    private function _skipLiteralTags(&$tmp, &$key, &$list, &$level)
    {
        $cnt = count($list);
        for ($key += 2; $key < $cnt; $key += 2) {
            $ptn = $list[$key];
            if(preg_match("/^\/LITERAL$/i", $ptn, $m)){
                $tmp .= $list[$key + 1];
                break;
            }
            $tmp .= $this->left_delimiter.$ptn.$this->right_delimiter;
            $tmp .= $list[$key + 1];
        }
    }

    /**
     * @param $tmp
     * @param $key
     * @param $list
     * @param $level
     */
    private function _skipIfTags(&$tmp, &$key, &$list, &$level)
    {
        $cnt = count($list);
        $start_level = $level - 1;
        for ($key += 2; $key < $cnt; $key += 2) {
            // tag
            $this->_setTemplateTags($tmp, $key, $list, $level, true, true);
            // level check
            if ($level <= $start_level) {
                break;
            }
        }
    }

    /**
     * set template FOR
     * @access private
     */
    private function _setForLoop($str, &$tmp, &$key, &$list, &$level)
    {
        // 要素を抽出
        if(!preg_match("/^\s*\\\$([\S]+)\s*=\s*([\S]+)\s+TO\s+(\S+)\s*(([\S\s]+?)\s*)?$/i", $str, $m)){
            $this->error('un support format "for '.$str.'"');
        }
        $name = $m[1];
        $start_p = (new TemplateVarParser($m[2],$this->system_Encoding));
        $loop_p = (new TemplateVarParser($m[3],$this->system_Encoding));
        $start = $this->convertTemplateVar($start_p->getFirst(),true);
        $loop = $this->convertTemplateVar($loop_p->getFirst(),true);
        $step = 1;
        if (isset($m[5])) {
            $at = $m[5];
            // 属性値を取得
            $attr = $this->getAttr($attr, $at);
            if (isset($attr["STEP"])) {
                $step = intval($attr["STEP"]);
            }
        }
        if($step == 0){
            $this->error('un support format "for '.$str.'" by step is not 0.');
        }
        $start_key = $key;
        $start_level = $level - 1;
        $cnt = count($list);
        if (($step > 0) ? ($start <= $loop) : ($start >= $loop)) {
            $i = $start;
            while(($step > 0) ? ($i <= $loop) : ($i >= $loop)){
                $this->assign($name, $i);
                $tmp .= $list[$key + 1];
                $key += 2;
                while ($key < $cnt) {
                    // タグの実装
                    $this->_setTemplateTags($tmp, $key, $list, $level);
                    // レベルチェック
                    if ($level <= $start_level) {
                        if (($step > 0) ? (($i + $step) <= $loop) : (($i + $step) >= $loop)) {
                            // キー値を戻す
                            $key = $start_key;
                            $level++;
                        }
                        break;
                    }
                    $key += 2;
                };
                $i += $step;
            }
            $tmp .= $list[$key + 1];
        } else {
            // tag skip
            $this->_skipIfTags($tmp, $key, $list, $level);
            $tmp .= $list[$key + 1];
        }
    }

    /**
     * set template FOREACH
     * @access private
     */
    private function _setForeachLoop($str, &$tmp, &$key, &$list, &$level)
    {
        // 要素を抽出
        preg_match("/^\s*([\S]+)\s+AS\s+(.+?)\s*$/i", $str, $m);
        $item_p = (new TemplateVarParser($m[1],$this->system_Encoding));
        $item_list = $this->convertTemplateVar($item_p->getFirst(),true);
        $loop_key = "";
        $name = "";
        $s = $m[2];
        if (preg_match("/^\\\$([\S]+)\s*=>\s*\\\$([\S]+)$/", $s, $m)) {
            $loop_key = $m[1];
            $name = $m[2];
        } elseif (preg_match("/^\\\$([\S]+)$/", $s, $m)) {
            $name = $m[1];
        }
        // section
        $start_key = $key;
        $start_level = $level - 1;
        $cnt = count($list);
        $item_cnt = count($item_list);
        //if ($item_list != null && !is_array($item_list)){
        //$this->error("template : foreach value ".$str." is not array;");
        if (0 < $item_cnt && is_array($item_list)) {
            $i = 0;
            foreach ($item_list as $item_key => $item) {
                if ($loop_key != "") {
                    $this->assign($loop_key, $item_key);
                }
                $this->assign($name, $item);

                $tmp .= $list[$key + 1];
                $key += 2;
                while ($key < $cnt) {
                    // タグの実装
                    $this->_setTemplateTags($tmp, $key, $list, $level);
                    // level check
                    if ($level <= $start_level) {
                        if (($i + 1) < $item_cnt) {
                            // キー値を戻す
                            $key = $start_key;
                            $level++;
                        }
                        break;
                    }
                    $key += 2;
                };
                $i++;
            }
            $tmp .= $list[$key + 1];
        } else {
            // tag skip
            $this->_skipIfTags($tmp, $key, $list, $level);
            $tmp .= $list[$key + 1];
        }
    }

    /**
     * set template IF
     * @access private
     */
    private function _setIf($str, &$tmp, &$key, &$list, &$level)
    {
        // if処理
        $check = $this->_setIfExecute($str, $tmp, $key, $list, $level);

        $start_level = $level - 1;
        $cnt = count($list);
        $loop = 0;
        for (; $key < $cnt; $key += 2) {
            //echo str_repeat("　",$level).htmlspecialchars($list[$key])."A<br/>";
            // 式を取得
            $ptn = $list[$key];
            // elseif文
            if (preg_match("/^(\/?)ELSE\s*IF\s*\(\s*(.+?)\)$/i", $ptn, $m)) {
                //echo "skip";
                if ($check == false) {
                    // if文法処理
                    if ($check = $this->_setIfExecute($m[2], $tmp, $key, $list, $level, $check)) {
                    }
                    $key -= 2;
                    continue;
                } else {
                    // tag skip
                    $this->_skipIfTags($tmp, $key, $list, $level);
                }
                // else文
            } else if (preg_match("/^(\/?)ELSE\s*$/i", $ptn, $m)) {
                if ($check == false) {
                    // if文法処理
                    if ($check = $this->_setIfExecute(NULL, $tmp, $key, $list, $level, $check)) {
                    }
                    $key -= 2;
                    continue;
                } else {
                    // tag skip
                    $this->_skipIfTags($tmp, $key, $list, $level);
                }
            } else {
                $this->_setTemplateTags($tmp, $key, $list, $level, $check, ($check == false));
            }
            // leevl check
            if ($level <= $start_level) {
                $tmp .= $list[$key + 1];
                break;
            }
            $loop++;
        }
    }

    /**
     * set template IF/ELSEIF/ELSE
     * @access private
     */
    private function _setIfExecute($str, &$tmp, &$key, &$list, &$level, $checked = false)
    {
        // チェック
        $check = true;
        if ($str != NULL) {
            $check = $this->_setIfCheck($str);
        }
        if ($check && $checked == false) {
            $tmp .= $list[$key + 1];
        }
        $key += 2;
        return ($check | $checked);
    }

    /**
     * @param $str
     * @return bool
     */
    private function _setIfCheck($str)
    {
        $check = false;
        if ($res = $this->evaString($str)) {
            $check = true;
        }
        return $check;
    }
}