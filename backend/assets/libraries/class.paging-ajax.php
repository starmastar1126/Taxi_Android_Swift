<?php 

class Paging
{
	private  $page_limit;	//set page limit 
	private  $rec_limit;	//set rec limit per page
	private  $totRec;		//tot record for paging
	private  $totPages;		//total pages to display
	private  $start;
	private  $page_string;


	/**
	 *@ intiliaze paging class & vars
	 *@ no return values
	 */

	function __construct($tot_rec,$start,$functoCall='',$res_limits='')
	{
		
		//intiailaze variables here & methods
		$this->setStart($start);				//set start
        $this->setTotalRecord($tot_rec);		//set total record
		$this->setRecordLimit($res_limits);				//set record limit
		$this->setPageLimit();					//set total page limit
		$this->setTotalPages($tot_rec);			//set total pages
		$this->JSFunc = $functoCall;
	}

	/**
	 *@ private set start from
	 *@ return start value
	 */

	private function setStart($start){
		if($start == "0")
			return $this->start = 0;
		else 
			return $this->start = $start;
	}

	/**
	 *@ private set total record
	 *@ return total record
	 */

	private function setTotalRecord($tot_rec){
		if($tot_rec == "")
			return $this->totRec = 0;
		else 
			return $this->totRec = $tot_rec;
	}

	/**
	 *@ private set total record limit per page
	 *@ return total record limit per page
	 */

	private function setRecordLimit($res_limits){
		global 	$REC_LIMIT_FRONT;
		if($res_limits == ''){
           $this->rec_limit = $REC_LIMIT_FRONT;
        }else{
              $this->rec_limit = $res_limits;
        }
		return $this->rec_limit;
	}

	/**
	 *@ private set totapages of listing
	 *@ return total pages
	 */

	private function setTotalPages($tot_rec){
	    return $this->totPages = ceil($this->totRec/$this->rec_limit);
	}

	/**
	 *@ private set pagelimit 
	 *@ return page limit
	 */

	private function setPageLimit(){
		global $FPLIMIT;
		return $this->page_limit = 4;
	}


	/**
	 *@ public display paging 
	 *@ return paging string
	 */

	public function displayPaging()
	{

		$this->page_string = "";
		$page_limit = $this->page_limit;
		$tot_pages = $this->totPages;
		$loop_limit = (($page_limit > $tot_pages) ? $tot_pages : $page_limit) ;

		$start_loop = floor($this->start/$page_limit);

		if($start_loop != ($this->start/$page_limit))
			$start_loop = $start_loop * $page_limit+1;
		else
			$start_loop = ($start_loop-1) * $page_limit+1;

		$this->page_string.="<div id='paging' align='right'>";
		
		if($start_loop > $page_limit)
		{
			$prev_loop = $start_loop - 1;
			$this->page_string.="<a href='javascript:".$this->JSFunc."(\"".$prev_loop."\");' title='Previous' class = 'paginglink'><b>Previous</b></a>&nbsp;";
	    }
		for($loop=1 ; $loop<=$loop_limit ; $loop++)
		{
			if($start_loop > $tot_pages) break;
			if($start_loop == $this->start)
				$clas = "paging-active";
			else
				$clas = "paginglink";
			$this->page_string.="<a href='javascript:".$this->JSFunc."(\"".$start_loop."\");' title=\"".$start_loop."\" class=".$clas." ><b>".$start_loop."</b></a>&nbsp;";
			$start_loop++;	
		}

		if($start_loop<=$tot_pages)
			$this->page_string.="<a href='javascript:".$this->JSFunc."(\"".$start_loop."\");'; title='Next' class = 'paginglink';><b>Next</b></a>&nbsp;";
		$this->page_string.="</div>";
		return $this->page_string;
	}

	/**
	 *@ public display paging message
	 *@ return paging message
	 */
	public function setMessage($msg)
	{
		$rec_limit = $this->rec_limit;
		$num_limit = ($this->start-1)*$rec_limit;
		$startrec = $num_limit;
		$lastrec = $startrec + $rec_limit;
		$startrec = $startrec + 1;
		if($lastrec > $this->totRec)
			$lastrec = $this->totRec;
		if($this->totRec > 0 ){
			return $recmsg = "Showing ".$startrec." - ".$lastrec." ".strtolower($msg)." of ".$this->totRec;
		}else{
			return $recmsg="No ".$msg." Found";
		}
	}
}
?>
