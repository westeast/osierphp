<?php 
/**
 * 文件操作有关的常用的函数
 */
 /**
  * Return an array of file names and folders in directory;
  */
 function read_folder( $dir = '/etc'){
	 $listDir = array();
	 if($handler = opendir($dir)){
		 while(($sub = readdir($handler)) != false ){
			 if($sub != "." && $sub != ".."){
				 if(is_file($dir."/".$sub)){
					 $listDir[] = $sub;
				 }elseif(is_dir($dir.'/'.$sub)){
					 $listDir[$sub] = read_folder($dir.'/'.$sub);
				 }
			 }
		 }
		 closedir($handler);
	 }
	 return $listDir;
 }
 
 
/**
 * 
 * @param unknown_type $degubinfo
 * @param String $basedir 项目所在的目录 
 * @param unknown_type $clear
 * @throws Exception
 * @todo 1,$basedir 
 */
 function dong($debuginfo,$basedir='',$clear=false){
	 $mod = $clear ? 'wb+':'a+';
	 $path = dirname(__FILE__);
	 if($basedir != ''){
	 	$expr = '/'.$basedir.'/';
	 	if(preg_match($expr, $path)){
	 		$arr = explode(DIRECTORY_SEPARATOR.$basedir.DIRECTORY_SEPARATOR,$path);
	 		$path = $arr[0].DIRECTORY_SEPARATOR.$basedir;
	 	}
	 }
	 $fp = fopen($path.'/'.$basedir.'_debug.htm',$mod);

	 $print_info = '<pre>'.print_r($debuginfo,true).'</pre>';
	 $header_info = $clear?"<meta http-equiv='Content-Type' content='text/html' charset='utf-i' />\r\n":'';
	 $help_info = "<br />\r\n<span style='color:grey;'>链接地址:http://".$_SERVER['HPPT_HOST'].$_SERVER['REQUEST_URI']
		 ."\r\n<br />\r\n时间:".date('Y-m-d H:i:s',time())
		 ."</span>\r\n<br /n>\r\n";

	 if(@fwrite($fp,$header_info.$help_info.$print_info) === false){
		 $error = '你没有权限了';
		 throw new Exception($error);
	 }
 }