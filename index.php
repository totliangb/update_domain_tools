<?php

include_once('replaceString.php');

$init = array(
	'', // 配置文件 默认为 update_domain.config.ini
	'sld_old.sql', // 需要替换的文件路径地址
	'./sld_new.sql', // 替换完成的文件地址
);

$obj = new ReplaceString($init);
$obj->start();