<?php

/**
 * 替换域名
 */
class ReplaceString
{
	private $config;
	private $data_from_file;
	private $data_to_file;
	private $old_url = '';
	private $new_url = '';
	private $encode_one_old_url = '';
	private $encode_one_new_url = '';
	private $encode_two_old_url = '';
	private $encode_two_new_url = '';
	private $line_count = 0;
	private $http_base = 'http://';

	public function __construct($init=array())
	{
		$this->init($init);
	}

	// 提示报错
	public function error($msg)
	{
		echo "//------------------------------------------------------------------//\n";
		echo "ERROR:".$msg."\n";
		echo "//------------------------------------------------------------------//\n";
		exit;
	}

	// 初始化 验证配置文件 及 各项需要的内容 是否齐全
	public function init($init)
	{
		$config_path = '';
		$data_from_file = '';
		$data_to_file = '';

		if (!empty($init)) {
			list($config_path,$data_from_file,$data_to_file) = $init;
		}

		try {
			$this->data_from_file= $data_from_file;
			$this->data_to_file= $data_to_file;
			$this->config = $this->getConfigData($config_path);
			// 验证需要需要替换的文件是否存在
			if (!file_exists($data_from_file)) {
				throw new Exception("未找到需要替换的文件");
			}else{
				$this->old_url = $this->http_base.$this->config['old_domain_name'];
				$this->new_url = $this->http_base.$this->config['new_domain_name'];

				// http:// 编码一次
				$this->encode_one_old_url = urlencode($this->http_base).$this->config['old_domain_name'];
				$this->encode_one_new_url = urlencode($this->http_base).$this->config['new_domain_name'];

				// http:// 编码两次次
				$this->encode_two_old_url = urlencode(urlencode($this->http_base)).$this->config['old_domain_name'];
				$this->encode_two_new_url = urlencode(urlencode($this->http_base)).$this->config['new_domain_name'];
			}
		} catch (Exception $e) {
			$this->error($e->getMessage());
		}
	}

	public function getConfigData($config_path='')
	{
		$config = array();
		$config_path = $config_path ? : 'update_domain.config.ini';

		if (file_exists($config_path)) {
			// 获取配置文件
			include_once($config_path);
		}else{
			throw new Exception("配置文件未找到");
		}

		return $config;
	}

	// 获取需要替换的文件数据
	public function getFileData($file)
	{
		$f = fopen($file,'r');
		try {
			while($line = fgets($f))
			{
				yield $line;
			}
		} finally {
			fclose($f);
		}
	}

	// 获取需要替换的文件总行数
	public function getFileCount($file)
	{
		$f = fopen($file,'r');
		try {
			while($line = fgets($f))
			{
				$this->line_count++;
			}
		} finally {
			fclose($f);
		}
	}

	// 写入新文件
	public function writeToNewFile($line_str)
	{
		if ($line_str) {
			file_put_contents($this->data_to_file,$line_str,FILE_APPEND);
		}
	}

	// 写入替换后的字符串至新文件中
	public function replaceDataToNewFile($line_str){

		$http_header = 'http:\/\/';
		$preg_url = $http_header.$this->config['old_domain_name'];
		// 更新 序列化 数据中的相关域名
		$rule = '/s:(\d+):(\W+)"'.$preg_url.'/';
		if (preg_match_all($rule,$line_str,$matches)) {
			if (!empty($matches[1])) {
				foreach ($matches[1] as $key => $value) {
					$num = strlen($this->new_url) - strlen($this->old_url);
					$need_replace[$key]['s'] = $value + $num;
					$need_replace[$key]['h'] = $this->new_url;
				}
			}
			if (!empty($matches[2])) {
				foreach ($matches[2] as $key => $value) {
					$need_replace[$key]['b'] = $value;
				}
			}
			if (!empty($matches[0]) && !empty($need_replace)) {
				foreach ($matches[0] as $key => $value) {
					$need_replace_str_item = 's:'.$need_replace[$key]['s'].':'.$need_replace[$key]['b'].'"'.$need_replace[$key]['h'];
					$line_str = str_replace($value, $need_replace_str_item, $line_str);
				}
			}
		}else{
			// 非序列化的字符串替换
			$line_str = str_replace($this->old_url, $this->new_url, $line_str);
			$line_str = str_replace($this->encode_one_old_url, $this->encode_one_new_url, $line_str);
			$line_str = str_replace($this->encode_two_old_url, $this->encode_two_new_url, $line_str);
		}

		// 写入新文件
		$this->writeToNewFile($line_str);
	}

	// 开始执行替换
	public function start()
	{
		$this->getFileCount($this->data_from_file);
		$file_data = $this->getFileData($this->data_from_file);

		echo "开始替换\n";
		foreach ($file_data as $key => $line) {
			// usleep(1000);

			$this->replaceDataToNewFile($line);

			// 进度条
			$i = $key + 1;
			$i = ($i / $this->line_count) * 100;
			printf("\r [%-100s] (%2d%%/%2d%%)", str_repeat("=", $i) . ">", $i, 100);
		}
		echo "\n替换完成！\n";
	}

}