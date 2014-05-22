<?php
/**
 * OpenSlopeOne Class File
 * 
 * This is the main file of openslopeone
 * @author Chaoqun Fu <fuchaoqun@gmail.com>
 * @version 1.0
 * @since 2008-09-10
 * @copyright Chaoqun Fu <fuchaoqun@gmail.com>
 * @license GPL V3 
 */


require_once ('database.php'); 


class OpenSlopeOne
{
    /**
     * Database link
     *
     * @var resource
     */
    var $_db;
    
    /**
     * Config
     *
     * @var array
     */
    public $_config;
    
    /**
     * Counstuctor
     *
     * @param array $config
     */
    function __construct($config = '')
    {
        /**
         * Init config
         */
        empty($config) ? $this->initConfig() : $this->_config = $config;
        
        /**
         * Init database link
         */
        $this->_initDb();
    }
    
    /**
     * Init database link
     *
     * @param array $config
     * @return resource
     */
    public function _initDb()
    {
        /**
         * Singleton Pattern
         */
        if (is_resource($this->_db)) return $this->_db;
        
        /**
         * Init database config
         */


        $config = array(
            'host' => $this->_config['host'],
            'username' => $this->_config['username'],
            'password' => $this->_config['password'],
            'dbname' => $this->_config['dbname'],
            'port' => $this->_config['port']
        );        
        $adapter = $this->_config['adapter'];
        
        return $this->_db;
    }
    
    /**
     * Init config
     *
     * @param string $configIniFile
     */
    public function initConfig($configIniFile = 'config.ini.php')
    {
        $this->_config = parse_ini_file($configIniFile);
    }
    
    /**
     * Init SlopeOneTable
     *
     * Use factory pattern
     * Specify the mode use 'PHP' or 'MySQL'
     * If you user 'PHP' mode, it's a pure php implementation, and it might be very slow
     * You can use 'MySQL' mode, it's based on mysql procedure, and it will be mutch faster.
     * @param string $factory
     */
    public function initSlopeOneTable($factory = 'PHP')
    {
        set_time_limit(0);
        
        /**
         * If the mode is not PHP or MySQL, then it will be set as 'PHP'
         */
        ($factory != 'PHP' && $factory != 'MySQL') && ($factory = 'PHP');
        
        /**
         * Delete all the data of oso_slope_one
         */
	$Database=new DateBase ($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);  
	$Database->Connect(); 
	$Database->Query='TRUNCATE TABLE `oso_slope_one`'; 
	$Database->Query();        
	$Database->Close();
        
        /**
         * Form the function
         */
        $func = '_initSlopeOneTableBy' . $factory;
        
        /**
         * Execute the function
         */
        $this->$func();
    }
    
    /**
     * Init SlopeOneTable By PHP
     *
     * A pure php implementation, use it just for fun.
     */
    public function _initSlopeOneTableByPHP()
    {        
        /**
         * Get distinct item_id
         */
        $sql = 'SELECT DISTINCT item_id FROM oso_user_ratings';
	$Database=new DateBase ($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);  
	$Database->Connect(); 
	$Database->Query=$sql; 
	$Database->Query();     

	while ($row = mysql_fetch_array($Database->queryResult)) {
          	$slopeOneSql = 'insert into oso_slope_one (select a.item_id as item_id1,b.item_id as item_id2,count(*) as times, sum(a.rating-b.rating) as rating from oso_user_ratings a,oso_user_ratings b where a.item_id = '
                         .$row[0].' and b.item_id != a.item_id and a.user_id=b.user_id group by a.item_id,b.item_id)';
		$Database->Query=$slopeOneSql; 
        }
    }
    
    /**
     * Init SlopeOneTable By MySQL
     *
     * A MySQL procedure implementation, use it in production environment
     * You can also call the procedure in shell
     */
    private function _initSlopeOneTableByMySQL()
    {
        if (!$this->_hasProcedure())
        {
            $this->_createProcedure();
        }
	$Database=new DateBase ($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);  
	$Database->Connect(); 
	$Database->Query='call slope_one'; 
	$Database->Query(); 
	$Database->Close(); 
    }
    
    /**
     * Check if exists the procedurn
     *
     * @return boolean
     */
    private function _hasProcedure()
    {
        $sql = 'show procedure status where Db = "' . $this->_config['dbname'] . '" and name= "slope_one"';
	$Database=new DateBase ($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);  
	$Database->Connect(); 
	$Database->Query=$sql; 
	$Database->Query(); 
	$result=mysql_fetch_array($Database->queryResult);  
	$Database->Close(); 
	return !empty($result) ? true : false;
    }
    
    /**
     * Create procedure
     *
     */
    private function _createProcedure()
    {
        $sql = '
            CREATE PROCEDURE `slope_one`()
                begin                    
                    DECLARE tmp_item_id int;
                    DECLARE done int default 0;                    
                    DECLARE mycursor CURSOR FOR select distinct item_id from oso_user_ratings;
                    DECLARE CONTINUE HANDLER FOR NOT FOUND set done=1;
                    open mycursor;
                    while (!done) do
                        fetch mycursor into tmp_item_id;
                        if (!done) then
                            insert into oso_slope_one (select a.item_id as item_id1,b.item_id as item_id2,count(*) as times, sum(a.rating-b.rating) as rating from oso_user_ratings a,oso_user_ratings b where a.item_id = tmp_item_id and b.item_id != a.item_id and a.user_id=b.user_id group by a.item_id,b.item_id);
                        end if;
                    END while;
                    close mycursor;
                end
        ';
	$Database=new DateBase ($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);  
	$Database->Connect(); 
	$Database->Query=$sql; 
	$Database->Query(); 
	$Database->Close(); 
    }

    /**
     * Insert new data
     *
     * @param int $userId
     * @param int $itemId
     * @param int $rating
     * @return boolean
     */
    public function selectDataUserId($userId, $rating)
    {
        $sql = "select * from oso_user_ratings where user_id='".$userId."' and rating='".$rating."'";
	$Database=new DateBase ($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);  
	$Database->Connect(); 
	$Database->Query=$sql; 
	$Database->Query(); 
	while ($row = mysql_fetch_array($Database->queryResult)) {
		$items[]=$row;
        }
	$Database->Close(); 
	return $items;  
    }
 
    /**
     * Insert new data
     *
     * @param int $userId
     * @param int $itemId
     * @param int $rating
     * @return boolean
     */
    public function insertNewData($userId, $itemId,$rating)
    {
        $sql = "insert into oso_user_ratings (user_id, item_id, rating) values ('".$userId."', '".$itemId."', '".$rating."')";
	$Database=new DateBase ($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);  
	$Database->Connect(); 
	$Database->Query=$sql; 
	$Database->Query();   
	$Database->Close(); 
	return true;
    }
   
    /**
     * Get recommended items by item's id 
     *
     * @param int $itemId
     * @param int $limit
     * @return array
     */
    public function getRecommendedItemsById($itemId, $limit = 20)
    {
        $sql = 'select item_id2 from oso_slope_one where item_id1 = '
             . $itemId
             . ' group by item_id2 order by sum(rating/times) limit '
             . $limit;

	$Database=new DateBase ($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);  
	$Database->Connect(); 
	$Database->Query=$sql; 
	$Database->Query();     

	$i=0;
	while ($row = mysql_fetch_array($Database->queryResult)) {
		$items[$i]=$row[0];$i++;
        }
	$Database->Close(); 
	return $items;
    }
    
    /**
     * Get recommended items by user's id
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getRecommendedItemsByUser($userId, $limit = 20)
    {

        $sql = 'select s.item_id2 from oso_slope_one s,oso_user_ratings u where u.user_id = '
             . $userId
             . ' and s.item_id1 = u.item_id and s.item_id2 != u.item_id group by s.item_id2 order by sum(u.rating * s.times - s.rating)/sum(s.times) desc limit '
             . $limit;
	$Database=new DateBase ($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);  
	$Database->Connect(); 
	$Database->Query=$sql; 
	$Database->Query();   
	while ($row = mysql_fetch_array($Database->queryResult)) {
		$user[]=$row[0];
        }
	$Database->Close(); 
	return $user;
    }

}
?>
