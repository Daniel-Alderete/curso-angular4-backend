CREATE DATABASE IF NOT EXISTS curso_angular4;
USE curso_angular4;

CREATE TABLE products(
id 		int(255) 	auto_increment not null,
name 		varchar(255),
description 	text,
prize		varchar(255),
image		varchar(255),
CONSTRAINT pk_products PRIMARY KEY(id)
)ENGINE=InnoDb;
