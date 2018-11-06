<?php
class UploadFile {
 
 protected  $user_post_file = array();  //用户上传的文件
 protected $save_file_path;    //存放用户上传文件的路径
 protected $max_file_size;     //文件最大尺寸
 protected $last_error;     //记录最后一次出错信息
 //默认允许用户上传的文件类型
 protected $allow_type = array('gif', 'jpg', 'png', 'zip', 'rar', 'txt', 'doc', 'pdf','docx');
 protected $final_file_path;  //最终保存的文件名
 protected $save_info = array(); //返回一组有用信息，用于提示用户。
 
 /**
  * 构造函数
  */
 function __construct($file, $path, $size = 2097152, $type = '') {
	$this->user_post_file = $file;
	if(!is_dir($path)){ //存储路径文件不存在就创建
		mkdir($path);
		chmod($path,0777);
	}
	$this->save_file_path = $path;
	$this->max_file_size = $size;  //如果用户不填写文件大小，则默认为2M.
	if ($type != '')
		$this->allow_type = $type;
}
 
 /**
  * 存储用户上传文件，检验合法性通过后，存储至指定位置。
  */
 function upload() {
  
  for ($i = 0; $i < count($this->user_post_file['name']); $i++) {
	  //var_dump($this->user_post_file['name']);exit;
   //如果当前文件上传功能，则执行下一步。
     if ($this->user_post_file['error'][$i] == 0) {
      //取当前文件名、临时文件名、大小、扩展名，后面将用到。
      $name = $this->user_post_file['name'][$i];
      $tmpname = $this->user_post_file['tmp_name'][$i];
      $size = $this->user_post_file['size'][$i];
      $mime_type = $this->user_post_file['type'][$i];
      $type = $this->getFileExt($this->user_post_file['name'][$i]);
      //检测当前上传文件大小是否合法。
      if (!$this->checkSize($size)) {
       $this->last_error = "The file size is too big. File name is: ".$name;
       $this->halt($this->last_error);
       continue;
      }
      //检测当前上传文件扩展名是否合法。
      if (!$this->checkType($type)) {
       $this->last_error = "Unallowable file type: .".$type." File name is: ".$name;
       $this->halt($this->last_error);
       continue;
      }
      //检测当前上传文件是否非法提交。
      if(!is_uploaded_file($tmpname)) {
       $this->last_error = "Invalid post file method. File name is: ".$name;
       $this->halt($this->last_error);
       continue;
      }
      //移动文件后，重命名文件用。
      //$basename = $this->getBaseName($name, ".".$type);
      //为防止文件名乱码
      //$basename = iconv("UTF-8","gb2312", $basename);
      //移动后的文件名
      $saveas = rand(1,99999).time().".".$type;
      //组合新文件名再存到指定目录下，格式：存储路径 + 文件名 + 时间 + 扩展名
      $this->final_file_path = $this->save_file_path."/".$saveas;
      if(!move_uploaded_file($tmpname, $this->final_file_path)) {
       $this->last_error = $this->user_post_file['error'][$i];
       $this->halt($this->last_error);
       continue;
      }
      //存储当前文件的有关信息，以便其它程序调用。
      $this->save_info[] =  array(
                "name" => $name, "type" => $type,
                "mime_type" => $mime_type,
                "size" => $size, 
                "saveas" => $saveas,
                "path" => $this->final_file_path
                );
     }
  }
  return count($this->save_info); //返回上传成功的文件数目
 }
 
 /**
  * 返回一些有用的信息，以便用于其它地方。
  */
 function getSaveInfo() {
  return $this->save_info;
 }
 
 /**
  * 检测用户提交文件大小是否合法
  */
 function checkSize($size) {
  if ($size > $this->max_file_size) {
   return false;
  }
  else {
   return true;
  }
 }
 
 /**
  * 检测用户提交文件类型是否合法
  */
 function checkType($extension) {
  foreach ($this->allow_type as $type) {
   if (strcasecmp($extension , $type) == 0)
    return true;
  }
  return false;
 }
 
 /**
  * 显示出错信息
  */
 function halt($msg) {
  printf("<b><UploadFile Error:></b> %s <br>\n", $msg);
 }
 
 /**
  * 取文件扩展名
  */
 function getFileExt($filename) {
  $stuff = pathinfo($filename);
  return $stuff['extension'];
 }
}
?>