<?php

/**
 * 下载html静态html
 * @author Administrator
 * todo 1,日志记录功能下载失败的放在一个log文件中，而且是不断追加。
 * 2,像cisco这种相对链接的网站下载还有些问题
 *
 */
const  	DS = '/';
class downloadhtml{

	//要下载的静态页的地址
	public $index_url ;
	//此页面上的图片或者css的相对基地址
	public $base_url ;
	private $base_dir ;
	
	private $js_dir = 'js';
	private $css_dir = 'css';
	private $image_dir = 'image';

	/**
	 * 
	 * @param string $index_url，一般为一个绝对链接后加一个index.php
	 * @param string $base_dir  存放自己东西的根目录
	 * @param string $base_url  带不带斜杠要看页面中的css或者js的相对链接情况，一般情况下带的
	 */
	function __construct($index_url,$base_dir='html',$base_url = ''){
		//初始化
		set_time_limit(0);
		$this->index_url = $index_url;
		$this->base_dir =  $base_dir;
		if(empty($base_url)){
			$this->base_url = dirname($index_url).DS;
		}else{
			$this->base_url = $base_url;
		}
		is_dir($this->base_dir) or mkdir($this->base_dir);
		
		$html = file_get_contents($this->index_url);
		//$html = preg_replace_callback('#<!--((.|\r\n)*?)-->#', '', $html);//去掉注释，且只去多行的注释，因多选的一般为垃圾信息了
		
		//下载所有js image css文件
		$this->handle_html($html,'js');
		$this->handle_html($html,'image');
		$this->handle_html($html,'css');
		
		file_put_contents($this->base_dir.DS.'index.html', $html);
	}
	
	/**
	 * @param html
	 * @param pattern
	 * @param name
	 */
	function handle_html(&$html, $type) {
		$filetype = array();
		switch ($type){
			case 'js':
				$pattern = '#<script(.*?)src="(.*?)"#';
				$filetype = array('js');
				break;
			case 'css':
				$pattern = '#<link(.*?)href="(.*?)"#';
				$filetype = array('css');
				break;
			case 'image':
				$pattern = '#<img(.*?)src="(.*?)"#';//正常的图片正则
				//$pattern = '#<a(.*?)href="(.*?)"#';
				$filetype = array('gif','png','jpg','jpeg');
				break;
		}
		
		$basedir = $this->base_dir.DS.$type.DS;
		is_dir($basedir) or mkdir($basedir);
		
		preg_match_all($pattern, $html, $matches);
		//print_r($matches);
		
		$newjspath = array();
		if(is_array($matches[2])){
			foreach($matches[2] as $value){
				$strname = str_replace('?', '', end(explode('/', $value)));
				$name = in_array(end(explode('.',$strname)),$filetype)? $strname:$strname.'.'.$type;
				if(!preg_match('#http://#', $value)){
					if(preg_match('#^/(.*)#', $value)){
						$arr = parse_url($this->base_url);
						$host = str_replace($arr['path'], '', $this->base_url);
						$value = $host.$value;
					}else{
						$value = $this->base_url.$value;
					}
				}
				$data = file_get_contents($value);
				switch ($type){
					case 'js':
						//对js文件的修改undo
						break;
					case 'css':
						$data = $this->handle_css ($basedir, $data,$value);
						break;
					case 'image':
						//image貌似不用处理undo
						break;
				}
				
				file_put_contents($basedir.$name,$data);
				$newjspath[] = $type.DS.$name;
			}
		}
		$html = str_replace($matches[2], $newjspath, $html);
	}
	
	/**
	 * @param basedir
	 * @param data
	 */
	function handle_css($basedir, $data,$value) {
		//@import url(base.css)，url(../images/member/ncus_public.png)的下载处理
		//这里做一个合理假设包含的base.css文件中不再包含其它css文件
		preg_match_all('#url\((.*?)\)#', $data, $match);echo $value;print_r($match);
		$newCssorimgPath = array();
		if(is_array($match[1])){
			foreach($match[1] as $v){
				$cssorimg = explode('/', $v);
				$cssorimgname = is_array($cssorimg) ? end($cssorimg):$cssorimg;
	
				if(end(explode('.',$v)) == 'css'){//下载css文件(在一个css中import的另一个css)
					$cssUrl = dirname($value).DS.$cssorimgname;
					$subdata = file_get_contents($cssUrl);
					$newCssorimgPath[] = $cssorimgname;
					$subdata = $this->handle_css($basedir, $subdata, $cssUrl);
					file_put_contents($basedir.$cssorimgname,$subdata);
				}else{//一个css里以url()包含的文件除了css就只有图片了，这里处理图片
					$depth = preg_match_all('#\.\./#', $v, $temp);
					$img = substr($v, 3*$depth-1);
					
					$tempvalue = $value;
					do{
						$tempvalue = dirname($tempvalue);
					}while($depth-- > 0);
					$imgUrl = $tempvalue.$img;
					$subdata = file_get_contents($imgUrl);
					$newCssorimgPath[] = '../image/'.$cssorimgname;
					file_put_contents($this->base_dir.'/image/'.$cssorimgname,$subdata);
				}
			}
		}
		$data = str_replace($match[1], $newCssorimgPath, $data);
		return $data;
	}
}
new downloadhtml('http://www.cisco.com/web/CN/index.html','cisco','http://www.cisco.com');

//new downloadhtml('http://www.expervision.com/index.php','vision');
//new downloadhtml('http://www.expervision.com/wp-content/themes/expervisiontheme/css/bright-blue/img/','image','http://www.expervision.com/wp-content/themes/expervisiontheme/css/bright-blue/img/');