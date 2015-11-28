--
-- lamp39数据库
--
CREATE DATABASE IF NOT EXISTS lamp39;

CREATE TABLE goods(
	id INT(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(32) NOT NULL DEFAULT '',
	type VARCHAR(32) NOT NULL DEFAULT '',
	price DOUBLE(6,2) NOT NULL DEFAULT 0,  -- 两位小数。
	num INT,
	pic VARCHAR(32) NOT NULL DEFAULT '',
	addtime DATETIME NOT NULL
)ENGINE=MYISAM DEFAULT CHARSET=utf8 auto_increment=1;