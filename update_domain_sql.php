<?php

	include_once('update_domain.config.ini');

	$domain_name = $config['old_domain_name'];
	$new_domain_name = $config['new_domain_name'];

	$file = 'sld_old.sql';
	$new_file = 'sld_new.sql';


	$http_header = 'http:\/\/';
	$preg_url = $http_header.$domain_name;

	$old_url = 'http://'.$domain_name;

	$new_url = 'http://'.$new_domain_name;

	$run_flag = false;

	if (file_exists($file)) {
		$run_flag = true;
	}

	$a = '';
	
	if ($run_flag) {
		$a = file_get_contents($file);
	}

	if ($a) {
		
		// 更新 序列化 数据中的相关域名
		$rule = '/s:(\d+):(\W+)"'.$preg_url.'/';
		if (preg_match_all($rule,$a,$matches)) {
			if (!empty($matches[1])) {
				foreach ($matches[1] as $key => $value) {
					$num = strlen($new_url) - strlen($old_url);
					$need_replace[$key]['s'] = $value + $num;
					$need_replace[$key]['h'] = $new_url;
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
					$a = str_replace($value, $need_replace_str_item, $a);
				}
			}
			file_put_contents($new_file,$a);
			echo "成功";
		}else{
			echo "未找到。";
		}

		// 更新 全部 域名
		$a = str_replace($old_url, $new_url, $a);
		file_put_contents($new_file,$a);
	}else{
		echo "数据库文件未找到。"."\n\n";
	}
