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
