<?php  
include_once('../common.php');
$fileURL = $tconfig["tsite_upload_files_db_backup"];
$filePATH = $tconfig["tsite_upload_files_db_backup_path"];
$file_name = $_REQUEST['file'];
$file_url = $fileURL.$file_name;
$filesize = filesize($filePATH.$file_name);
header('Content-Description: File Transfer');
header('Content-Type: application/download');
header('Content-Disposition: attachment; filename='.basename($file_url));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . $filesize);
ob_clean();
flush();
readfile($file_url);
exit;
?>