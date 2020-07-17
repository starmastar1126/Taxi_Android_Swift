<?php 
/**
 * This Class is for db connection to whole site 
 *
 * @package		class.dbquery.php
 * @section		general
**/

class DBConnection 
{
	private $DBASE="";
	private $CONN="";


	/**
	* @access	public
	* @check database connection
	* @return	true/false
	*/
	public function __construct($server="",$dbase="", $user="", $pass="") 
	{
		$this->DBASE = $dbase;
		$conn = mysqli_connect($server,$user,$pass);
		if(!$conn) {
			$this->MySQLDie("Connection attempt failed");
		}
		if(!$conn->select_db($dbase))
		{
			$this->MySQLDie("Dbase Select failed");
		} 
		$this->CONN = $conn;
		mysqli_query($conn,"SET NAMES 'utf8'");
		mysqli_query($conn,"SET character SET 'utf8'");
		mysqli_set_charset($conn,'UTF8');
    //mb_internal_encoding("UTF-8"); 
		return true;
	
	}

	/**
	* @access	public
	* @Close Database connection
	* @return	true/false
	*/
	public function MySQLClose()
	{
		$conn = $this->CONN ;
		$close = mysqli_close($conn);
		if(!$close) {
			$this->MySQLDie("Connection close failed");
		}
		return true;
	}

	/**
	* @access	private
	* @Set Message for Die
	* @return	Message
	*/
	private function MySQLDie($text)
	{
		die($text);
	}

	/**
	* @access	public
	* @Retrive  Records
	* @param 	$sql query
	* @return	array
	*/
	public function MySQLSelect($sql="",$cached="")
	{	
    //echo "<br><br>".$sql;
    if(empty($sql)) { return false; }
		/*if(!eregi("^select",$sql))
		{
			echo "wrongquery<br>$sql<p>";
			echo "<H2>Wrong function silly!</H2>\n";
			return false;
		}*/
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$results = mysqli_query($conn,$sql);
		
		if( (!$results) or (empty($results)) ) {
			return false;
		}
		$count = 0;
		$data = array();
		while ($row = mysqli_fetch_assoc($results))
		{
			$data[$count] = $row;
			$count++;
			// echo "<pre>";print_r($row);
		}
		mysqli_free_result($results);
		return $data;
	}

	/**
	* @access	public
	* @get all fields from table 
	* @param 	$table name
	* @return	all fields
	*/
	public function MySQLGetFields($table)
	{
		$fields = mysqli_list_fields($this->DBASE, $table, $this->CONN); 
		$columns = mysqli_num_fields($fields); 
		for ($i = 0; $i < $columns; $i++) { 
		   $arr[]= mysqli_field_name($fields, $i); 
		}
		return $arr;
	}
	/**
	* @access	public
	* @get all fields from table 
	* @param 	$table name
	* @return	all fields
	*/

	public function MySQLGetFieldsQuery($table,$primarykey='Yes')
	{
		$fields = mysqli_list_fields($this->DBASE, $table, $this->CONN);

		$columns = mysqli_num_fields($fields);

		for ($i = 0; $i < $columns; $i++)
		{
			if($primarykey=='Yes')
			{
				if($arrF !='')
						$arrF.= ",";
					
				$arrF.= mysqli_field_name($fields, $i);
			}
			elseif($primarykey=='No')
			{
				if(!stristr(mysqli_field_flags($fields, $i),'primary_key'))		
				{
					if($arrF !='')
						$arrF.= ",";
					
					$arrF.= mysqli_field_name($fields, $i);
				}
			}
		}
		return $arrF;
	}
	
	
	/**
	* @access	public
	* @insert update/Query
	* @param 	$table name
	* @return	all fields
	*/
	public function MySQLQueryPerform($table, $data, $action = 'insert', $parameters = '')
	{                
		$conn = $this->CONN;
		reset($data);
	    if ($action == 'insert'){$query = 'insert into ' . $table . ' (';while (list($columns, ) = each($data)) {
	        $query .= $columns . ', ';
	    }
	    $query = substr($query, 0, -2) . ') values (';reset($data);
		while (list(, $value) = each($data))
		{
			switch ((string)$value) {
			case 'null':
				$query .= 'null, ';
		    break;
		    default:
		    	$query .= '\'' . $this->cleanQuery($value) . '\', ';
		    	break;
		     }
	    }

		$query 		= substr($query, 0, -2) . ')'; //Insert Query ready
		$value1['query'] = $query;
		echo json_encode($value1);
		exit;
		
		$results 	= mysqli_query($conn,$query) or die("Query failed: " . mysqli_error()."::".$query);
		$results 	= mysqli_insert_id($conn);
		
		if(!$results)
		   {
			 $this->MySQLDie("Query went bad!");
			 return false;
		   }
	    }
		elseif ($action == 'update')
		{
	      $query = 'update ' . $table . ' set ';
	      while (list($columns, $value) = each($data))
		  {
	        switch ((string)$value)
			{
	          case 'null':
	            $query .= $columns .= ' = null, ';
	             break;
	          default:
	            $query .= $columns . ' = \'' .$this->cleanQuery($value). '\', ';
	            break;
	        }
	     }
	    $query = substr($query, 0, -2) . ' where ' . $parameters; //Update Query ready
		
			
		$results = mysqli_query($conn,$query) or die("Query failed: " . mysqli_error()."::".$query);
		  if(!$results)
		  {
			 $this->MySQLDie("Query went bad!");
			 return false;
		  }
	    }
		
		return $results;
	}
	
	/**
	* @access	public
	* @Delete
	* @param 	$table,$where
	* @return	$query
	*/
	public function MySQLDelete( $table, $where)
	{
		$query = "DELETE FROM `$table` WHERE  $where";
		//echo $query;exit;
		$conn = $this->CONN;

		// or MySQLDie("DELETE ERROR ($query): " . mysqli_error() )

		if( $conn )
			return mysqli_query($conn,$query);
		return $query;
	}
	
	/**
	**/
	/*public function Getfieldtype($table,$field)
	{
		$data = array();
		if(empty($table)) { return false; }
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$sql = "select * from ".$table;
		$results = mysqli_query($sql,$conn) or die(mysqli_error()."query fail");
		
		if(!$results)
		{   $message = "Query went bad!";
			$this->error($message);
			return false;
		}
		$i = 0;
		while ($i < mysqli_num_fields($results)) 
		{
		    $meta = mysqli_fetch_field($results,$i);
			echo $meta->name;
			echo $meta->type;
			echo "</br>";
			
			/*if ($meta->name == $field)
			{
				$data[name]=$meta->name;
				$data[type]=$meta->type;
			}
			$i++;
		}
		if($data)
		{
			return $data;
		}
		else
		{
			return false;
		}
	}*/
	
	/**
	* @access	public
	* @Perform the query action
	* @param 	$sql;
	* @return	$data;
	*/
	
	public function sql_query($sql="")
	{	
    //echo "<br><br>".$sql;
    if(empty($sql)) { return false; }
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$results = mysqli_query($conn,$sql) or die(mysqli_error()."query fail");
		if(!$results)
		{   $message = "Query went bad!";
			$this->error($message);
			return false;
		}
		 $sql;
		if(strpos($sql, 'select') === false) {
			return true; }
		else {
			$count = 0;
			$data = array();
			while ( $row = mysqli_fetch_array($results))	{
				$data[$count] = $row;
				$count++;
			}
			mysqli_free_result($results);
			return $data;
	 	}
	}
	
	public function MySQLInsert ($sql="")
	{
    if(empty($sql)) { return false; }
		if(strpos(strtolower($sql), 'insert') === false)
		{
			return false;
		}
		if(empty($this->CONN))
		{
			return false;
		}
		$conn = $this->CONN;
		$results = mysqli_query($conn,$sql);
		if(!$results) 
		{
			$this->error("<H2>No results!</H2>\n");
			return false;
		}
		$id = mysqli_insert_id($conn);
		return $id;
	}
	
	
	/**
	* @access	public
	* @insert  Query
	* @param 	$table name
	* @return	all fields
	*/
	public function MySQLInsertPerform($table, $data, $action = 'insert', $parameters = '')
	{
		$conn = $this->CONN;
		reset($data);
	    if ($action == 'insert'){$query = 'insert into ' . $table . ' (';while (list($columns, ) = each($data)) {
	        $query .= $columns . ', ';
	    }
	    $query = substr($query, 0, -2) . ') values (';reset($data);
		while (list(, $value) = each($data))
		{
			switch ((string)$value) {
			case 'null':
				$query .= 'null, ';
		    break;
		    default:
		    	$query .= '\'' . $value . '\', ';
		    	break;
		     }
	    }

		$query 		= substr($query, 0, -2) . ')'; //Insert Query ready
		$results 	= mysqli_query($conn,$query) or die("Query failed: " . mysqli_error()."::".$query);
		if(!$results)
		   {
			 $this->MySQLDie("Query went bad!");
			 return false;
		   }
	    }
		return $results;
	}
	
	public function cache_array_new($query) {
	
    global $dbobj,$TIME_ELAPSE;
    
    $TIME_ELAPSE = !isset($TIME_ELAPSE) ? 10000:$TIME_ELAPSE;
    
    $filename = SPATH_ROOT."/cache_files/".md5($query).".txt";
      if (!file_exists($filename)) {
      	$content=	$this->MySQLSelect($query,"No");//Result array set of $array=$db->query($query, "query");
        
        if (!$handle = fopen($filename, 'w+')) {	//If File is not exists than attemp to create it
      		echo "not created";
      		exit();	
      	}
      	$content_file	=	serialize($content);
      	if (fwrite($handle, $content_file) === FALSE) {
      		echo "permision denied or file not exists";
      		exit();	
      	}
      	chmod($filename,0777);
      	fclose($handle);
      } else {
      	
        $time = filemtime($filename);
       	$time = $time + $TIME_ELAPSE;
      	$curTime = strtotime("now");
      	/*echo $curTime." < ".$time;
      	echo "<hr>";
      	echo $curTime < $time;*/
      	//echo "<pre>";
        if($curTime < $time) { 
          
        	if (!$handle = fopen($filename, 'r')) {	//If File exists than attemp to create it
        	
        		echo "not created";
        	
        		exit();	
        	}
        	$content	=	fread($handle, filesize($filename));
        	$content	=	unserialize($content);
        	//var_dump($content);
      	} else {
      	   $content=	$this->MySQLSelect($query,"No");	//Result array set of $array=$db->query($query, "query");
      
        	if (!$handle = fopen($filename, 'w+')) {	//If File is not exists than attemp to create it
        		echo "not created";
        		exit();	
        	}
        
        	$content_file	=	serialize($content);
        
        	if (fwrite($handle, $content_file) === FALSE) {
        		echo "permision denied or file not exists";
        		exit();	
        	}
        	chmod($filename,0777);
        	fclose($handle);
      	
        }
      }
    return $content;
    }
	
	function cleanQuery($string)
{
  $conn = $this->CONN;
  if(get_magic_quotes_gpc())  // prevents duplicate backslashes
  {
    $string = stripslashes($string);
  }
  if (phpversion() >= '4.3.0')
  {
    // $string = stripslashes($string);
    //Commented by CD on 28th Sept
    $string = mysqli_real_escape_string($conn,$string);
  }
  else
  {
    //Commented by CD on 28th Sept
    $string = mysqli_real_escape_string($conn,$string);
  }
  return $string;
}

public function MyTokenId($tokenid)
	{
		echo '+++++++';
	} 
	public function SqlEscapeString($string="")
	{	
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$str = mysqli_real_escape_string($conn,$string);
		return $str;
	}
	
	public function ExecuteQuery($string)
	{	
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$result_data = mysqli_query($conn,$string) or die('Query failed!');
		
		return $result_data;
	}
	
	 public function GetConnection()
	{	
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		return $conn;
	} 
	
	public function GetInsertId(){
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$result = mysqli_insert_id($conn) or die('Query failed!');
		
		return $result;
	}
	
	public function GetAffectedRows()
	{	
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$rows = mysqli_affected_rows($conn);
		return $rows;
	}
}
?>
