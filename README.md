CRUD PHP - Laravel

    Sistema de gerenciamento de usuários com vinculação de cores, desenvolvido em Laravel 8 e PHP 7.4, utilizando JavaScript, Bootstrap, DataTables e Ajax para uma experiência dinâmica e responsiva.

Tecnologias Utilizadas

    Laravel 8
    PHP 7.4
    JavaScript (jQuery + Ajax)
    Bootstrap 5
    DataTables para listagem dinâmica
    Selectize.js para seleção múltipla de cores
    SweetAlert para feedback visual


- SQL :
    CREATE DATABASE users_test;

    CREATE TABLE colors (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL
    );


    CREATE TABLE users (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL
    );

    INSERT INTO colors (name) VALUES 
        ('Blue'),
        ('Red'),
        ('Yellow'),
        ('Green');

    INSERT INTO users (name, email) VALUES
        ('Foo Bar', 'foo@bar'),
        ('Bar Baz', 'bar@baz'),
        ('Baz Foo', 'baz@foo');


    CREATE TABLE user_color (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        color_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, color_id),
        CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_color FOREIGN KEY (color_id) REFERENCES colors(id) ON DELETE CASCADE
    );

