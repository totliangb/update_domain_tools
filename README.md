替换域名

1. 将需要替换的 数据库文件 改名 为 sld_old.sql 放在 当前目录下；
2. 在 update_domain.config.ini 中填写 相关域名
3. 运行 index.php   (命令行 移动到 当前目录下  运行  php -f index.php)
4. 当前目录 会生成 sld_new.sql ，为 替换域名地址 后的 sql 文件。
5. sld_old.sql 、sld_new.sql、update_domain.config.ini 文件均可自定义名称。