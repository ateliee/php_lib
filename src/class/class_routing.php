<?php

/**
 * Class RoutingRule
 */
class RoutingRule{
    private $rule;
    private $value;
    private $params = array();
    private $no;

    /**
     * @param $rule
     * @param $value
     * @param int $no
     */
    function __construct($rule,$value,$params=array(),$no=0){
        $this->rule = $rule;
        $this->value = $value;
        $this->params = $params;
        $this->no = $no;
    }

    /**
     * @return mixed
     */
    public function getRule(){
        return $this->rule;
    }

    /**
     * @return mixed
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getNo(){
        return $this->no;
    }

    /**
     * @return array
     */
    public function getParams(){
        return $this->params;
    }

    /**
     * @param $rule
     * @return mixed
     */
    public function setRule($rule){
        $this->rule = $rule;
        return $this->rule;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setValue($value){
        $this->value = $value;
        return $this->value;
    }

    /**
     * @param $no
     * @return mixed
     */
    public function setNo($no){
        $this->no = $no;
        return $this->no;
    }

    /**
     * @param $params
     * @return array
     */
    public function setParams($params){
        $this->params = $params;
        return $this->params;
    }
}

/**
 * Class RoutingRuleMatch
 */
class RoutingRuleMatch extends RoutingRule{
    private $params;
    /**
     * @param RoutingRule $rule
     * @param array $param
     */
    function __construct(RoutingRule $rule,$params = array()){
        parent::__construct($rule->getRule(),$rule->getValue(),$rule->getNo());

        $params_list = $rule->getParams();
        $keys = array_keys($params_list);

        $i = 0;
        $this->params = array();
        foreach($params_list as $key => $p){
            $val = $p;
            if(isset($params[$i])){
                $val = $params[$i];
            }
            $this->params[$key] = $val;
            $i ++;
        }
    }
}

/**
 * Class class_routing
 */
class class_routing{
    private $rules;

    /**
     *
     */
    function __construct()
    {
        $this->rules = array();
    }

    /**
     * @param $rule
     * @param $val
     * @param $no
     * @return $this
     */
    public function addRule($rule,$val,$params=array(),$no=0)
    {
        return $this->addRoutingRule(new RoutingRule($rule,$val,$params,$no));
    }

    /**
     * @param RoutingRule $rule
     * @return $this
     */
    public function addRoutingRule(RoutingRule $rule)
    {
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param $url
     * @return RoutingRuleMatch|null
     */
    public function getUrlRule($url)
    {
        $rules = array();
        foreach($this->rules as $key => $val){
            $r = $val->getRule();
            $r2 = ((substr($r,-1) == '/') ? substr($r,0,strlen($r) - 1) : $r.'/');
            if(preg_match('/^'.str_replace('/','\/',$r).'$/',$url,$matchs) || preg_match('/^'.str_replace('/','\/',$r2).'$/',$url,$matchs)){
                if(!isset($rules[$val->getNo()])){
                    $params = array_slice($matchs,1);
                    $rules[$val->getNo()] = new RoutingRuleMatch($val,$params);
                }
            }
        }
        if(count($rules) > 0){
            ksort($rules);
            foreach($rules as $rule){
                return $rule;
            }
        }
        return null;
    }
}