<?php
/** 
* 获取IP与位置信息
* 
* 用于配合QQ机器人检测窥屏插件
* 
* @author      lt_name<admin@lanink.cn>
*/ 
if(!defined('CORE_OK'))exit();
class getinfo{
	public $id=0;
	public $usip=null;
	public $uswz=null;
	public $sysinfo=null;
	public $txt=null;
	/**  
	* 设置记录文件ID
	* 
	* @param mixed $id 文件ID
	*/
	public function set_id($id=0){
		$this->id = $id;
	}
	/**  
	* 获取访问者IP
	* 
	*/
	public function get_ip(){
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
	/**  
	* 查询IP位置
	* 
	* @param mixed $ip 要查询的IP
	*/
	public function get_wz($ip=null){
		if($ip!=null){
			$this->usip = $ip;
		}
		$str=file_get_contents("http://m.ip138.com/ip.asp?ip={$this->usip}");
		$a=explode('本站主数据：', $str)[1];
		$b=explode('</p><p class="result">',$a)[0];
		$this->uswz=explode('<br/></p>',$b)[0];
	}
	/**  
	* 获取浏览器及设备信息（暂未使用）
	* 
	*/
	public function get_sysinfo(){
		
	}
	/**  
	* 获取IP与位置信息
	* 
	*/
	public function get_all(){
		self::get_ip();
		self::get_wz();
	}
	/**  
	* 设置文件保存路径
	* 
	* @param mixed $path 保存路径
	*/
	public function set_txt_path($path){
		$this->txt = $path.$this->id.".txt";
	}
	/**  
	* 保存信息
	* 
	*/
	public function save_txt(){
		if($this->txt!=null){
			if(!file_exists($this->txt)){
				$log = date("ymdhi").PHP_EOL."IP:".$this->usip.PHP_EOL."位置:".$this->uswz.PHP_EOL;
			}else{
				$txt_time = fopen($this->txt,"rb");
				$ctime=(int)fgets($txt_time,11);
				fclose($txt_time);
				if((date("ymdhi") - $ctime) > 3){ //超过三分钟重新记录
					unlink($this->txt);
					$log = date("ymdhi").PHP_EOL."IP:".$this->usip.PHP_EOL."位置:".$this->uswz.PHP_EOL;
				}else{
					$log = PHP_EOL."IP:".$this->usip.PHP_EOL."位置:".$this->uswz.PHP_EOL;
				}
			}
			$txt = fopen($this->txt,"ab");
			fwrite($txt,$log,strlen($log));
			fclose($txt);
			return true;
		}else{
			return '未设置文件路径';
		}
	}
	/**  
	* 查看信息
	* 
	*/
	public function see_txt(){
		if($this->txt!=null){
			if(file_exists($this->txt)){
				$txt = fopen($this->txt,"rb");
				$result = fread($txt, filesize($this->txt));
				fclose($txt);
				return $result;
			}else{
				return '文件不存在';
			}
		}else{
			return '未设置文件路径';
		}
	}
}

	