<?php
/**
 * �ļ������йصĳ��õĺ���
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


 function dong($degubinfo,$basedir='www',$clear=false){
	 $mod = $clear ? 'wb+':'a+';
	 $basedir = dirname(__FILE__);
	 echo $basedir;
	 $fp = fopen($basedir.'/'.'debug.htm',$mod);

	 $print_info = '<pre>'.print_r($debuginfo,true).'</pre>';
	 $header_info = $clear?"<meta http-equiv='Content-Type' content='text/html' charset='utf-i' />\r\n":'';
	 $help_info = "<br />\r\n<span style='color:grey;'>���ӵ�ַ:http://".$_SERVER['HPPT_HOST'].$_SERVER['REQUEST_URI']
		 ."\r\n<br />\r\nʱ��:".date('Y-m-d H:i:s',time())
		 ."</span>\r\n<br /n>\r\n";

	 if(@fwrite($fp,$header_info.$help_info.$print_info) === false){
		 $error = '��û��Ȩ����';
		 throw new Exception($error);
	 }
 }