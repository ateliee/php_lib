<?php

class DateBase {

	private $host;
	private $datebase;
	private $name;
	private $pass;
	public  $link; 


	public function __construct ($h, $n, $p, $b) {
	    $this->host=$h;
	    $this->name=$n;
	    $this->pass=$p;
	    $this->database=$b;
	}

	public function Connect() {
		$noerrors=true;
		if (!($this->link=mysql_connect($this->host,$this->name,$this->pass))) {
		    $this->error('No connect '.$this->host);
		    $noerrors=false;
		}
		if (!mysql_select_db($this->database)) {
		    $this->error('No connect '.$this->database);
		    $noerrors=false;
		}
		return $noerrors;
	    }

	public function Query() {
		$this->queryResult=mysql_query($this->Query);
		return $this->queryResult;
	    }

	public function Close() {
		mysql_close($this->link);
	}
}
?>
