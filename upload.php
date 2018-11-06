<?php
//echo 123;exit;
include("UploadFile.class.php");
if (!empty($_POST['submit'])) {
 $upload = new UploadFile($_FILES['files'], 'data/upload/avatar');
 //上传用户文件，返回int值，为上传成功的文件个数。
 $num = $upload->upload();//返回上传成功的文件数目
 if ($num != 0) {
  echo "上传成功<br>";
  echo $num."个文件上传成功";
 }
 else {
  echo "上传失败<br>";
 }
}
?>