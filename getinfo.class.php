<?php
if(!defined('CORE_OK'))exit();
class getinfo{
	public $id=null;
	public $usip=null;
	public $uswz=null;
	public $sysinfo=null;
	public $txtpath=null;
	public $isnew=false;
	
	function setid($info){
		$this->id = $info;
	}
	
	function getip(){
		if (isset($_SERVER)){
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
				$this->usip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
				$this->usip = $_SERVER["HTTP_CLIENT_IP"];
			} else {
				$this->usip = $_SERVER["REMOTE_ADDR"];
			}
		} else {
			if (getenv("HTTP_X_FORWARDED_FOR")){
				$this->usip = getenv("HTTP_X_FORWARDED_FOR");
			} else if (getenv("HTTP_CLIENT_IP")) {
				$this->usip = getenv("HTTP_CLIENT_IP");
			} else {
				$this->usip = getenv("REMOTE_ADDR");
			}
		}
	}
	
	function getwz(){
		$str=file_get_contents("http://m.ip138.com/ip.asp?ip={$this->usip}");
		$a=explode('本站主数据：', $str)[1];
		$b=explode('</p><p class="result">',$a)[0];
		$this->uswz=explode('<br/></p>',$b)[0];
	}
	
	function setsysinfo($info){
		$this->sysinfo = $info;
	}
	
	function get(){
		getinfo::getip();
		getinfo::getwz();
	}
	
	function settxtpath($path,$see=false){
		$txt = $path.$this->id.".txt";
		if($see==true){
			$this->txtpath = fopen($txt,"rb");
		}else{
			if(!file_exists($txt)){
				$this->isnew = true;
			}else{
				$txt_info = fopen($txt,"rb");
				$ctime=(int)fgets($txt_info,9);
				fclose($txt_info);
				if((date("mdhi") - $ctime) > 3){
					unlink($txt);
					$this->isnew = true;
				}
			}
			$this->txtpath = fopen($txt,"ab");
		}
	}
	
	function save(){
		if($this->txtpath!=null){
			if($this->isnew){
				$log = date("mdhis")."\r\nIP:".$this->usip."\r\n位置:".$this->uswz."\r\n";
			}else{
				$log = "\r\nIP:".$this->usip."\r\n位置:".$this->uswz."\r\n";
			}
			fwrite($this->txtpath,$log,strlen($log));
			fclose($this->txtpath);
		}
	}
	
	function seeinfo(){
		if($this->txtpath!=null){
			while(! feof($this->txtpath))  {
				$result .= fgets($this->txtpath)."\n";
			}
			return $result;
		}else{
			return '文件不存在';
		}
	}
}

	