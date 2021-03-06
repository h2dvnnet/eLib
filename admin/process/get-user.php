<?php
require_once('../../assets/config.php');
if ($_SESSION["admin"] != 1) die();
$records = array(); // Tạo mảng
function is_number($s)
{
  if(preg_match('/^([0-9]+)$/',$s) == '1') {return true;} else {return false;}
}
function class_check($s) {
	if(preg_match('/^([0-9]{2,2})+([a-dA-d]{1,1})+([1-5]{1,1})$/',$s) == 1) {return true;} else {return false;}
}
if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
  switch ($_REQUEST["customActionValue"]) {
  case '1':  
    $sqlaction="DELETE FROM `user` WHERE `id` IN (";
    $i=1;
    foreach ($_REQUEST[id] as $id)
    {
      if ($i == 1) { $sqlaction.="'".$id."'"; }
      else { $sqlaction.=",'".$id."'"; }
      $i=2;
    }
    $sqlaction.=")";
    $queryaction=@mysql_query($sqlaction);
    $records["customActionStatus"] = "OK";
    $numrow = @mysql_affected_rows();
    $records["customActionMessage"] = "Xóa ".$numrow." tài khoản thành công";
    break;
  }
}
$sql="SELECT * FROM `user` WHERE 1";
	if ($_REQUEST['user_id'] != NULL)
	{
		$sql.=" AND `id` LIKE '%".$_REQUEST['user_id']."%'";
	}
	if ($_REQUEST['user_code'] != NULL)
	{
		$sql.=" AND `scode` LIKE '%".$_REQUEST['user_code']."%'";
	}  
	if ($_REQUEST['user_name'] != NULL)
	{
		$sql.=" AND `name` LIKE '%".$_REQUEST['user_name']."%'";
	}
	if (($_REQUEST['user_class'] != NULL) && (class_check($_REQUEST['user_class'])))
	{
		$sql.=" AND `class` LIKE '%".$_REQUEST['user_class']."%'";
	}
	if ($_REQUEST['user_borrowing'] != NULL)
	{
		$sql.=" AND `borrowing` LIKE '%".$_REQUEST['user_borrowing']."%'";
	}
	if ($_REQUEST['user_verify'] != '0' && $_REQUEST['user_verify'] != NULL)
	{
		if ($_REQUEST['book_verify'] == '1')
		{
		$sql.=" AND `verify` ='1'";
		}
		else
		{
		$sql.=" AND `verify` ='0'";
		}
	}
	switch ($_REQUEST['order']['0']['column']) {
	case '1': $sql.=" ORDER BY `id` ".$_REQUEST['order']['0']['dir']; break;
	case '2': $sql.=" ORDER BY `scode` ".$_REQUEST['order']['0']['dir']; break;
	case '3': $sql.=" ORDER BY `name` ".$_REQUEST['order']['0']['dir']; break;
	case '4': $sql.=" ORDER BY `class` ".$_REQUEST['order']['0']['dir']; break;
	case '5': $sql.=" ORDER BY `borrowing` ".$_REQUEST['order']['0']['dir']; break;
	case '6': $sql.=" ORDER BY `verify` ".$_REQUEST['order']['0']['dir']; break;
	default: $sql.=" ORDER BY `id` ".$_REQUEST['order']['0']['dir']; break;
	}
$query=@mysql_query($sql);
$iTotalRecords = @mysql_num_rows($query); // Tổng số sách lấy được
$iDisplayLength = intval($_REQUEST['length']); // Số sách trên 1 trang
$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; // Kiểm tra
$iDisplayStart = intval($_REQUEST['start']); // Bắt đầu lấy
$sEcho = intval($_REQUEST['draw']); // Số lần get (Đéo cần quan tâm)
$records["data"] = array(); 
$end = $iDisplayStart + $iDisplayLength; // Kết thúc lấy
$end = $end > $iTotalRecords ? $iTotalRecords : $end; // Kiểm tra
//$records["sql"]=$sql; 
$query2=@mysql_query($sql." LIMIT ".$iDisplayStart.",".$end);
while ($row2=@mysql_fetch_assoc($query2))
{
  if ($row2['verify'] != '1')
  {
    $verify='<span class="label label-sm label-danger">Chưa xác nhận</span>';
  }
  else
  {
    $verify='<span class="label label-sm label-success">Đã xác nhận</span>';
  }
    $records["data"][] = array(
    '<input type="checkbox" name="id[]" value="'.$row2['id'].'">',
    $row2['id'],
    $row2['scode'],
    $row2['name'],
    $row2['class'],
    $row2['borrowing'],
    $verify,
    '<a href="user-edit.php?id='.$row2['id'].'" class="btn btn-xs default btn-editable"><i class="fa fa-pencil"></i> Sửa</a>',
  );
}
$records["draw"] = $sEcho;
$records["recordsTotal"] = $iTotalRecords;
$records["recordsFiltered"] = $iTotalRecords;
die (json_encode($records));
?>