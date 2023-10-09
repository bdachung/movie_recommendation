create database recommendation;

use recommendation;

SET time_zone = '+07:00';

create table items (
	movie_id int,
    movie_title varchar(255),
    release_date TIMESTAMP
);

create table users(
	user_id int,
    age int,
    sex char(1),
    occupation varchar(100),
    user varchar(100),
    pwd varchar(100)
);

create table ratings(
	user_id int,
    movie_id int,
    rating int
);

create table item_type(
	movie_id int,
    movie_type varchar(100)
);