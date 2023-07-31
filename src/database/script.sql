-- tạo database

create database if not exists MD17306;

use MD17306;

-- tạo bảng

create table
    if not exists users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        password VARCHAR(5000) NOT NULL,
        name VARCHAR(50) NOT NULL,
        email VARCHAR(50) NOT NULL UNIQUE
    );

insert into
    users (id, password, name, email)
values (
        11,
        '123123',
        'tn',
        'tnmod200@gmail.com'
    );

insert into
    users (id, password, name, email)
values (
        2,
        'Blondie',
        'Iannuzzi',
        'biannuzzi1@foxnews.com'
    );

insert into
    users (id, password, name, email)
values (
        3,
        'Hewie',
        'Mellers',
        'hmellers2@com.com'
    );

insert into
    users (id, password, name, email)
values (
        4,
        'Shadow',
        'Dubery',
        'sdubery3@weather.com'
    );

insert into
    users (id, password, name, email)
values (
        5,
        'Rodrick',
        'Thomason',
        'rthomason4@ucsd.edu'
    );

create table
    reset_password (
        id INT PRIMARY KEY AUTO_INCREMENT,
        token VARCHAR(50) NOT NULL,
        createdAt DATETIME NOT NULL DEFAULT NOW(),
        email VARCHAR(50) NOT NULL,
        avaiable BIT DEFAULT 1
    );

create table
    if not exists categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(50) NOT NULL UNIQUE 
    );

create table
    if not exists products (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(50) NOT NULL,
        price INT NOT NULL,
        image VARCHAR(5000) NOT NULL,
        description VARCHAR(50) NOT NULL,
        quantity INT NOT NULL,
        categoryId INT NOT NULL,
        FOREIGN KEY (categoryId) REFERENCES categories(id)
    );

insert into categories (id, name) values (1, 'Điện thoại');

insert into categories (id, name) values (2, 'Laptop');

insert into categories (id, name) values (3, 'Phụ kiện');

insert into
    products (
        id,
        name,
        price,
        image,
        description,
        quantity,
        categoryId
    )
values (
        1,
        'Điện thoại 1',
        1000,
        'https://asianwiki.com/images/d/de/Chi_Pu-p001.jpg',
        'Điện thoại 1',
        10,
        1
    );

insert into
    products (
        id,
        name,
        price,
        image,
        description,
        quantity,
        categoryId
    )
values (
        2,
        'Điện thoại 2',
        2000,
        'https://asianwiki.com/images/d/de/Chi_Pu-p001.jpg',
        'Điện thoại 2',
        20,
        2
    );

insert into
    products (
        id,
        name,
        price,
        image,
        description,
        quantity,
        categoryId
    )
values (
        3,
        'Điện thoại 3',
        3000,
        'https://asianwiki.com/images/d/de/Chi_Pu-p001.jpg',
        'Điện thoại 3',
        30,
        3
    );