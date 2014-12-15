<?php

/**
 * Class class_pnkzItem
 */
class class_pnkzItem
{
    protected $url;
    protected $name;

    function __construct($name,$url=null)
    {
        $this->name = $name;
        $this->url = $url;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }
}

/**
 * Class class_pnkz
 */
class class_pnkz
{
    protected $wrapElement;
    protected $wrapStart;
    protected $wrapEnd;
    protected $linkClass;
    protected $items;

    function __construct()
    {
        $this->wrapElement = 'li';
        $this->wrapStart = null;
        $this->wrapEnd = null;
        $this->linkClass = null;
        $this->items = array();
    }

    /**
     * @param $elm
     * @return $this
     */
    public function setWrapElement($elm)
    {
        $this->wrapElement = $elm;
        return $this;
    }

    /**
     * @param $start
     * @param $end
     * @return $this
     */
    public function setWrapHTML($start,$end)
    {
        $this->wrapStart = $start;
        $this->wrapEnd = $end;
        return $this;
    }

    /**
     * @param $class
     * @return $this
     */
    public function setLinkClass($class)
    {
        $this->linkClass = $class;
        return $this;
    }

    /**
     * @param class_pnkzItem $item
     * @return $this
     */
    public function addItem(class_pnkzItem $item)
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * @param $key
     * @param class_pnkzItem $item
     * @return $this
     */
    public function setItem($key,class_pnkzItem $item)
    {
        $this->items[$key] = $item;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getHTML()
    {
        $count = count($this->items);
        // 一覧に設定
        $list = "";
        $i = 0;
        foreach ($this->items as $key => $item) {
            if ($this->wrapStart) {
                $list .= $this->wrapStart;
            }
            $link = false;
            if ((($i + 1) < $count) && ($item->getUrl())) {
                $link = true;
            }
            if ($this->wrapElement) {
                $class = 'li'.$i;
                if($this->linkClass){
                    $class .= ' '.$this->linkClass;
                }
                $list .= '<'.$this->wrapElement.' class="'.$class.'">';
            }
            if ($link) {
                $list .= '<a href="' . htmlspecialchars($item->getUrl()) . '">' . htmlspecialchars($item->getName()) . '</a>';
            } else {
                $list .= '<span>' . htmlspecialchars($item->getName()) . '</span>';
            }
            if ($this->wrapElement) {
                $list .= '</'.$this->wrapElement.'>';
            }
            if ($this->wrapEnd) {
                $list .= $this->wrapEnd;
            }
            $i++;
        }
        return $list;
    }
}