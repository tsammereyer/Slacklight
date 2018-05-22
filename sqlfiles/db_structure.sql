DROP DATABASE fh_2018_scm4_S1610307035;
CREATE DATABASE fh_2018_scm4_S1610307035  CHARACTER SET utf8 COLLATE utf8_general_ci;

USE fh_2018_scm4_S1610307035;


CREATE TABLE users (
	id INT(11) NOT NULL AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	password VARCHAR(255) NOT NULL, 
	PRIMARY KEY (id),
	UNIQUE KEY (name) 
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;

CREATE TABLE channel (
	id INT(11) NOT NULL AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;

CREATE TABLE channel_user_reference (
	id INT(11) NOT NULL AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	channel_id INT(11) NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY (user_id, channel_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8; 
ALTER TABLE channel_user_reference
ADD CONSTRAINT channel_user_reference_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE channel_user_reference
ADD CONSTRAINT channel_user_reference_channel_id FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE topic (
	id INT(11) NOT NULL AUTO_INCREMENT,
	channel_id INT(11) NOT NULL,
	name VARCHAR(1000) NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;

ALTER TABLE topic
ADD CONSTRAINT topic_belongs_to_channel FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE message (
	id INT(11) NOT NULL AUTO_INCREMENT,
	user_id INT(11) NOT NULL,
	channel_id INT(11) NOT NULL,
	content VARCHAR(1000) NOT NULL, 
	created DATETIME DEFAULT CURRENT_TIMESTAMP,
	deleted TINYINT(1) NOT NULL DEFAULT FALSE,
	seen TINYINT(1) NOT NULL DEFAULT FALSE,
	favourite TINYINT(1) NOT NULL DEFAULT FALSE,
	PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 CHARSET=utf8;

ALTER TABLE topic
ADD CONSTRAINT message_belongs_to_channel FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE ON UPDATE CASCADE;


INSERT INTO users (name, password) 
VALUES ('hans', '0ba7335aa392b86ba4888087f0b308b1f8024f0e');

INSERT INTO users (name, password) 
VALUES ('franz', 'b54078ec72181fc043d010d2dc72269da4f600a1');

INSERT INTO channel (name) 
VALUES ('general');

INSERT INTO channel (name) 
VALUES ('random');

INSERT INTO topic (channel_id, name) 
VALUES (1, "Company-wide announcements and work-based matters");

INSERT INTO topic (channel_id, name) 
VALUES (2, "Non-work banter and water cooler conversation");

INSERT INTO message (user_id, channel_id, content, created, seen, favourite)
VALUES (1, 1, "heyho, whats up?", '2018-05-06 15:01:35', 1, 0);

INSERT INTO message (user_id, channel_id, content, created,  seen, favourite) 
VALUES (2, 1, "hey hans, i am fine how are you?", '2018-05-06 16:41:23', 1, 1);

INSERT INTO message (user_id, channel_id, content, created, seen, favourite) 
VALUES (1, 2, "random", '2018-05-06 14:15:35', 1, 0);

INSERT INTO channel_user_reference (user_id, channel_id) 
VALUES (1, 1);

INSERT INTO channel_user_reference (user_id, channel_id) 
VALUES (1, 2);

INSERT INTO channel_user_reference (user_id, channel_id) 
VALUES (2, 1);