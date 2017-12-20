CREATE DATABASE std57126db;

CREATE TABLE tb_user (
  Username varchar(20) NOT NULL,
  Password varchar(20) NOT NULL,
  Realname varchar(50) NOT NULL,
  Position varchar(20) NOT NULL,
  Filename varchar(25) NOT NULL,
  PRIMARY KEY (Username)
)
CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE tb_project (
  Project_id int NOT NULL,
  Projectname varchar(50) NOT NULL,
  Employer varchar(50) NOT NULL,
  Startdate varchar(10) NOT NULL,
  Enddate varchar(10) NOT NULL,
  Requirement text NOT NULL,
  Username varchar(20) NOT NULL,
  Realenddate varchar(10) NOT NULL,
  Result varchar(12) NOT NULL,
  PRIMARY KEY (Project_id)
)
CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE tb_fileupload (
  File_id int NOT NULL AUTO_INCREMENT,
  Filename varchar(256) NOT NULL,
  Date varchar(10) NOT NULL,
  Username varchar(20) NOT NULL,
  Project_id int NOT NULL,
  Activity_id int NOT NULL,
  PRIMARY KEY (File_id)
)
CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE tb_activity (
  Project_id int NOT NULL,
  Activity_id int NOT NULL,
  Activityname varchar(50) NOT NULL,
  Username varchar(20) NOT NULL,
  Startdate varchar(10) NOT NULL,
  Enddate varchar(10) NOT NULL,
  Status varchar(7) NOT NULL,
  Realenddate varchar(10) NOT NULL,
  Result varchar(12) NOT NULL
)
CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE tb_notification (
  Notification_id int NOT NULL AUTO_INCREMENT,
  Username varchar(20) NOT NULL,
  From_username varchar(20) NOT NULL,
  Message varchar(500) NOT NULL,
  Project_id int NOT NULL,
  Date varchar(10) NOT NULL,
  Time varchar(5) NOT NULL,
  Status varchar(6) NOT NULL,
  PRIMARY KEY (Notification_id)
)
CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE tb_conversation (
  Message_id bigint NOT NULL AUTO_INCREMENT,
  From_user varchar(20) NOT NULL,
  To_user varchar(20) NOT NULL,
  Message text NOT NULL,
  Date varchar(10) NOT NULL,
  Time varchar(5) NOT NULL,
  Status varchar(6) NOT NULL,
  PRIMARY KEY (Message_id)
)
CHARACTER SET utf8 COLLATE utf8_unicode_ci;

INSERT INTO tb_user VALUES('admin', 'admin', 'admin', 'Administrator', 'admin.png');
INSERT INTO tb_user VALUES('manager', 'manager', 'manager', 'Manager', 'manager.png');
INSERT INTO tb_user VALUES('manager1', 'manager1', 'manager1', 'Manager', 'manager1.png');
INSERT INTO tb_user VALUES('manager2', 'manager2', 'manager2', 'Manager', 'manager2.png');
INSERT INTO tb_user VALUES('staff', 'staff', 'staff', 'Staff', 'staff.png');
INSERT INTO tb_user VALUES('staff1', 'staff1', 'staff1', 'Staff', 'staff1.png');
INSERT INTO tb_user VALUES('staff2', 'staff2', 'staff2', 'Staff', 'staff2.png');
INSERT INTO tb_user VALUES('staff3', 'staff3', 'staff3', 'Staff', 'staff3.png');
INSERT INTO tb_user VALUES('staff4', 'staff4', 'staff4', 'Staff', 'staff4.png');
INSERT INTO tb_user VALUES('staff5', 'staff5', 'staff5', 'Staff', 'staff5.png');
