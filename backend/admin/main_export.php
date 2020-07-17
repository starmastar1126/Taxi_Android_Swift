<?php 

include_once('../common.php');
if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}

$section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
//$searchData = isset($_REQUEST['searchData']) ? $_REQUEST['searchData'] : '';
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$option = isset($_REQUEST['option']) ? $_REQUEST['option'] : "";
$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : "";
$eStatus = isset($_REQUEST['eStatus']) ? $_REQUEST['eStatus'] : "";
$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : "";
$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : "";
$type = isset($_REQUEST['exportType']) ? $_REQUEST['exportType'] : '';
$ssql = "";
require('fpdf/fpdf.php');

function cleanData(&$str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if (strstr($str, '"'))
        $str = '"' . str_replace('"', '""', $str) . '"';
}

if ($section == 'admin') {
    
    $ord = ' ORDER BY ad.vFirstName ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY ad.vFirstName ASC";
      else
      $ord = " ORDER BY ad.vFirstName DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY ad.vEmail ASC";
      else
      $ord = " ORDER BY ad.vEmail DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY ag.vGroup ASC";
      else
      $ord = " ORDER BY ag.vGroup DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY ad.eStatus ASC";
      else
      $ord = " ORDER BY ad.eStatus DESC";
    }
    //End Sorting

    // $adm_ssql = "";
    // if (SITE_TYPE == 'Demo') {
        // $adm_ssql = " And ad.tRegistrationDate > '" . WEEK_DATE . "'";
    // }

    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (concat(ad.vFirstName,' ',ad.vLastName) LIKE '%".$keyword."%' OR ad.vEmail LIKE '%".$keyword."%' OR ag.vGroup LIKE '%".$keyword."%' OR ad.eStatus LIKE '%".$keyword."%')";
        }
    }
	if($option == "ad.eStatus"){	
	 $eStatussql = " AND ad.eStatus = '".ucfirst($keyword)."'";
	}else{
	 $eStatussql = " AND ad.eStatus != 'Deleted'";
	}

    $sql = "SELECT CONCAT(ad.vFirstName,' ',ad.vLastName) as Name,ad.vEmail as Email,ag.vGroup as `Admin Roles`, ad.eStatus as Status FROM administrators AS ad LEFT JOIN admin_groups AS ag ON ad.iGroupId=ag.iGroupId where 1=1 $eStatussql $ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        // $result = mysqli_query($sql) 
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
        echo implode("\t", array_keys($result[0])) . "\r\n";
        
        foreach($result as $value){
            foreach($value as $key=>$val) {
            // echo $key.' => '.$val;
                if($key == 'Name'){
                    $val = $generalobjAdmin->clearName($val);
                }
                if($key == 'Email'){
                    $val = $generalobjAdmin->clearEmail($val);
                }
                /*if($key == 'Mobile'){
                    $val = $generalobjAdmin->clearPhone($val);
                }*/
                echo $val."\t";
            }
            echo "\r\n";
        }
    } else {
        $heading = array('Name', 'Email', 'Admin Roles', 'Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Admin Users");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(10, 10, $column_heading, 1);
            } /*else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            }*/ else if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                $values = $key;
                if($column == 'Name'){
                    $values = $generalobjAdmin->clearName($key);
                }
                if($column == 'Email'){
                    $values = $generalobjAdmin->clearEmail($key);
                }
               /* if($column == 'Mobile'){
                    $values = $generalobjAdmin->clearPhone($key);
                }*/
                
                if ($column == 'Id') {
                    $pdf->Cell(10, 10, $values, 1);
                } /*else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $values, 1);
                } */else if ($column == 'Status') {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}



if ($section == 'company') {
    
    $ord = ' ORDER BY c.iCompanyId DESC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY c.vCompany ASC";
      else
      $ord = " ORDER BY c.vCompany DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY c.vEmail ASC";
      else
      $ord = " ORDER BY c.vEmail DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY c.eStatus ASC";
      else
      $ord = " ORDER BY c.eStatus DESC";
    }
    //End Sorting
    
    if ($keyword != '') {
        if ($option != '') {
            if($eStatus != ''){
                $ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND c.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            if($eStatus != ''){
                $ssql.= " AND (c.vCompany LIKE '%".$keyword."%' OR c.vEmail LIKE '%".$keyword."%' OR c.vPhone LIKE '%".$keyword."%') AND c.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql.= " AND (c.vCompany LIKE '%".$keyword."%' OR c.vEmail LIKE '%".$keyword."%' OR c.vPhone LIKE '%".$keyword."%')";
            }
        }
    } else if( $eStatus != '' && $keyword == '' ) {
         $ssql.= " AND c.eStatus LIKE '".$generalobjAdmin->clean($eStatus)."'";
    }
    
    $cmp_ssql = "";
    // if (SITE_TYPE == 'Demo') {
        // $cmp_ssql = " And c.tRegistrationDate > '" . WEEK_DATE . "'";
    // }
    if($eStatus != '') { 
        $eStatus_sql = "";
    } else {
        $eStatus_sql = " AND c.eStatus != 'Deleted'"; 
    }


     $sql = "SELECT  c.vCompany AS Name, c.vEmail AS Email,(SELECT count(rd.iDriverId) FROM register_driver AS rd WHERE rd.iCompanyId=c.iCompanyId) AS `Total Drivers`, CONCAT(c.vCode,'',c.vPhone) AS Mobile,c.eStatus AS Status FROM company AS c WHERE 1 = 1 $eStatus_sql $ssql $cmp_ssql $ord";
    
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query Failed!');
        echo implode("\t", array_keys($result[0])) . "\r\n";
        
        foreach($result as $value){
            foreach($value as $key=>$val) {
            // echo $key.' => '.$val;
                if($key == 'Email'){
                    $val = $generalobjAdmin->clearEmail($val);
                }
                if($key == 'Mobile'){
                    $val = $generalobjAdmin->clearPhone($val);
                }
                if($key == 'Name'){
                    $val = $generalobjAdmin->clearCmpName($val);
                }
                echo $val."\t";
            }
            echo "\r\n";
        }
    } else {
        $heading = array('Name', 'Email', 'Total Drivers', 'Mobile', 'Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Companies");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Total Drivers') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else if ($column_heading == 'Mobile') {
                $pdf->Cell(30, 10, $column_heading, 1);
            } else if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(55, 10, $column_heading, 1);
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                $values = $key;
                if($column == 'Email'){
                    $values = $generalobjAdmin->clearEmail($key);
                }
                if($column == 'Mobile'){
                    $values = $generalobjAdmin->clearPhone($key);
                }
                if($column == 'Name'){
                    $values = $generalobjAdmin->clearCmpName($key);
                }
                if ($column == 'Total Drivers') {
                    $pdf->Cell(25, 10, $values, 1);
                } else if ($column == 'Mobile') {
                    $pdf->Cell(30, 10, $values, 1);
                } else if ($column == 'Status') {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(55, 10, $values, 1);
                }
            }
        }
        $pdf->Output('D');
       
    }
}


if ($section == 'rider') {
    $ord = ' ORDER BY iUserId DESC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vName ASC";
      else
      $ord = " ORDER BY vName DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY vEmail ASC";
      else
      $ord = " ORDER BY vEmail DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY tRegistrationDate ASC";
      else
      $ord = " ORDER BY tRegistrationDate DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    }
    $rdr_ssql = "";
    if (SITE_TYPE == 'Demo') {
        $rdr_ssql = " And tRegistrationDate > '" . WEEK_DATE . "'";
    }
    
    if ($keyword != '') {
        if ($option != '') {
             $option_new = $option;
            if($option == 'RiderName'){
              $option_new = "CONCAT(vName,' ',vLastName)";
            }
            if($eStatus != ''){
                $ssql .= " AND " . stripslashes($option_new) . " LIKE '%" . stripslashes($keyword) . "%' AND eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql .= " AND " . stripslashes($option_new) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            if($eStatus != ''){
			     $ssql .= " AND (concat(vName,' ',vLastName) LIKE '%".$keyword."%' OR vEmail LIKE '%".$keyword."%' OR vPhone LIKE '%".$keyword."%') AND eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql .= " AND (concat(vName,' ',vLastName) LIKE '%".$keyword."%' OR vEmail LIKE '%".$keyword."%' OR vPhone LIKE '%".$keyword."%')"; 
            }
        }
    } else if($eStatus != '' && $keyword == '') {
         $ssql.= " AND eStatus LIKE '".$generalobjAdmin->clean($eStatus)."'";
    }

    if($eStatus != '') { 
        $eStatus_sql = "";
    } else {
        $eStatus_sql = " AND eStatus != 'Deleted'"; 
    }

    $sql = "SELECT CONCAT(vName,' ',vLastName) as Name,vEmail as Email,vPhone AS Mobile,eStatus as Status FROM register_user WHERE 1=1 $eStatus_sql $ssql $rdr_ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
        echo implode("\t", array_keys($result[0])) . "\r\n";
        
        foreach($result as $value){
            foreach($value as $key=>$val) {
            // echo $key.' => '.$val;
                if($key == 'Name'){
                    $val = $generalobjAdmin->clearName($val);
                }
                if($key == 'Email'){
                    $val = $generalobjAdmin->clearEmail($val);
                }
                if($key == 'Mobile'){
                    $val = $generalobjAdmin->clearPhone($val);
                }
                echo $val."\t";
            }
            echo "\r\n";
        }
    } else {
        $heading = array('Name', 'Email', 'Mobile', 'Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Riders");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
           if ($column_heading == 'Email') {
                $pdf->Cell(55, 10, $column_heading, 1);
            } else if ($column_heading == 'Mobile') {
                $pdf->Cell(45, 10, $column_heading, 1);
            } else if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                $values = $key;
                if($column == 'Name'){
                    $values = $generalobjAdmin->clearName($key);
                }
                if($column == 'Email'){
                    $values = $generalobjAdmin->clearEmail($key);
                }
                if($column == 'Mobile'){
                    $values = $generalobjAdmin->clearPhone($key);
                }
                if ($column == 'Email') {
                    $pdf->Cell(55, 10, $values, 1);
                } else if ($column == 'Mobile') {
                    $pdf->Cell(45, 10, $values, 1);
                } else if ($column == 'Status') {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}
//make 
if ($section == 'make') {
	$ord = ' ORDER BY vMake ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vMake ASC";
      else
      $ord = " ORDER BY vMake DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    }
	//echo "<pre>"; print_r($_REQUEST); exit;
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (vMake LIKE '%".$keyword."%' OR eStatus LIKE '%".($keyword)."%')";
        }
    }
	if($option == "eStatus"){	
	 $eStatussql = " AND eStatus = '".($keyword)."'";
	}else{
	 $eStatussql = " AND eStatus != 'Deleted'";
	}

    $sql = "SELECT vMake as Make, eStatus as Status FROM make where 1=1 $eStatussql $ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Make','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Make");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Status') {
                $pdf->Cell(70, 10, $column_heading, 1);
            } else {
                $pdf->Cell(80, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Status') {
                    $pdf->Cell(70, 10, $key, 1);
                } else {
                    $pdf->Cell(80, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}
//make

////////// Package Start //////////////

if ($section == 'package_type') {
    $ord = ' ORDER BY vName ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vName ASC";
      else
      $ord = " ORDER BY vName DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    }
    //echo "<pre>"; print_r($_REQUEST); exit;
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (vName LIKE '%".$keyword."%' OR eStatus LIKE '%".($keyword)."%')";
        }
    }
    if($option == "eStatus"){   
     $eStatussql = " AND eStatus = '".($keyword)."'";
    }else{
     $eStatussql = " AND eStatus != 'Deleted'";
    }

    $sql = "SELECT vName as Name, eStatus as Status FROM package_type where 1=1 $eStatussql $ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Name','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Name");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Status') {
                $pdf->Cell(70, 10, $column_heading, 1);
            } else {
                $pdf->Cell(80, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Status') {
                    $pdf->Cell(70, 10, $key, 1);
                } else {
                    $pdf->Cell(80, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}

////////// Package End ////////////// 

//model
if ($section == 'model') {
    $ord = ' ORDER BY mo.vTitle ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY mo.vTitle ASC";
      else
      $ord = " ORDER BY mo.vTitle DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY mk.vMake ASC";
      else
      $ord = " ORDER BY mk.vMake DESC";
    }


    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY mo.eStatus ASC";
      else
      $ord = " ORDER BY mo.eStatus DESC";
    }
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (mo.vTitle LIKE '%".$keyword."%' OR mo.eStatus LIKE '%".$keyword."%' OR mk.vMake LIKE '%".$keyword."%')";
        }
    }
    
    if($option == "eStatus"){   
     $eStatussql = " AND mo.eStatus = '".ucfirst($keyword)."'";
    }else{
     $eStatussql = " AND mo.eStatus != 'Deleted'";
    }
    $sql = "SELECT mo.vTitle AS Title, mk.vMake AS Make, mo.eStatus AS Status FROM model  AS mo LEFT JOIN make AS mk ON mk.iMakeId = mo.iMakeId WHERE 1=1 $eStatussql $ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Title', 'Make', 'Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Model");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(45, 10, $column_heading, 1);
            } else if ($column_heading == 'Status') {
                $pdf->Cell(60, 10, $column_heading, 1);
            } else {
                $pdf->Cell(70, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Id') {
                    $pdf->Cell(45, 10, $key, 1);
                } else if ($column == 'Status') {
                    $pdf->Cell(60, 10, $key, 1);
                } else {
                    $pdf->Cell(70, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}

//model

//country
if ($section == 'country') {
    
    $ord = ' ORDER BY vCountry ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vCountry ASC";
      else
      $ord = " ORDER BY vCountry DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY vPhoneCode ASC";
      else
      $ord = " ORDER BY vPhoneCode DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY eUnit ASC";
      else
      $ord = " ORDER BY eUnit DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    }
    //End Sorting
    
    
    if ($keyword != '') {
        if ($option != '') {
            if($eStatus != ''){
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%' AND eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            if($eStatus != ''){
                $ssql .= " AND (vCountry LIKE '%".stripslashes($keyword)."%' OR vPhoneCode LIKE '%".stripslashes($keyword)."%' OR vCountryCodeISO_3 LIKE '%".stripslashes($keyword)."%') AND eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                 $ssql .= " AND (vCountry LIKE '%".stripslashes($keyword)."%' OR vPhoneCode LIKE '%".stripslashes($keyword)."%' OR vCountryCodeISO_3 LIKE '%".stripslashes($keyword)."%')";
            }
        }
    } else if( $eStatus != '' && $keyword == '' ) {
         $ssql.= " AND eStatus = '".$generalobjAdmin->clean($eStatus)."'";
    }

    if($eStatus != '') { 
        $eStatus_sql = "";
    } else {
        $eStatus_sql = " AND eStatus != 'Deleted'"; 
    }

    $sql = "SELECT vCountry as Country,vPhoneCode as PhoneCode, eUnit as Unit, eStatus as Status FROM country where 1 = 1 $eStatus_sql $ssql";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Country','PhoneCode','Unit','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Country");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Status') {
                $pdf->Cell(44, 10, $column_heading, 1);
            } else {
                $pdf->Cell(44, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Status') {
                    $pdf->Cell(44, 10, $key, 1);
                } else {
                    $pdf->Cell(44, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
    }
}

//State
if ($section == 'state') {
    
    $ord = ' ORDER BY s.vState ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY c.vCountry ASC";
      else
      $ord = " ORDER BY c.vCountry DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY s.vState ASC";
      else
      $ord = " ORDER BY s.vState DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY s.vStateCode ASC";
      else
      $ord = " ORDER BY s.vStateCode DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY s.eStatus ASC";
      else
      $ord = " ORDER BY s.eStatus DESC";
    }
    //End Sorting
    
    if($keyword != ''){
        if($option != '') {
            if (strpos($option, 's.eStatus') !== false) {
                $ssql.= " AND ".stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            }else {
                $ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        }else {
            $ssql.= " AND (c.vCountry LIKE '%".$keyword."%' OR s.vState LIKE '%".$keyword."%' OR s.vStateCode LIKE '%".$keyword."%' OR s.eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT s.vState AS State,s.vStateCode AS `State Code`,c.vCountry AS Country,s.eStatus
            FROM state AS s
            LEFT JOIN country AS c ON c.iCountryId = s.iCountryId
            WHERE s.eStatus !=  'Deleted' $ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('State','State Code', 'Country','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "State");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Status') {
                $pdf->Cell(40, 10, $column_heading, 1);
            } else {
                $pdf->Cell(40, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
               if ($column == 'Status') {
                    $pdf->Cell(40, 10, $key, 1);
                } else {
                    $pdf->Cell(40, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
    }
}

//State
if ($section == 'city') {
    $ord = ' ORDER BY vCity ASC';
    if($sortby == 1){
      if($order == 0)
        $ord = " ORDER BY st.vState ASC";
      else
        $ord = " ORDER BY st.vState DESC";
    }

    if($sortby == 2){
      if($order == 0)
        $ord = " ORDER BY ct.vCity ASC";
      else
        $ord = " ORDER BY ct.vCity DESC";
    }


    if($sortby == 3){
      if($order == 0)
        $ord = " ORDER BY c.vCountry ASC";
      else
        $ord = " ORDER BY c.vCountry DESC";
    }

    if($sortby == 4){
        if($order == 0)
            $ord = " ORDER BY ct.eStatus ASC";
        else
            $ord = " ORDER BY ct.eStatus DESC";
    }
    
    if($keyword != ''){
        if($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql.= " AND ".stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            }else {
                $ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        }else {
            $ssql.= " AND (ct.vCity LIKE '%".$keyword."%' OR st.vState LIKE '%".$keyword."%' OR c.vCountry LIKE '%".$keyword."%' OR ct.eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT ct.vCity AS City,st.vState AS State,c.vCountry AS Country, ct.eStatus AS Status FROM city AS ct left join country AS c ON c.iCountryId =ct.iCountryId left join state AS st ON st.iStateId=ct.iStateId WHERE  ct.eStatus != 'Deleted' $ssql $ord";
    
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('City','State','Country','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "City");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Status') {
                $pdf->Cell(35, 10, $column_heading, 1);
            } else {
                $pdf->Cell(35, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Status') {
                    $pdf->Cell(35, 10, $key, 1);
                } else {
                    $pdf->Cell(35, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
    }
}

//country

//faq
$default_lang   = $generalobj->get_default_lang();
if ($section == 'faq') {

    $ord = ' ORDER BY f.vTitle_'.$default_lang.' ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY f.vTitle_".$default_lang." ASC";
      else
      $ord = " ORDER BY f.vTitle_".$default_lang." DESC";
    }

    if($sortby == 2){
      if($order == 0)
     // $ord = " ORDER BY iFaqcategoryId ASC";
        $ord = " ORDER BY fc.vTitle ASC";
      else
      //$ord = " ORDER BY iFaqcategoryId DESC";
        $ord = " ORDER BY fc.vTitle DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY f.iDisplayOrder ASC";
      else
      $ord = " ORDER BY f.iDisplayOrder DESC";
    } 

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY f.eStatus ASC";
      else
      $ord = " ORDER BY f.eStatus DESC";
    }

    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (f.vTitle_".$default_lang." LIKE '%".$keyword."%' OR fc.vTitle LIKE '%".$keyword."%' OR f.iDisplayOrder LIKE '%".$keyword."%' OR f.eStatus LIKE '%".$keyword."%')";
        }
    }                                   
    
    $tbl_name       = 'faqs';
    $sql = "SELECT f.vTitle_".$default_lang." as `Title`, fc.vTitle as `Category` ,f.iDisplayOrder as `DisplayOrder` ,f.eStatus  as `Status` FROM ".$tbl_name." f, faq_categories fc WHERE f.iFaqcategoryId = fc.iUniqueId AND fc.vCode = '".$default_lang."' $ssql $ord"; 
    
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Title','Category','Order','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        //print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "FAQ");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Title') {
                $pdf->Cell(80, 10, $column_heading, 1);
            }  else if ($column_heading == 'Category') {
                $pdf->Cell(45, 10, $column_heading, 1);
            }  else if ($column_heading == 'Order') {
                $pdf->Cell(28, 10, $column_heading, 1);             
            } else if ($column_heading == 'Status') {
                $pdf->Cell(28, 10, $column_heading, 1);
            } else {
                $pdf->Cell(28, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Title') {
                    $pdf->Cell(80, 10, $key, 1);
                }  else if ($column == 'Category') {
                    $pdf->Cell(45, 10, $key, 1);
                }  else if ($column == 'Order') {
                    $pdf->Cell(28, 10, $key, 1);    
                }  else if ($column == 'Status') {
                    $pdf->Cell(28, 10, $key, 1);
                } else {
                    $pdf->Cell(28, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
        }
}
//faq

// help Detail
if ($section == 'help_detail') {

    $ord = ' ORDER BY f.vTitle_'.$default_lang.' ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY f.vTitle_".$default_lang." ASC";
      else
      $ord = " ORDER BY f.vTitle_".$default_lang." DESC";
    }

    if($sortby == 2){
      if($order == 0)
     // $ord = " ORDER BY iFaqcategoryId ASC";
        $ord = " ORDER BY fc.vTitle ASC";
      else
      //$ord = " ORDER BY iFaqcategoryId DESC";
        $ord = " ORDER BY fc.vTitle DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY f.iDisplayOrder ASC";
      else
      $ord = " ORDER BY f.iDisplayOrder DESC";
    } 

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY f.eStatus ASC";
      else
      $ord = " ORDER BY f.eStatus DESC";
    }

    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (f.vTitle_".$default_lang." LIKE '%".$keyword."%' OR fc.vTitle LIKE '%".$keyword."%' OR f.iDisplayOrder LIKE '%".$keyword."%' OR f.eStatus LIKE '%".$keyword."%')";
        }
    }                                   
    
    $tbl_name       = 'help_detail';
    $sql = "SELECT f.vTitle_".$default_lang." as `Title`, fc.vTitle as `Category` ,f.iDisplayOrder as `DisplayOrder` ,f.eStatus  as `Status` FROM ".$tbl_name." f, help_detail_categories fc WHERE f.iHelpDetailCategoryId = fc.iUniqueId AND fc.vCode = '".$default_lang."' $ssql $ord"; 
    
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Title','Category','Order','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        //print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Help Detail");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Title') {
                $pdf->Cell(80, 10, $column_heading, 1);
            }  else if ($column_heading == 'Category') {
                $pdf->Cell(45, 10, $column_heading, 1);
            }  else if ($column_heading == 'Order') {
                $pdf->Cell(28, 10, $column_heading, 1);             
            } else if ($column_heading == 'Status') {
                $pdf->Cell(28, 10, $column_heading, 1);
            } else {
                $pdf->Cell(28, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Title') {
                    $pdf->Cell(80, 10, $key, 1);
                }  else if ($column == 'Category') {
                    $pdf->Cell(45, 10, $key, 1);
                }  else if ($column == 'Order') {
                    $pdf->Cell(28, 10, $key, 1);    
                }  else if ($column == 'Status') {
                    $pdf->Cell(28, 10, $key, 1);
                } else {
                    $pdf->Cell(28, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
        }
}
//help detail end

//faq category
if ($section == 'faq_category') {
    $ord = ' ORDER BY vTitle ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vImage ASC";
      else
      $ord = " ORDER BY vImage DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY vTitle ASC";
      else
      $ord = " ORDER BY vTitle DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY iDisplayOrder ASC";
      else
      $ord = " ORDER BY iDisplayOrder DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    }
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (vTitle LIKE '%".$keyword."%' OR iDisplayOrder LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

     $sql = "SELECT vTitle as `Title`, iDisplayOrder as `Order`, eStatus as `Status` FROM faq_categories where vCode = '".$default_lang."' $ssql $ord";
    // die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Title','Order','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "FAQ Category");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Status') {
                $pdf->Cell(44, 10, $column_heading, 1);
            } else {
                $pdf->Cell(44, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Status') {
                    $pdf->Cell(44, 10, $key, 1);
                } else {
                    $pdf->Cell(44, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
       // $pdf->Output();
    }
}
//faq category

//Help Detail category
if ($section == 'help_detail_category') {
    $ord = ' ORDER BY vTitle ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vImage ASC";
      else
      $ord = " ORDER BY vImage DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY vTitle ASC";
      else
      $ord = " ORDER BY vTitle DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY iDisplayOrder ASC";
      else
      $ord = " ORDER BY iDisplayOrder DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    }
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (vTitle LIKE '%".$keyword."%' OR iDisplayOrder LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

     $sql = "SELECT vTitle as `Title`, iDisplayOrder as `Order`, eStatus as `Status` FROM help_detail_categories where vCode = '".$default_lang."' $ssql $ord";
    // die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Title','Order','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Help Detail Category");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Status') {
                $pdf->Cell(44, 10, $column_heading, 1);
            } else {
                $pdf->Cell(44, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Status') {
                    $pdf->Cell(44, 10, $key, 1);
                } else {
                    $pdf->Cell(44, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
       // $pdf->Output();
    }
}
//Help Detail category

//pages
$default_lang   = $generalobj->get_default_lang();
if ($section == 'page') {
    $ord = ' ORDER BY vPageName ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vPageName ASC";
      else
      $ord = " ORDER BY vPageName DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY vPageTitle_".$default_lang." ASC";
      else
      $ord = " ORDER BY vPageTitle_".$default_lang." DESC";
    }
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (vPageName LIKE '%".$keyword."%' OR vPageTitle_".$default_lang." LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }
    
    $sql = "SELECT vPageName as `Name`, vPageTitle_".$default_lang." as `PageTitle` FROM pages where ipageId NOT IN('5','20','21','20') AND eStatus != 'Deleted' $ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Name','PageTitle');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Pages");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Name') {
                $pdf->Cell(57, 10, $column_heading, 1);
            } else if ($column_heading == 'PageTitle') {
                $pdf->Cell(100, 10, $column_heading, 1);
            } else {
                $pdf->Cell(20, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Name') {
                    $pdf->Cell(57, 10, $key, 1);
                }  else if ($column == 'PageTitle') {
                    $pdf->Cell(100, 10, $key, 1);
                } else {
                    $pdf->Cell(20, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
    }
}
//pages

//languages
$default_lang   = $generalobj->get_default_lang();
if ($section == 'languages') {
    $ord = ' ORDER BY vValue ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vLabel ASC";
      else
      $ord = " ORDER BY vLabel DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY vValue ASC";
      else
      $ord = " ORDER BY vValue DESC";
    }

    if($keyword != ''){
        if($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql.= " AND ".addslashes($option)." LIKE '".addslashes($keyword)."'";
            }else {
                $ssql.= " AND ".addslashes($option)." LIKE '%".addslashes($keyword)."%'";
            }
        }else {
            $ssql.= " AND (vLabel  LIKE '%".addslashes($keyword)."%' OR vValue  LIKE '%".addslashes($keyword)."%') ";
        }
    }     
    $tbl_name = 'language_label';

    $sql = "SELECT vLabel as `Code`,vValue as `Value in English Language`  FROM ".$tbl_name." WHERE vCode = '".$default_lang."' $ssql $ord"; 
    
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Code','Value in English Language');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Languages");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
           if ($column_heading == 'Status') {
                $pdf->Cell(88, 10, $column_heading, 1);
            } else {
                $pdf->Cell(88, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Status') {
                    $pdf->Cell(88, 10, $key, 1);
                } else {
                    $pdf->Cell(88, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');        
    }
}

//language label other
if ($section == 'language_label_other') {
    $ord = ' ORDER BY vValue ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vLabel ASC";
      else
      $ord = " ORDER BY vLabel DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY vValue ASC";
      else
      $ord = " ORDER BY vValue DESC";
    }

    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (vLabel LIKE '%".$keyword."%' OR vValue LIKE '%".$keyword."%')";
        }
    }   
    
    $tbl_name = 'language_label_other';
    $sql = "SELECT vLabel as `Code`,vValue as `Value in English Language`  FROM ".$tbl_name." WHERE vCode = '".$default_lang."' $ssql $ord";
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Code','Value in English Language');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Admin Language Label");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
           if ($column_heading == 'Status') {
                $pdf->Cell(88, 10, $column_heading, 1);
            } else {
                $pdf->Cell(88, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Status') {
                    $pdf->Cell(88, 10, $key, 1);
                } else {
                    $pdf->Cell(88, 10, $key, 1);
                }
            }
        }
       $pdf->Output('D');        
    }
}
//language label other

//vehicle_type
if ($section == 'vehicle_type') {

    $iVehicleCategoryId = isset($_REQUEST['iVehicleCategoryId']) ? $_REQUEST['iVehicleCategoryId'] : "";
    $eType = isset($_REQUEST['eType'])?($_REQUEST['eType']):"";

    $ord = ' ORDER BY vt.vVehicleType_'.$default_lang.' ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vt.vVehicleType_".$default_lang." ASC";
      else
      $ord = " ORDER BY vt.vVehicleType_".$default_lang." DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY vt.fPricePerKM ASC";
      else
      $ord = " ORDER BY vt.fPricePerKM DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY vt.fPricePerMin ASC";
      else
      $ord = " ORDER BY vt.fPricePerMin DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY vt.iPersonSize ASC";
      else
      $ord = " ORDER BY vt.iPersonSize DESC";
    }
    if ($keyword != '') {
        if ($option != '') {
            if($iVehicleCategoryId != '') {
                $ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."'";
            } else {
                $ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        } else {
            if($iVehicleCategoryId != '') {
                $ssql.= " AND (vt.vVehicleType_".$default_lang." LIKE '%".$keyword."%' OR vt.fPricePerKM LIKE '%".$keyword."%' OR vt.fPricePerMin LIKE '%".$keyword."%' OR vt.iPersonSize    LIKE '%".$keyword."%') AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."'";
            } else {
                $ssql.= " AND (vt.vVehicleType_".$default_lang." LIKE '%".$keyword."%' OR vt.fPricePerKM LIKE '%".$keyword."%' OR vt.fPricePerMin LIKE '%".$keyword."%' OR vt.iPersonSize   LIKE '%".$keyword."%')";
            }
        }
    } else if( $iVehicleCategoryId != '' && $keyword == '') {
         $ssql.= " AND vt.iVehicleCategoryId = '".$iVehicleCategoryId."'";
    } else if( $eType != '' && $keyword == '') {
      $ssql.= " AND vt.eType = '".$eType."'";
    }

    $Vehicle_type_name = ($APP_TYPE == 'Delivery')? 'Deliver':$APP_TYPE ;
   //$sql = "SELECT vt.vVehicleType_".$default_lang." as Type,vt.fPricePerKM as PricePerKM,vt.fPricePerMin as PricePerMin,vt.iBaseFare as BaseFare,vt.fCommision as Commision,vt.iPersonSize as PersonSize,lm.vLocationName as location,vt.iLocationid as locationId  from  vehicle_type as vt left join location_master as lm ON lm.iLocationId = vt.iLocationid where vt.eType='".$Vehicle_type_name."'  $ssql $ord"; 
    
    if($Vehicle_type_name == "Ride-Delivery") {
        if(empty($eType)){
            $ssql = "AND (vt.eType ='Ride' or vt.eType ='Deliver')";
          }
        $sql = "SELECT vt.vVehicleType_".$default_lang." as Type,vt.fPricePerKM as PricePer".$DEFAULT_DISTANCE_UNIT.",vt.fPricePerMin as PricePerMin,vt.iBaseFare as BaseFare,vt.fCommision as Commision,vt.iPersonSize as PersonSize,lm.vLocationName as location,vt.iLocationid as locationId from  vehicle_type as vt left join location_master as lm ON lm.iLocationId = vt.iLocationid where 1=1 $ssql $ord";
     } else {
        if($APP_TYPE == 'UberX') {
            $sql = "SELECT vt.vVehicleType_".$default_lang." as Type,vc.vCategory_".$default_lang." as Subcategory,lm.vLocationName as location,vt.iLocationid as locationId from vehicle_type as vt  left join vehicle_category as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId left join country as c ON c.iCountryId = vt.iCountryId left join state as st ON st.iStateId = vt.iStateId left join city as ct ON ct.iCityId = vt.iCityId left join location_master as lm ON lm.iLocationId = vt.iLocationid where vt.eType='".$Vehicle_type_name."' $ssql $ord";  
        } else if($APP_TYPE == 'Ride-Delivery-UberX') {
            $sql = "SELECT vt.vVehicleType_".$default_lang." as Type,vt.fPricePerKM as PricePer".$DEFAULT_DISTANCE_UNIT.",vt.fPricePerMin as PricePerMin,vt.iBaseFare as BaseFare,vt.fCommision as Commision,vt.iPersonSize as PersonSize,lm.vLocationName as location,vt.iLocationid as locationId from vehicle_type as vt left join country as c ON c.iCountryId = vt.iCountryId left join state as st ON st.iStateId = vt.iStateId left join city as ct ON ct.iCityId = vt.iCityId left join location_master as lm ON lm.iLocationId = vt.iLocationid  where 1=1 $ssql $ord";
        } else {
            $sql = "SELECT vt.vVehicleType_".$default_lang." as Type,vt.fPricePerKM as PricePer".$DEFAULT_DISTANCE_UNIT.",vt.fPricePerMin as PricePerMin,vt.iBaseFare as BaseFare,vt.fCommision as Commision,vt.iPersonSize as PersonSize,lm.vLocationName as location,vt.iLocationid as locationId  from  vehicle_type as vt left join location_master as lm ON lm.iLocationId = vt.iLocationid where vt.eType='".$Vehicle_type_name."'  $ssql $ord";
        }
     }


    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
        $data = array_keys($result[0]);
        $arr = array_diff($data, array("locationId"));
        echo implode("\t", $arr) . "\r\n";
         $i = 0;
        foreach($result as $value){
            foreach($value as $key=>$val) {
                if($key == 'locationId'){
                    $val = "";
                }
                if($key == 'location' && $value['locationId'] == '-1'){
                    $val = "All Location";
                }
                echo $val."\t";
            }
            echo "\r\n";
            $i++;
        }
    } else {
        if($APP_TYPE == 'UberX') {
            $heading = array('Type','Subcategory','Location Name');
        } else {
            $heading = array('Type','PricePer'.$DEFAULT_DISTANCE_UNIT,'PricePerMin','BaseFare','Commision','PersonSize','Location Name');
        }
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        //print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Vehicle Type");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Type' && $APP_TYPE == 'UberX') {
                $pdf->Cell(80, 10, $column_heading, 1);
            } else if ($column_heading == 'Type' && $APP_TYPE != 'UberX'){
                 $pdf->Cell(30, 10, $column_heading, 1);
            }  else if ($column_heading == 'PricePerKM') {
                $pdf->Cell(25, 10, $column_heading, 1);
            }  else if ($column_heading == 'BaseFare') {
                $pdf->Cell(25, 10, $column_heading, 1);             
            } else if ($column_heading == 'Commision') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else if ($column_heading == 'PersonSize') {
                $pdf->Cell(25, 10, $column_heading, 1);
            }else if ($column_heading == 'Location Name') {
                $pdf->Cell(35, 10, $column_heading, 1);
            } else if ($column_heading == 'Subcategory') {
                $pdf->Cell(50, 10, $column_heading, 1);
            } else {
                $pdf->Cell(26, 10, $column_heading, 1); 
            } 
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
               if ($column == 'Type' && $APP_TYPE == 'UberX') {
                    $pdf->Cell(80, 10, $key, 1);
                } else if ($column == 'Type' && $APP_TYPE != 'UberX'){
                    $pdf->Cell(30, 10, $key, 1);
                } else if ($column == 'PricePerKM') {
                    $pdf->Cell(25, 10, $key, 1);
                } else if ($column == 'BaseFare') {
                    $pdf->Cell(25, 10, $key, 1);    
                } else if ($column == 'Commision') {
                    $pdf->Cell(25, 10, $key, 1);
                } else if ($column == 'PersonSize') {
                    $pdf->Cell(25, 10, $key, 1);
                } else if ($column == 'location' && $row['locationId'] == "-1") {
                    $pdf->Cell(35, 10, 'All Location', 1);
                } else if ($column == 'locationId') {
                    $pdf->Cell(2, 10, '', 0);
                } else if ($column == 'Subcategory') {
                    $pdf->Cell(50, 10, $key, 1);
                } else {
                    $pdf->Cell(26, 10, $key, 1);
                } 
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
        }
}
//vehicle_type


//coupon
if ($section == 'coupon') {

    $ord = ' ORDER BY vCouponCode ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vCouponCode ASC";
      else
      $ord = " ORDER BY vCouponCode DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY dActiveDate ASC";
      else
      $ord = " ORDER BY dActiveDate DESC";
    }
    if($sortby == 5){
      if($order == 0)
      $ord = " ORDER BY dExpiryDate ASC";
      else
      $ord = " ORDER BY dExpiryDate DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY eValidityType ASC";
      else
      $ord = " ORDER BY eValidityType DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    }

    if($sortby == 6){
      if($order == 0)
      $ord = " ORDER BY iUsageLimit ASC";
      else
      $ord = " ORDER BY iUsageLimit DESC";
    }

    if($sortby == 7){
      if($order == 0)
      $ord = " ORDER BY iUsed ASC";
      else
      $ord = " ORDER BY iUsed DESC";
    }
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (vCouponCode LIKE '%".$keyword."%'  OR eValidityType LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }                                   
    
    // $sql = "SELECT *,DATE_FORMAT(dExpiryDate,'%d/%m/%Y') AS dExpiryDate,DATE_FORMAT(dActiveDate,'%d/%m/%Y') AS dActiveDate FROM coupon WHERE eStatus != 'Deleted' $ssql $adm_ssql";
    $sql = "SELECT vCouponCode as `Gift Certificate`,fDiscount as `Discount`,eValidityType as `ValidityType`,DATE_FORMAT(dActiveDate,'%d/%m/%Y') AS `Active Date`,DATE_FORMAT(dExpiryDate,'%d/%m/%Y') AS `ExpiryDate`,iUsageLimit as `Usage Limit`,iUsed as `Used`,eStatus as `Status` FROM coupon WHERE eStatus != 'Deleted' $ssql $ord"; 
    
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
		
		// echo "<pre>";print_r($result);exit;
        while($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Gift Certificate','Discount','ValidityType','Active Date','ExpiryDate','Usage Limit','Used','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        //print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Coupon");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Gift Certificate') {
                $pdf->Cell(24, 10, $column_heading, 1);
            }  else if ($column_heading == 'Discount') {
                $pdf->Cell(20, 10, $column_heading, 1);
             }   else if ($column_heading == 'Validity Type') {
                $pdf->Cell(26, 10, $column_heading, 1);
            }  else if ($column_heading == 'Active Date') {
                $pdf->Cell(28, 10, $column_heading, 1);             
            } else if ($column_heading == 'ExpiryDate') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else if ($column_heading == 'Usage Limit') {
                $pdf->Cell(24, 10, $column_heading, 1); 
            } else if ($column_heading == 'Used') {
                $pdf->Cell(22, 10, $column_heading, 1);
            }   
             else if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            }               
            else {
                $pdf->Cell(25, 10, $column_heading, 1); 
            } 
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                //echo '<pre>'; print_r($column);die;
                if ($column == 'Gift Certificate') {
                    $pdf->Cell(24, 10, $key, 1);
                }  else if ($column == 'Discount') {
                    
                        $key = $key.' $';
                    $pdf->Cell(20, 10, $key, 1);
                }   else if ($column == 'ValidityType') {                        
                        if($key=='Defined'){
                        $key='Custom';
                         $pdf->Cell(25, 10, $key, 1);   
                    }else{
                     $pdf->Cell(25, 10, $key, 1);       
                    }
                    //$pdf->Cell(26, 10, $key, 1);                  
                    
                } else if ($column == 'Active Date') {
                    /* if($key=='00/00/0000'){
                        $key='---';
                         $pdf->Cell(28, 10, $key, 1);   
                    }else{
                     $pdf->Cell(28, 10, $key, 1);       
                    } */
                    $pdf->Cell(28, 10, $key, 1);
                }   else if ($column == 'ExpiryDate') {
                    $pdf->Cell(25, 10, $key, 1);
                } else if ($column == 'Usage Limit') {
                    $pdf->Cell(24, 10, $key, 1);
                }
                else if ($column == 'Used') {
                    $pdf->Cell(22, 10, $key, 1);
                }
                else if ($column == 'Status') {
                    $pdf->Cell(25, 10, $key, 1);
                }
                
                else {
                    $pdf->Cell(25, 10, $key, 1);
                } 
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
        }
}
//coupon


//driver 
if ($section == 'driver') {
    
    $ord = ' ORDER BY rd.iDriverId DESC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY rd.vName ASC";
      else
      $ord = " ORDER BY rd.vName DESC";
    }
    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY c.vCompany ASC";
      else
      $ord = " ORDER BY c.vCompany DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY rd.vEmail ASC";
      else
      $ord = " ORDER BY rd.vEmail DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY rd.tRegistrationDate ASC";
      else
      $ord = " ORDER BY rd.tRegistrationDate DESC";
    }

    if($sortby == 5){
      if($order == 0)
      $ord = " ORDER BY rd.eStatus ASC";
      else
      $ord = " ORDER BY rd.eStatus DESC";
    }
    
    if ($keyword != '') {
        if ($option != '') {
            $option_new = $option;
            if($option == 'DriverName'){
              $option_new = "CONCAT(rd.vName,' ',rd.vLastName)";
            }

            if($eStatus != ''){
                $ssql .= " AND " . stripslashes($option_new) . " LIKE '%" . stripslashes($keyword) . "%' AND rd.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql .= " AND " . stripslashes($option_new) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            if($eStatus != ''){
                $ssql .= " AND (CONCAT(rd.vName,' ',rd.vLastName) LIKE '%".$keyword."%' OR c.vCompany LIKE '%".$keyword."%' OR rd.vEmail LIKE '%".$keyword."%' OR rd.tRegistrationDate LIKE '%".$keyword."%' OR rd.vPhone LIKE '%".$keyword."%') AND rd.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql .= " AND (CONCAT(rd.vName,' ',rd.vLastName) LIKE '%".$keyword."%' OR c.vCompany LIKE '%".$keyword."%' OR rd.vEmail LIKE '%".$keyword."%' OR rd.tRegistrationDate LIKE '%".$keyword."%' OR rd.vPhone LIKE '%".$keyword."%')";
            }
        }
    }  else if( $eStatus != '' && $keyword == '') {
         $ssql.= " AND rd.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
    }
    
    $dri_ssql = "";
    if (SITE_TYPE == 'Demo') {
        $dri_ssql = " And rd.tRegistrationDate > '" . WEEK_DATE . "'";
    }

    if($eStatus != '') { 
        $eStatus_sql = "";
    } else {
        $eStatus_sql = " AND rd.eStatus != 'Deleted'"; 
    }

    $sql = "SELECT CONCAT(rd.vName,' ',rd.vLastName) AS `Driver Name`,c.vCompany as `Company Name`,rd.vEmail as `Email Id`,(select count(dv2.iDriverVehicleId)  from driver_vehicle as dv2 where dv2.iDriverId=rd.iDriverId ) as `Taxi Count`, rd.tRegistrationDate as `signupdate`,rd.vPhone as `Phone`,rd.eStatus as `status` FROM register_driver rd LEFT JOIN company c ON rd.iCompanyId = c.iCompanyId 
    WHERE 1 = 1  $eStatus_sql $ssql $dri_ssql $ord"; 
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";


        header("Content-Disposition: attachment; filename=\"$filename\"");
        //header("Content-Type: application/vnd.ms-excel");
		//header('Content-Type: text/xml,  charset=windows-1256; enucoding=windows-1256');
		//header("Content-type:html/vnd.ms-excel; charset=utf-8");
		header("Content-type:text/html; charset=utf-8");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
        echo implode("\t", array_keys($result[0])) . "\r\n";
        
        foreach($result as $value){
            foreach($value as $key=>$val) {
            // echo $key.' => '.$val;
                if($key == 'Driver Name'){
                    $val = $generalobjAdmin->clearName($val);
					//$val = iconv("UTF-8", "iso-8859-6", $generalobjAdmin->clearName($val));
                }
                if($key == 'Email Id'){
                    $val = $generalobjAdmin->clearEmail($val);
                }
                if($key == 'Phone'){
                    $val = $generalobjAdmin->clearPhone($val);
                }
                if($key == 'Company Name'){
                    $val = $generalobjAdmin->clearCmpName($val);
                }
                echo $val."\t";
            }
            echo "\r\n";
        }
    } else {
        $heading = array('Driver Name','Company Name','Email Id','Count','signupdate','Phone','Status');
        $result = $obj->ExecuteQuery($sql);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        //print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Drivers");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Driver Name') {
                $pdf->Cell(29, 10, $column_heading, 1);
            }  else if ($column_heading == 'Company Name') {
                $pdf->Cell(35, 10, $column_heading, 1);
             }   else if ($column_heading == 'Email Id') {
                $pdf->Cell(46, 10, $column_heading, 1);
            }  else if ($column_heading == 'TaxiCount') {
                $pdf->Cell(2, 10, $column_heading, 1);              
            } else if ($column_heading == 'signupdate') {
                $pdf->Cell(31, 10, $column_heading, 1);
            } else if ($column_heading == 'Phone') {
                $pdf->Cell(22, 10, $column_heading, 1); 
            } else if ($column_heading == 'status') {
                $pdf->Cell(20, 10, $column_heading, 1);
            }                           
            else {
                $pdf->Cell(20, 10, $column_heading, 1); 
            }         }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                //echo '<pre>'; print_r($result);die;
                $values = $key;
                if($column == 'Driver Name'){
                    $values = $generalobjAdmin->clearName($key);
                }
                if($column == 'Email Id'){
                    $values = $generalobjAdmin->clearEmail($key);
                }
                if($column == 'Phone'){
                    $values = $generalobjAdmin->clearPhone($key);
                }
                if($column == 'Company Name'){
                    $values = $generalobjAdmin->clearCmpName($key);
                }
				
                if ($column == 'Driver Name') {
                    $pdf->Cell(29, 10, $values, 1 , 0 ,"1");
                }  else if ($column == 'Company Name') {                            
                    $pdf->Cell(35, 10, $values, 1);
                }   else if ($column == 'Email Id') {                   
                    $pdf->Cell(46, 10, $values, 1); 
                }  else if ($column == 'TaxiCount') {
                   $pdf->Cell(2, 10, $values, 1);   
                }   else if ($column == 'signupdate') {
                    $pdf->Cell(31, 10, $values, 1);
                } else if ($column == 'Phone') {
                    $pdf->Cell(22, 10, $values, 1);
                } else if ($column == 'status') {
                    $pdf->Cell(20, 10, $values, 1);
                }
                                
                else {
                    $pdf->Cell(20, 10, $key, 1);
                } 
            }
        }
        $pdf->Output('D');
        // $pdf->Output();
        }
}
//driver

//vehicles 
if ($section == 'vehicles') {
   
    $ord = ' ORDER BY dv.iDriverVehicleId DESC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY m.vMake ASC";
      else
      $ord = " ORDER BY m.vMake DESC";
    }
    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY c.vCompany ASC";
      else
      $ord = " ORDER BY c.vCompany DESC";
    }
    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY rd.vName ASC";
      else
      $ord = " ORDER BY rd.vName DESC";
    }

    if($sortby == 5){
      if($order == 0)
      $ord = " ORDER BY dv.eStatus ASC";
      else
      $ord = " ORDER BY dv.eStatus DESC";
    }
    //End Sorting

    $dri_ssql = "";
    if (SITE_TYPE == 'Demo') {
        $dri_ssql = " And rd.tRegistrationDate > '" . WEEK_DATE . "'";
    }

    // Start Search Parameters
    $option = isset($_REQUEST['option'])?stripslashes($_REQUEST['option']):"";
    $keyword = isset($_REQUEST['keyword'])?stripslashes($_REQUEST['keyword']):"";
    $searchDate = isset($_REQUEST['searchDate'])?$_REQUEST['searchDate']:"";
    $iDriverId = isset($_REQUEST['iDriverId'])?$_REQUEST['iDriverId']:"";
    $ssql = '';
    if($keyword != ''){
        if($option != '') {
           if($eStatus != ''){
                $ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND dv.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        }else {
            if($eStatus != ''){
                $ssql.= " AND (m.vMake LIKE '%".$keyword."%' OR c.vCompany LIKE '%".$keyword."%' OR CONCAT(rd.vName,' ',rd.vLastName) LIKE '%".$keyword."%')  AND dv.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql.= " AND (m.vMake LIKE '%".$keyword."%' OR c.vCompany LIKE '%".$keyword."%' OR CONCAT(rd.vName,' ',rd.vLastName) LIKE '%".$keyword."%')";
            }
        }
    } else if( $eStatus != '' && $keyword == '') {
             $ssql.= " AND dv.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
    }
    // End Search Parameters

    if($iDriverId != "") {
        $query1="SELECT COUNT(iDriverVehicleId) as total FROM driver_vehicle where iDriverId ='".$iDriverId."'";
        $totalData = $obj->MySQLSelect($query1);
        $total_vehicle = $totalData[0]['total'];
        if($total_vehicle > 1){
           $ssql .= " AND dv.iDriverId='".$iDriverId."'";
        }
    }

    //Pagination Start
    $per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page

    if($eStatus != '') { 
        $eStatus_sql = "";
    } else {
        $eStatus_sql = " AND dv.eStatus != 'Deleted'"; 
    }

    if($APP_TYPE == 'UberX'){
        $sql = "SELECT COUNT(dv.iDriverVehicleId) AS Total
        FROM driver_vehicle AS dv, register_driver rd, make m, model md, company c
        WHERE 1 = 1 AND dv.iDriverId = rd.iDriverId  AND dv.iCompanyId = c.iCompanyId".$eStatus_sql.$ssql.$dri_ssql;
    }else{
        $sql = "SELECT COUNT(dv.iDriverVehicleId) AS Total
            FROM driver_vehicle AS dv, register_driver rd, make m, model md, company c
            WHERE 1 = 1
            AND dv.iDriverId = rd.iDriverId
            AND dv.iCompanyId = c.iCompanyId
            AND dv.iModelId = md.iModelId
            AND dv.iMakeId = m.iMakeId".$eStatus_sql.$ssql.$dri_ssql ;
    }

    $totalData = $obj->MySQLSelect($sql);
    $total_results = $totalData[0]['Total'];
    $total_pages = ceil($total_results / $per_page); //total pages we going to have
    $show_page = 1;

    //-------------if page is setcheck------------------//
    if (isset($_GET['page'])) {
        $show_page = $_GET['page'];             //it will telles the current page
        if ($show_page > 0 && $show_page <= $total_pages) {
            $start = ($show_page - 1) * $per_page;
            $end = $start + $per_page;
        } else {
            // error - show first set of results
            $start = 0;
            $end = $per_page;
        }
    } else {
        // if page isn't set, show first set of results
        $start = 0;
        $end = $per_page;
    }
    // display pagination
    $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
    $tpages=$total_pages;
    if ($page <= 0)
        $page = 1;
    //Pagination End

    if($APP_TYPE == 'UberX'){

        $sql = "SELECT dv.iDriverVehicleId,dv.eStatus,CONCAT(rd.vName,' ',rd.vLastName) AS driverName,dv.vLicencePlate, c.vCompany FROM driver_vehicle dv, register_driver rd,company c
            WHERE 1 = 1   AND dv.iDriverId = rd.iDriverId  AND dv.iCompanyId = c.iCompanyId $eStatus_sql $ssql $dri_ssql";
    }else{
        $sql = "SELECT  CONCAT(m.vMake,' ', md.vTitle) AS TAXIS, c.vCompany AS Company, CONCAT(rd.vName,' ',rd.vLastName) AS Driver ,dv.eStatus as Status
            FROM driver_vehicle dv, register_driver rd, make m, model md, company c
            WHERE 1 = 1
            AND dv.iDriverId = rd.iDriverId
            AND dv.iCompanyId = c.iCompanyId
            AND dv.iModelId = md.iModelId
            AND dv.iMakeId = m.iMakeId $eStatus_sql $ssql $dri_ssql $ord ";//LIMIT $start, $per_page
    }


    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
           $result = $obj->MySQLSelect($sql) or die('Query failed!');
        echo implode("\t", array_keys($result[0])) . "\r\n";
        
        foreach($result as $value){
            foreach($value as $key=>$val) {
            // echo $key.' => '.$val;
                if($key == 'TAXIS'){
                    $val;
                }
                if($key == 'Company'){
                    $val = $generalobjAdmin->clearCmpName($val);
                }
                if($key == 'Driver'){
                    $val = $generalobjAdmin->clearName($val);
                }
                if($key == 'Status'){
                    $val ;
                }
                echo $val."\t";
            }
            echo "\r\n";
        }
    } else {
        $heading = array('TAXIS','Company','Driver','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        //print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Taxis");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'TAXIS') {
                $pdf->Cell(70, 10, $column_heading, 1);
            }  else if ($column_heading == 'Company') {
                $pdf->Cell(45, 10, $column_heading, 1);
        }   else if ($column_heading == 'Driver') {
                $pdf->Cell(45, 10, $column_heading, 1);
            }   else if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            }                           
            else {
                $pdf->Cell(45, 10, $column_heading, 1); 
            } 
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                //echo '<pre>'; print_r($result);die;
                if ($column == 'TAXIS') {
                    $pdf->Cell(70, 10, $key, 1);
                }  else if ($column == 'Company') {                         
                    $pdf->Cell(45, 10, $generalobjAdmin->clearCmpName($key), 1);
                }   else if ($column == 'Driver') {                 
                  $pdf->Cell(45, 10, $generalobjAdmin->clearName($key), 1); //}
                }   else if ($column == 'Status') {
                    $pdf->Cell(25, 10, $key, 1);
                }                               
                else {
                    $pdf->Cell(45, 10, $key, 1);
                } 
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
        }
}
//vehicles

//email_template
$default_lang   = $generalobj->get_default_lang();
if ($section == 'email_template') {
    $ord = ' ORDER BY vSubject_'.$default_lang.' ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vSubject_".$default_lang." ASC";
      else
      $ord = " ORDER BY vSubject_".$default_lang." DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY vEmail_Code ASC";
      else
      $ord = " ORDER BY vEmail_Code DESC";
    }

     if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    } 
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (vSubject_".$default_lang." LIKE '%".$keyword."%' OR vEmail_Code LIKE '%".$keyword."%')";
        }
    }
    $default_lang   = $generalobj->get_default_lang();
    $tbl_name       = 'email_templates';
    $sql = "SELECT vSubject_".$default_lang." as `Email Subject`, vEmail_Code as `Email Code` FROM ".$tbl_name." WHERE eStatus = 'Active' $ssql $ord"; 
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Email Subject','Email Code');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Email Templates");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Email Subject') {
                $pdf->Cell(98, 10, $column_heading, 1);
            } else if ($column_heading == 'Email Code') {
                $pdf->Cell(98, 10, $column_heading, 1);
            } else {
                $pdf->Cell(8, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Email Subject') {
                    $pdf->Cell(98, 10, $key, 1);
                } else if ($column == 'Email Code') {
                    $pdf->Cell(98, 10, $key, 1);
                } else {
                    $pdf->Cell(8, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}
//email_template

//Restricted Area
if ($section == 'restrict_area') {

    $ord = ' ORDER BY lm.vLocationName ASC';
    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY lm.vLocationName ASC";
      else
      $ord = " ORDER BY lm.vLocationName DESC";
    }

    if($sortby == 5){
      if($order == 0)
      $ord = " ORDER BY ra.eRestrictType ASC";
      else
      $ord = " ORDER BY ra.eRestrictType DESC";
    }

    if($sortby == 6){
      if($order == 0)
      $ord = " ORDER BY ra.eStatus ASC";
      else
      $ord = " ORDER BY ra.eStatus DESC";
    }

    if($sortby == 7){
      if($order == 0)
      $ord = " ORDER BY ra.eType ASC";
      else
      $ord = " ORDER BY ra.eType DESC";
    }
    //End Sorting
    
    if($keyword != ''){
        if($option != '') {
            if (strpos($option, 'ra.eStatus') !== false) {
                $ssql.= " AND ".stripslashes($option)." LIKE '".stripslashes($generalobjAdmin->clean($keyword))."'";
            }else {
                $ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($generalobjAdmin->clean($keyword))."%'";
            }
        }else {
            //$ssql.= " AND (c.vCountry LIKE '%".$keyword."%' OR st.vState LIKE '%".$keyword."%' OR ct.vCity LIKE '%".$keyword."%' OR ra.vAddress LIKE '%".$keyword."%' OR ra.eStatus LIKE '%".$keyword."%')";
            $ssql.= " AND (lm.vLocationName LIKE '%".$generalobjAdmin->clean($keyword)."%' OR ra.eStatus LIKE '%".$generalobjAdmin->clean($keyword)."%' OR ra.eRestrictType LIKE '%".$generalobjAdmin->clean($keyword)."%' OR ra.eType LIKE '%".$generalobjAdmin->clean($keyword)."%')";
        }
    }
    $sql = "SELECT lm.vLocationName as Address, ra.eRestrictType AS Area, ra.eType AS Type, ra.eStatus AS Status FROM restricted_negative_area AS ra LEFT JOIN location_master AS lm ON lm.iLocationId=ra.iLocationId WHERE 1=1 $ssql $ord";
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Address','Area','Type','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Address");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Area') {
                $pdf->Cell(40, 10, $column_heading, 1);
            }else if ($column_heading == 'Address') {
                $pdf->Cell(80, 10, $column_heading, 1);
            } else {
                $pdf->Cell(40, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Area') {
                    $pdf->Cell(40, 10, $key, 1);
                }else if ($column == 'Address') {
                    $pdf->Cell(80, 10, $key, 1);
                } else {
                    $pdf->Cell(40, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
        //$pdf->Output();
    }
}


//visit location 
if ($section == 'visitlocation') {
    $ord = ' ORDER BY iVisitId DESC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vSourceAddresss ASC";
      else
      $ord = " ORDER BY vSourceAddresss DESC";
    }

     if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY tDestAddress ASC";
      else
      $ord = " ORDER BY tDestAddress DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    }
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
           $ssql.= " AND (vSourceAddresss LIKE '%".$keyword."%' OR tDestAddress LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT vSourceAddresss as SourceAddress, tDestAddress as DestAddress,eStatus as Status FROM visit_address where eStatus != 'Deleted' $ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
        $heading = array('SourceAddress','DestAddress','Status');
    } else {
        $heading = array('SourceAddress','DestAddress','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        //print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Visit Location");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'SourceAddress') {
                $pdf->Cell(75, 10, $column_heading, 1);
        }   else if ($column_heading == 'DestAddress') {
                $pdf->Cell(75, 10, $column_heading, 1);
            }   else if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            }                           
            else {
                $pdf->Cell(45, 10, $column_heading, 1); 
            } 
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                //echo '<pre>'; print_r($result);die;
                if ($column == 'SourceAddress') {                         
                    $pdf->Cell(75, 10, $generalobjAdmin->clearCmpName($key), 1);
                }   else if ($column == 'DestAddress') {                 
                  $pdf->Cell(75, 10, $generalobjAdmin->clearName($key), 1); //}
                }   else if ($column == 'Status') {
                    $pdf->Cell(25, 10, $key, 1);
                }                               
                else {
                    $pdf->Cell(45, 10, $key, 1);
                } 
            }
        }
		//Output('D');
        $pdf->Output('D');
    }
}

//hotel rider

if ($section == 'hotel_rider') {

    $ord = ' ORDER BY vName ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vName ASC";
      else
      $ord = " ORDER BY vName DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY vEmail ASC";
      else
      $ord = " ORDER BY vEmail DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY tRegistrationDate ASC";
      else
      $ord = " ORDER BY tRegistrationDate DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    }
    $rdr_ssql = "";
    if (SITE_TYPE == 'Demo') {
        $rdr_ssql = " And tRegistrationDate > '" . WEEK_DATE . "'";
    }
    
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (concat(vFirstName,' ',vLastName) LIKE '%".$keyword."%' OR vEmail LIKE '%".$keyword."%' OR vPhone LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

    $sql = "SELECT  CONCAT(vName,' ',vLastName) as Name,vEmail as Email,CONCAT(vPhoneCode,' ',vPhone) AS Mobile,eStatus as Status FROM hotel WHERE eStatus != 'Deleted' $ssql $rdr_ssql $ord";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
       // echo implode("\t", array_keys($result[0])) . "\r\n";
        
        foreach($result as $value){
            foreach($value as $key=>$val) {
            // echo $key.' => '.$val;
                if($key == 'Name'){
                    $val = $generalobjAdmin->clearName($val);
                }
                if($key == 'Email'){
                    $val = $generalobjAdmin->clearEmail($val);
                }
                if($key == 'Mobile'){
                    $val = $generalobjAdmin->clearPhone($val);
                }
                echo $val."\t";
            }
            echo "\r\n";
        }
    } else {
        $heading = array('Name', 'Email', 'Mobile', 'Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Hotel Riders");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Email') {
                $pdf->Cell(55, 10, $column_heading, 1);
            } else if ($column_heading == 'Mobile') {
                $pdf->Cell(45, 10, $column_heading, 1);
            } else if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                $values = $key;
                if($column == 'Name'){
                    $values = $generalobjAdmin->clearName($key);
                }
                if($column == 'Email'){
                    $values = $generalobjAdmin->clearEmail($key);
                }
                if($column == 'Mobile'){
                    $values = $generalobjAdmin->clearPhone($key);
                }
                if ($column == 'Email') {
                    $pdf->Cell(55, 10, $values, 1);
                } else if ($column == 'Mobile') {
                    $pdf->Cell(45, 10, $values, 1);
                } else if ($column == 'Status') {
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}
if ($section == 'sub_service_category') {   
  global $tconfig;
	$sub_cid = isset($_REQUEST['sub_cid']) ? $_REQUEST['sub_cid'] : '';

    $ord = ' ORDER BY vCategory_'.$default_lang.' ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vCategory_".$default_lang." ASC";
      else
      $ord = " ORDER BY vCategory_".$default_lang." DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    }
    if ($keyword != '') {
        if ($option != '') {
            if($eStatus != ''){
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'  AND eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            if($eStatus != ''){
                $ssql.= " AND (vCategory_".$default_lang." LIKE '%".$generalobjAdmin->clean($keyword)."%') AND eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql.= " AND (vCategory_".$default_lang." LIKE '%".$generalobjAdmin->clean($keyword)."%')";
            }
        }
    } else if( $eStatus != '' && $keyword == '' ) {
         $ssql.= " AND eStatus = '".$generalobjAdmin->clean($eStatus)."'";
    }

    if($parent_ufx_catid != "0") {
        $sql = "SELECT vCategory_".$default_lang." as SubCategory, (SELECT vCategory_".$default_lang." FROM vehicle_category WHERE iVehicleCategoryId='".$sub_cid."') as Category, (select count(iVehicleTypeId) from vehicle_type where vehicle_type.iVehicleCategoryId = vehicle_category.iVehicleCategoryId) as `Service Types`, eStatus as Status FROM vehicle_category WHERE 1 = 1 $ssql $ord";
    } else {
        $sql = "SELECT vCategory_".$default_lang." as SubCategory, (SELECT vCategory_".$default_lang." FROM vehicle_category WHERE iVehicleCategoryId='".$sub_cid."') as Category,(select count(iVehicleTypeId) from vehicle_type where vehicle_type.iVehicleCategoryId = vehicle_category.iVehicleCategoryId) as `Service Types`,eStatus as Status FROM vehicle_category WHERE iParentId='".$sub_cid."' $ssql $ord";
    } 
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
        echo implode("\t", array_keys($result[0])) . "\r\n";
        
        foreach($result as $value){
            foreach($value as $key=>$val) {
                if($key == 'SubCategory'){
                    $val = $generalobjAdmin->clearName($val);
                }
                echo $val."\t";
            }
            echo "\r\n";
        }
    } else {
        $heading = array('SubCategory', 'Category' ,'Service Types','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Sub Category");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
           if ($column_heading == 'Status') {
                $pdf->Cell(25, 10, $column_heading, 1);
            } else if($column_heading == 'Service Types'){
                $pdf->Cell(25, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
				
                $values = $key;
				$id= "";
				 if($column == 'iVehicleCategoryId'){
					$id2 = $key;					 
				 }
				
                if($column == 'SubCategory'){
					
                    $values = $generalobjAdmin->clearName($key);
                }
				
				
               /*  if($column == 'Icon'){
					
                    $values = '<img src="'.$tconfig['tsite_upload_images_vehicle_category']."/".$id2."/ios/3x_".$key.'>';
                } */
                
                if ($column == 'Status') {
                    $pdf->Cell(25, 10, $values, 1);
                } else if($column == 'Service Types'){
                    $pdf->Cell(25, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}

if ($section == 'service_category') {
  global $tconfig;
	$sub_cid = isset($_REQUEST['sub_cid']) ? $_REQUEST['sub_cid'] : '';   
    
    $ord = ' ORDER BY vc.vCategory_'.$default_lang.' ASC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vc.vCategory_".$default_lang." ASC";
      else
      $ord = " ORDER BY vc.vCategory_".$default_lang." DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY vc.eStatus ASC";
      else
      $ord = " ORDER BY vc.eStatus DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY SubCategories ASC";
      else
      $ord = " ORDER BY SubCategories DESC";
    }

    if ($keyword != '') {
        if ($option != '') {
            if($eStatus != ''){
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%' AND vc.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            if($eStatus != ''){
                $ssql.= " AND vc.(vCategory_".$default_lang." LIKE '%".$generalobjAdmin->clean($keyword)."%') AND vc.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
            } else {
                $ssql.= " AND vc.(vCategory_".$default_lang." LIKE '%".$generalobjAdmin->clean($keyword)."%')";
            }
        }
    } else if( $eStatus != '' && $keyword == '' ) {
         $ssql.= " AND vc.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
    }


   $sql = "SELECT vc.vCategory_".$default_lang." as Category ,(select count(iVehicleCategoryId) from vehicle_category where iParentId=vc.iVehicleCategoryId) as SubCategories,vc.eStatus as Status FROM vehicle_category as vc WHERE  vc.iParentId='0' $ssql $ord"; 

    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
        echo implode("\t", array_keys($result[0])) . "\r\n";
        
        foreach($result as $value){
            foreach($value as $key=>$val) {
                if($key == 'Category'){
                    $val = $generalobjAdmin->clearName($val);
                }
                echo $val."\t";
            }
            echo "\r\n";
        }
    } else {
        $heading = array('Category','SubCategories', 'Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);

        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Category");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
           if ($column_heading == 'Category') {
                $pdf->Cell(55, 10, $column_heading, 1);
            }  else if ($column_heading == 'Total') {
                $pdf->Cell(45, 10, $column_heading, 1);
            } else {
                $pdf->Cell(45, 10, $column_heading, 1);
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
				/* echo $column;
				echo "<br>";
				echo $key; */
                $values = $key;			 
				
                if($column == 'Category'){
					
                    $values = $generalobjAdmin->clearName($key);
                }
				
				
               if($column == 'Total'){
					
                    $values = $key;
                } 
                
                if ($column == 'Category') {
                    $pdf->Cell(55, 10, $values, 1);
                }  			
				else if ($column == 'Total') {
                    $pdf->Cell(45, 10, $values, 1);
                } else {
                    $pdf->Cell(45, 10, $values, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}

//mask_number
if ($section == 'mask_number') {
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (mask_number LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
        }
    }

     $sql = "SELECT masknum_id as `Id`, mask_number as `Masking Number`,adding_date as `Added Date`, eStatus as `Status` FROM masking_numbers where 1 = 1 $ssql";
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Id', 'Masking Number','Added Date','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Masking Numbers");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Id') {
                $pdf->Cell(18, 10, $column_heading, 1);
            } else if ($column_heading == 'Status') {
                $pdf->Cell(55, 10, $column_heading, 1);
            } else {
                $pdf->Cell(55, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Id') {
                    $pdf->Cell(18, 10, $key, 1);
                } else if ($column == 'Status') {
                    $pdf->Cell(55, 10, $key, 1);
                } else {
                    $pdf->Cell(55, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}
//mask_number



//document master
//driver 
if ($section == 'Document_Master') {
    
   $ord = ' ORDER BY dm.doc_name ASC';
    if($sortby == 1){
	  if($order == 0)
	  $ord = " ORDER BY c.vCountry ASC";
	  else
	  $ord = " ORDER BY c.vCountry DESC";
	}

	if($sortby == 2){
	  if($order == 0)
	  $ord = " ORDER BY dm.doc_usertype ASC";
	  else
	  $ord = " ORDER BY dm.doc_usertype DESC";
	}

	if($sortby == 3){
	  if($order == 0)
	  $ord = " ORDER BY dm.doc_name ASC";
	  else
	  $ord = " ORDER BY dm.doc_name DESC";
	}

	if($sortby == 4){
	  if($order == 0)
	  $ord = " ORDER BY dm.status ASC";
	  else
	  $ord = " ORDER BY dm.status DESC";
	}
		
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND (c.vCountry LIKE '%".$keyword."%' OR dm.doc_usertype LIKE '%".$keyword."%' OR dm.doc_name LIKE '%".$keyword."%' OR dm.status LIKE '%".$keyword."%')";
        }
    }
	
	if($option == "dm.status"){	
	 $eStatussql = " AND dm.status = '$keyword'";
		}else{
		 $eStatussql = " AND dm.status != 'Deleted'";
		}
    
    $dri_ssql = "";
    if (SITE_TYPE == 'Demo') {
        $dri_ssql = " And dm.doc_instime > '" . WEEK_DATE . "'";
    }
    
		$sql = "SELECT if(c.vCountry IS NULL,'All',c.vCountry) as Country,dm.doc_name as `Document Name`,dm.doc_usertype as `Document For`, dm.status as Status FROM `document_master` AS dm
        LEFT JOIN `country` AS c ON c.vCountryCode=dm.country
        WHERE 1=1 $eStatussql $ssql $dri_ssql $ord"; 
    
   // die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
        echo implode("\t", array_keys($result[0])) . "\r\n";
        
        foreach($result as $value){
            foreach($value as $key=>$val) {
            // echo $key.' => '.$val;
                
                echo $val."\t";
            }
            echo "\r\n";
        }
    } else {
        $heading = array('Country','Document Name','Document For','Status');
        $result = $obj->ExecuteQuery($sql);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        //print_r($result);die;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Documents");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Country') {
                $pdf->Cell(35, 10, $column_heading, 1);
             }   else if ($column_heading == 'Document For') {
                $pdf->Cell(35, 10, $column_heading, 1);
            }  else if ($column_heading == 'Document Name') {
                $pdf->Cell(50, 10, $column_heading, 1);              
            } else if ($column_heading == 'Status') {
                $pdf->Cell(35, 10, $column_heading, 1);
            }else {
                $pdf->Cell(20, 10, $column_heading, 1); 
            }         }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                $values = $key;
               
				
                if ($column == 'Country') {                            
                    $pdf->Cell(35, 10, $values, 1);
                }   else if ($column == 'Document For') {                   
                    $pdf->Cell(35, 10, $values, 1); 
                }  else if ($column == 'Document Name') {
                   $pdf->Cell(50, 10, $values, 1);   
                }   else if ($column == 'Status') {
                    $pdf->Cell(35, 10, $values, 1);
                }else {
                    $pdf->Cell(20, 10, $key, 1);
                } 
            }
        }
        $pdf->Output('D');
        // $pdf->Output();
        }
}
//document master

// review page 
if ($section == 'review') {
	$reviewtype = isset($_REQUEST['reviewtype']) ? $_REQUEST['reviewtype'] : 'Driver';
    $adm_ssql = "";
    if (SITE_TYPE == 'Demo') {
        $adm_ssql = " And tRegistrationDate > '" . WEEK_DATE . "'";
    }
    $ord = ' ORDER BY iRatingId DESC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY t.vRideNo ASC";
      else
      $ord = " ORDER BY t.vRideNo DESC";
    }
    if($sortby == 2)
    {
        if($reviewtype=='Driver')
        {
            if($order == 0)
            $ord = " ORDER BY rd.vName ASC";
            else
            $ord = " ORDER BY rd.vName DESC";
        }
        else
        {
            if($order == 0)
            $ord = " ORDER BY ru.vName ASC";
            else
            $ord = " ORDER BY ru.vName DESC";
        }
    }
    if($sortby == 6)
    {
        if($reviewtype=='Driver')
        {
            if($order == 0)
            $ord = " ORDER BY ru.vName ASC";
            else
            $ord = " ORDER BY ru.vName DESC";
        }
        else
        {
            if($order == 0)
            $ord = " ORDER BY rd.vName ASC";
            else
            $ord = " ORDER BY rd.vName DESC";
        }
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY r.vRating1 ASC";
      else
      $ord = " ORDER BY r.vRating1 DESC";
    }

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY r.tDate ASC";
      else
      $ord = " ORDER BY r.tDate DESC";
    }

    if($sortby == 5){
      if($order == 0)
      $ord = " ORDER BY r.vMessage ASC";
      else
      $ord = " ORDER BY r.vMessage DESC";
    }
   if($keyword != ''){
		if($option != '') {
			if (strpos($option, 'r.eStatus') !== false) {
				$ssql.= " AND ".stripslashes($option)." LIKE '".$generalobjAdmin->clean($keyword)."'";
			}else {
                $option_new = $option;
                if($option == 'drivername'){
                  $option_new = "CONCAT(rd.vName,' ',rd.vLastName)";
                } 
                if($option == 'ridername'){
                  $option_new = "CONCAT(ru.vName,' ',ru.vLastName)";
                }
				$ssql.= " AND ".stripslashes($option_new)." LIKE '%".$generalobjAdmin->clean($keyword)."%'";
			}
		}else {
			$ssql.= " AND (t.vRideNo LIKE '%".$generalobjAdmin->clean($keyword)."%' OR  concat(rd.vName,' ',rd.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR concat(ru.vName,' ',ru.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR r.vRating1 LIKE '%".$generalobjAdmin->clean($keyword)."%')";
		}
	}
	$chkusertype ="";
	if($reviewtype == "Driver")
	{
		$chkusertype = "Passenger";
	}
	else
	{
		$chkusertype = "Driver";
	}
		
		if($reviewtype == "Driver")
		{	
			$sql = "SELECT t.vRideNo as RiderNumber,CONCAT(rd.vName,' ',rd.vLastName) as DriverName,rd.vAvgRating as AverageRate,CONCAT(ru.vName,' ',ru.vLastName) as RiderName,r.vRating1 as Rate,r.tDate as Date,r.vMessage as Comment
			FROM ratings_user_driver as r LEFT JOIN trips as t ON r.iTripId=t.iTripId LEFT JOIN register_driver as rd ON rd.iDriverId=t.iDriverId LEFT JOIN register_user as ru ON ru.iUserId=t.iUserId WHERE 1=1 AND r.eUserType='".$chkusertype."' And ru.eStatus!='Deleted' $ssql $adm_ssql $ord"; 
		} else {
			$sql = "SELECT t.vRideNo as RiderNumber,CONCAT(ru.vName,' ',ru.vLastName) as RiderName,ru.vAvgRating as AverageRate,CONCAT(rd.vName,' ',rd.vLastName) as DriverName,vRating1 as Rate,r.tDate as Date,r.vMessage as Comment FROM ratings_user_driver as r LEFT JOIN trips as t ON r.iTripId=t.iTripId LEFT JOIN register_driver as rd ON rd.iDriverId=t.iDriverId LEFT JOIN register_user as ru ON ru.iUserId=t.iUserId WHERE 1=1 AND r.eUserType='".$chkusertype."' And ru.eStatus!='Deleted'  $ssql $adm_ssql $ord";
		}	
		
	//$data_drv = $obj->MySQLSelect($sql);
    //die;
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->MySQLSelect($sql) or die('Query failed!');
        echo implode("\t", array_keys($result[0])) . "\r\n";
        
        foreach($result as $value){
            foreach($value as $key=>$val) {	
                if($key == 'RiderNumber'){
                    $val = $generalobjAdmin->clearName($val);
                }
				if($reviewtype == "Driver")
				{
					if($key == 'DriverName'){
						$val = $val;
					}
				}else{
					if($key == 'RiderName'){
						$val = $val;
					}
				
				}
				
                if($key == 'AverageRate'){
                    $val = $val;
                }
				if($reviewtype == "Driver")
				{
					if($key == 'RiderName'){
						$val = $val;
					}
				}else{
					if($key == 'DriverName'){
						$val = $val;
					}				
				}	
				
				if($key == 'Rate'){
                    $val = $val;
                }
				
				if($key == 'Date'){
                    $val = $generalobjAdmin->DateTime($val);
                }
				
				if($key == 'Comment'){
                    $val =$val;
                }
                echo $val."\t";
            }
            echo "\r\n";
        }
    } else {
		if($reviewtype == "Driver")
		{
			$heading = array('RiderNumber', 'DriverName', 'AverageRate', 'RiderName', 'Rate','Date','Comment');
		}else{
		$heading = array('RiderNumber', 'RiderName', 'AverageRate', 'DriverName', 'Rate','Date','Comment');
		
		}	
		
			$result = $obj->ExecuteQuery($sql);
			while ($row = mysqli_fetch_assoc($result)) {
				$resultset[] = $row;
			}
			$result = $resultset;
			$pdf = new FPDF('P', 'mm', 'Letter');
			$pdf->AddPage();
			$pdf->SetFillColor(36, 96, 84);

			$pdf->SetFont('Arial', 'b', 15);
			$pdf->Cell(100, 16, "Review");
			$pdf->Ln();
			$pdf->SetFont('Arial', 'b', 9);
			$pdf->Ln();
			foreach ($heading as $column_heading) {
				if ($column_heading == 'RiderNumber') {
					$pdf->Cell(22, 10, $column_heading, 1);
				} else if ($column_heading == 'DriverName') {
					$pdf->Cell(40, 10, $column_heading, 1);
				} else if ($column_heading == 'AverageRate') {
					$pdf->Cell(21, 10, $column_heading, 1);
				} else if ($column_heading == 'RiderName') {
					$pdf->Cell(25, 10, $column_heading, 1);
				} else if ($column_heading == 'Rate') {
					$pdf->Cell(10, 10, $column_heading, 1);
				}
				else if ($column_heading == 'Date') {
					$pdf->Cell(42, 10, $column_heading, 1);
				}
				else {
					$pdf->Cell(45, 10, $column_heading, 1);
				}
			}
			$pdf->SetFont('Arial', '', 9);
			foreach ($result as $row) {
				$pdf->Ln();
				foreach ($row as $column => $key) {
					$values = $key;               
					if($column == 'DriverName'){
						$values = $generalobjAdmin->clearName($key);
					}
					if($column == 'Date'){
						$values = $generalobjAdmin->DateTime($key);
					}
					
					
					$generalobjAdmin->DateTime($val);
					
					if ($column == 'RiderNumber') {
						$pdf->Cell(22, 10, $values, 1);
					} else if ($column == 'DriverName') {
						$pdf->Cell(40, 10, $values, 1);
					} else if ($column == 'AverageRate') {
						$pdf->Cell(21, 10, $values, 1);
					} else if ($column == 'RiderName') {
						$pdf->Cell(25, 10, $values, 1);
					}else if ($column == 'Rate') {
						$pdf->Cell(10, 10, $values, 1);
					} 
					else if ($column == 'Date') {
						$pdf->Cell(42, 10, $values, 1);
					} 
					else {
						$pdf->Cell(45, 10, $values, 1);
					}
				}
			}
		  $pdf->Output('D');
			// $pdf->Output();
			  
    }
	
}

//sms_template
if ($section == 'sms_template') {
    $ord = " ORDER BY vEmail_Code ASC";
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY vEmail_Code ASC";
      else
      $ord = " ORDER BY vEmail_Code DESC";
    }

    if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY vSubject_".$default_lang." ASC";
      else
      $ord = " ORDER BY vSubject_".$default_lang." DESC";
    }
    /* if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY vCountryCodeISO_3 ASC";
      else
      $ord = " ORDER BY vCountryCodeISO_3 DESC";
    } */

    /* if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY eStatus ASC";
      else
      $ord = " ORDER BY eStatus DESC";
    } */
    if ($keyword != '') {
        if ($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        } else {
            $ssql .= " AND vEmail_Code LIKE '%".$keyword."%' OR vSubject_".$default_lang." LIKE '%".$keyword."%'";
        }
    }
    $default_lang   = $generalobj->get_default_lang();
    $tbl_name       = 'send_message_templates';
    $sql = "SELECT vSubject_".$default_lang." as `SMS Title`,vEmail_Code as `SMS Code` FROM ".$tbl_name." WHERE eStatus = 'Active' $ssql $ord"; 
    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('SMS Title','SMS Code');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "SMS Templates");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'SMS Title') {
                $pdf->Cell(82, 10, $column_heading, 1);
            } else if ($column_heading == 'SMS Code') {
                $pdf->Cell(82, 10, $column_heading, 1);
            } else {
                $pdf->Cell(82, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'SMS Title') {
                    $pdf->Cell(82, 10, $key, 1);
                } else if ($column == 'SMS Code') {
                    $pdf->Cell(82, 10, $key, 1);
                } else {
                    $pdf->Cell(82, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}
//sms_template

// locationwise fare
if ($section == 'locationwise_fare') {
   $ord = ' ORDER BY ls.iLocatioId DESC';
    if($sortby == 1){
      if($order == 0)
      $ord = " ORDER BY lm1.vLocationName ASC";
      else
      $ord = " ORDER BY lm1.vLocationName DESC";
    }

     if($sortby == 2){
      if($order == 0)
      $ord = " ORDER BY lm2.vLocationName ASC";
      else
      $ord = " ORDER BY lm2.vLocationName DESC";
    }

    if($sortby == 3){
      if($order == 0)
      $ord = " ORDER BY ls.fFlatfare ASC";
      else
      $ord = " ORDER BY ls.fFlatfare DESC";
    } 

    if($sortby == 4){
      if($order == 0)
      $ord = " ORDER BY ls.eStatus ASC";
      else
      $ord = " ORDER BY ls.eStatus DESC";
    }
    if($sortby == 5){
      if($order == 0)
      $ord = " ORDER BY vt.vVehicleType ASC";
      else
      $ord = " ORDER BY vt.vVehicleType DESC";
    }

    if($keyword != ''){
        if($option != '') {
            if (strpos($option, 'eStatus') !== false) {
                $ssql.= " AND ".stripslashes($option)." LIKE '".stripslashes($keyword)."'";
            }else {
                $ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
            }
        }else {
            $ssql.= " AND lm1.vLocationName LIKE '%".$keyword."%' OR lm2.vLocationName LIKE '%".$keyword."%' OR ls.fFlatfare LIKE '%".$keyword."%' OR ls.eStatus LIKE '%".$keyword."%' OR vt.vVehicleType LIKE '%".$keyword."%'";
        }
    }

    if($option == "eStatus"){   
        $eStatussql = " AND ls.eStatus = '".ucfirst($keyword)."'";
    }else{
        $eStatussql = " AND ls.eStatus != 'Deleted'";
    }

    $sql = "SELECT lm2.vLocationName as `Source LocationName`,lm1.vLocationName as `Destination LocationName`,ls.fFlatfare as `Flat Fare`,vt.vVehicleType as `Vehicle Type`,ls.eStatus as `Status` FROM `location_wise_fare` ls left join location_master lm1 on ls.iToLocationId = lm1.iLocationId left join location_master lm2 on ls.iFromLocationId = lm2.iLocationId left join vehicle_type as vt on vt.iVehicleTypeId=ls.iVehicleTypeId  WHERE 1 = 1 $eStatussql $ssql $ord";

    // filename for download
    if ($type == 'XLS') {
        $filename = $section . "_" . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $flag = false;
        $result = $obj->ExecuteQuery($sql) or die('Query failed!');
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    } else {
        $heading = array('Source LocationName','Destination LocationName','Flat Fare','Vehicle Type','Status');
        $result = $obj->ExecuteQuery($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        $result = $resultset;
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetFillColor(36, 96, 84);
        
        $pdf->SetFont('Arial', 'b', 15);
        $pdf->Cell(100, 16, "Locationwise Fare");
        $pdf->Ln();
        $pdf->SetFont('Arial', 'b', 9);
        $pdf->Ln();
        foreach ($heading as $column_heading) {
            if ($column_heading == 'Source LocationName') {
                $pdf->Cell(65, 10, $column_heading, 1);
            } else if ($column_heading == 'Destination LocationName') {
                $pdf->Cell(65, 10, $column_heading, 1);
            } else if ($column_heading == 'Flat Fare') {
                $pdf->Cell(20, 10, $column_heading, 1);
            } else if ($column_heading == 'Status') {
                $pdf->Cell(20, 10, $column_heading, 1);
            } else {
                $pdf->Cell(30, 10, $column_heading, 1); 
            }
        }
        $pdf->SetFont('Arial', '', 9);
        foreach ($result as $row) {
            $pdf->Ln();
            foreach ($row as $column => $key) {
                if ($column == 'Source LocationName') {
                    $pdf->Cell(65, 10, $key, 1);
                } else if ($column == 'Destination LocationName') {
                    $pdf->Cell(65, 10, $key, 1);
                } else if ($column == 'Flat Fare') {
                    $pdf->Cell(20, 10, $key, 1);
                } else if ($column == 'Status') {
                    $pdf->Cell(20, 10, $key, 1);
                } else {
                    $pdf->Cell(30, 10, $key, 1);
                }
            }
        }
        $pdf->Output('D');
    }
}
// locationwise fare
?>