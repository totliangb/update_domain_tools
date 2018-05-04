<?php

	include_once('update_domain.config.ini');

	// $domain_name = $config['old_domain_name'];
	// $new_domain_name = $config['new_domain_name'];

	$file = 'sld_old.sql';
	$new_file = 'sld_new.sql';


	$http_header = 'https:\/\/';
	$preg_url = $http_header.$domain_name;

	$old_url = 'https://'.$domain_name;

	$new_url = 'https://'.$new_domain_name;

	$run_flag = false;

	if (file_exists($file)) {
		$run_flag = true;
	}

	$a = '';
	
	if ($run_flag) {
		$a = file_get_contents($file);
	}
//  多域名
	$need_replace_domain = array(
		'admin.phi-go.com',
		// 'api.phi-go.com',
		// 'v.phi-go.com',
		// 'tadmin.phi-go.com',
		// 'tapi.phi-go.com',
		// 'tv.phi-go.com'
	);
function run_replace($a,$preg_url,$old_url){

	$new_replace_url = 'https://static.phi-go.com';
	// 更新 序列化 数据中的相关域名
	$rule = '/s:(\d+):(\W+)"'.$preg_url.'\/data'.'/';
	if (preg_match_all($rule,$a,$matches)) {
		if (!empty($matches[1])) {
			foreach ($matches[1] as $key => $value) {
				$num = strlen($new_replace_url) - strlen($old_url);
				$need_replace[$key]['s'] = $value + $num;
				$need_replace[$key]['h'] = $new_replace_url;
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
	}
	// 更新 相关 域名地址
	$a = str_replace($old_url.'/data', $new_replace_url.'/data', $a);
	return $a;
}

	if ($a) {
		
		foreach ($need_replace_domain as $key => $value) {
			$old_url = 'https://'.$value;
			$a = run_replace($a,$value,$old_url);
		}

		// // 更新 序列化 数据中的相关域名
		// $rule = '/s:(\d+):(\W+)"'.$preg_url.'/';
		// if (preg_match_all($rule,$a,$matches)) {
		// 	if (!empty($matches[1])) {
		// 		foreach ($matches[1] as $key => $value) {
		// 			$num = strlen($new_url) - strlen($old_url);
		// 			$need_replace[$key]['s'] = $value + $num;
		// 			$need_replace[$key]['h'] = $new_url;
		// 		}
		// 	}
		// 	if (!empty($matches[2])) {
		// 		foreach ($matches[2] as $key => $value) {
		// 			$need_replace[$key]['b'] = $value;
		// 		}
		// 	}
		// 	if (!empty($matches[0]) && !empty($need_replace)) {
		// 		foreach ($matches[0] as $key => $value) {
		// 			$need_replace_str_item = 's:'.$need_replace[$key]['s'].':'.$need_replace[$key]['b'].'"'.$need_replace[$key]['h'];
		// 			$a = str_replace($value, $need_replace_str_item, $a);
		// 		}
		// 	}
		// 	file_put_contents($new_file,$a);
		// 	echo "成功";
		// }else{
		// 	echo "未找到。";
		// }

		// 更新 全部 域名
		// $a = str_replace($old_url, $new_url, $a);
		file_put_contents($new_file,$a);
	}else{
		echo "数据库文件未找到。"."\n\n";
	}
