-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 09 jan. 2020 à 12:07
-- Version du serveur :  5.7.19
-- Version de PHP :  7.1.9
DROP USER IF EXISTS 'library_agent'@'localhost';
-- Creating 'library_agent' user with permissions
CREATE USER 'library_agent'@'localhost' IDENTIFIED BY '123';
GRANT SELECT ON Project.* TO 'library_agent'@'localhost';
GRANT SELECT ON Project.Users TO 'library_agent'@'localhost';
GRANT SELECT ON Project.Card TO 'library_agent'@'localhost';
GRANT SELECT ON Project.Borrow TO 'library_agent'@'localhost';
GRANT SELECT ON Project.UseRoom TO 'library_agent'@'localhost';
GRANT SELECT ON Project.UseComputer TO 'library_agent'@'localhost';
GRANT SELECT ON Project.BookInLibrary TO 'library_agent'@'localhost';
GRANT SELECT ON Project.Book TO 'library_agent'@'localhost';
GRANT SELECT ON Project.Computer TO 'library_agent'@'localhost';
GRANT SELECT ON Project.MeetingRoom TO 'library_agent'@'localhost';
GRANT INSERT, UPDATE ON Project.Borrow TO 'library_agent'@'localhost';
GRANT INSERT, UPDATE ON Project.UseRoom TO 'library_agent'@'localhost';
GRANT INSERT, UPDATE ON Project.UseComputer TO 'library_agent'@'localhost';
FLUSH PRIVILEGES;
-- Creating 'student' user with permissions
DROP USER IF EXISTS 'student'@'localhost';
CREATE USER 'student'@'localhost' IDENTIFIED BY '123';
GRANT SELECT ON Project.Users TO 'student'@'localhost';
GRANT SELECT ON Project.Card TO 'student'@'localhost';
GRANT SELECT ON Project.Borrow TO 'student'@'localhost';
#GRANT SELECT ON Project.UseRoom TO 'student'@'localhost';
#GRANT SELECT ON Project.UseComputer TO 'student'@'localhost';
GRANT SELECT ON Project.BookInLibrary TO 'student'@'localhost';
GRANT SELECT ON Project.Book TO 'student'@'localhost';/*Necessary to get the titles of the books the student has borrowed*/
GRANT SELECT ON Project.Computer TO 'student'@'localhost';
GRANT SELECT ON Project.MeetingRoom TO 'student'@'localhost';

GRANT UPDATE ON Project.Users TO 'student'@'localhost';
FLUSH PRIVILEGES;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `Project`
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Création de la base de données
DROP DATABASE IF EXISTS project;
CREATE DATABASE project;
USE project;

-- Création de la table Book
CREATE TABLE Book (
    idBook INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(50),
    Language_ VARCHAR(50),
    Number_Of_Pages INT,
    Year_Of_Production DATE,
    Subject VARCHAR(50),
    rack_number INT
);

-- Création de la table Author
CREATE TABLE Author (
    idAuthor INT PRIMARY KEY AUTO_INCREMENT,
    Author_Name VARCHAR(50)
);

-- Création de la table Publisher
CREATE TABLE Publisher (
    idPublisher INT PRIMARY KEY AUTO_INCREMENT,
    Publisher_Name VARCHAR(50)
);

-- Création de la table Users
CREATE TABLE Users (
    idUser INT PRIMARY KEY AUTO_INCREMENT,
    profil VARCHAR(50) NOT NULL CHECK (profil IN ('Administrator', 'Library Agent', 'Student')),
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(50) UNIQUE,
    postal_address VARCHAR(50),
    phone_number VARCHAR(50),
    username VARCHAR(50),
    password VARCHAR(50),
    is_registered BOOLEAN
);

-- Création de la table BookInLibrary
CREATE TABLE BookInLibrary (
    idBookInLibrary INT PRIMARY KEY AUTO_INCREMENT,
    price INT,
    date_of_purchase DATE,
    availability BOOLEAN,
    idBook INT,
    CONSTRAINT fk_Book
        FOREIGN KEY (idBook) 
        REFERENCES Book(idBook) ON DELETE CASCADE
);

-- Création de la table Card
CREATE TABLE Card (
    idCard INT PRIMARY KEY AUTO_INCREMENT,
    RessourceType VARCHAR(50) CHECK (RessourceType IN ('Book', 'Computer', 'MeetingRoom')),
    Activation_Date DATE,
    is_active BOOLEAN,
    idUser INT,
    CONSTRAINT fk_User
        FOREIGN KEY (idUser) 
        REFERENCES Users(idUser) ON DELETE CASCADE
);

-- Création de la table Computer
CREATE TABLE Computer (
    idComputer INT PRIMARY KEY AUTO_INCREMENT,
    availability BOOLEAN
);

-- Création de la table MeetingRoom
CREATE TABLE MeetingRoom (
    idMeetingRoom INT PRIMARY KEY AUTO_INCREMENT,
    availability BOOLEAN
);

-- Création de la table Borrow
CREATE TABLE Borrow (
    idBorrow INT PRIMARY KEY AUTO_INCREMENT,
    DateBorrowStart DATE,
    DateBorrowEnd DATE,
    idCard INT,
    idBookInLibrary INT,
    CONSTRAINT fk_Card
        FOREIGN KEY (idCard) 
        REFERENCES Card(idCard) ON DELETE CASCADE,
    CONSTRAINT fk_BookInLibrary
        FOREIGN KEY (idBookInLibrary) 
        REFERENCES BookInLibrary(idBookInLibrary) ON DELETE CASCADE
);

-- Création de la table UseRoom
CREATE TABLE UseRoom (
    idUseRoom INT PRIMARY KEY AUTO_INCREMENT,
    DateBorrowStart DATE,
    DateBorrowEnd DATE,
    idCard INT,
    idMeetingRoom INT,
    CONSTRAINT fk_Card_UseRoom
        FOREIGN KEY (idCard) 
        REFERENCES Card(idCard) ON DELETE CASCADE,
    CONSTRAINT fk_MeetingRoom_UseRoom
        FOREIGN KEY (idMeetingRoom) 
        REFERENCES MeetingRoom(idMeetingRoom) ON DELETE CASCADE
);

-- Création de la table UseComputer
CREATE TABLE UseComputer (
    idUseComputer INT PRIMARY KEY AUTO_INCREMENT,
    DateBorrowStart DATE,
    DateBorrowEnd DATE,
    idCard INT,
    idComputer INT,
    CONSTRAINT fk_Card_UseComputer
        FOREIGN KEY (idCard) 
        REFERENCES Card(idCard) ON DELETE CASCADE,
    CONSTRAINT fk_Computer_UseComputer
        FOREIGN KEY (idComputer) 
        REFERENCES Computer(idComputer) ON DELETE CASCADE
);

-- Création de la table Write
CREATE TABLE Write_ (
    idBook INT,
    idAuthor INT,
    PRIMARY KEY (idBook, idAuthor),
    CONSTRAINT fk_Book_Write
        FOREIGN KEY (idBook) 
        REFERENCES Book(idBook) ON DELETE CASCADE,
    CONSTRAINT fk_Author_Write
        FOREIGN KEY (idAuthor) 
        REFERENCES Author(idAuthor) ON DELETE CASCADE
);

-- Création de la table Publish
CREATE TABLE Publish (
    idBook INT,
    idPublisher INT,
    PRIMARY KEY (idBook, idPublisher),
    CONSTRAINT fk_Book_Publish
        FOREIGN KEY (idBook) 
        REFERENCES Book(idBook) ON DELETE CASCADE,
    CONSTRAINT fk_Publisher_Publish
        FOREIGN KEY (idPublisher) 
        REFERENCES Publisher(idPublisher) ON DELETE CASCADE
);



/*Trigger delete Author->Delete Books*/
DELIMITER //
CREATE TRIGGER OnDeleteAuthor AFTER DELETE ON Author
FOR EACH ROW
BEGIN
    DELETE FROM Book WHERE idBook IN (SELECT idBook FROM Write_ WHERE idAuthor = OLD.idAuthor);
END;
//
DELIMITER ;
/*Trigger delete Publisher->Delete Books*/
DELIMITER //
CREATE TRIGGER OnDeletePublisher AFTER DELETE ON Publisher
FOR EACH ROW
BEGIN
    DELETE FROM Book WHERE idBook IN (SELECT idBook FROM Publish WHERE idPublisher = OLD.idPublisher);
END;
//
DELIMITER ;











-- Trigger to check if the user can have another card or not
DELIMITER //
CREATE TRIGGER CheckNewCArd BEFORE INSERT ON Card
FOR EACH ROW 
BEGIN
    DECLARE test_number_of_card INT;
    
    SELECT COUNT(idCard) INTO test_number_of_card FROM Card WHERE RessourceType=NEW.RessourceType AND idUser=NEW.idUser;
    IF (test_number_of_card >= 1)
    THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Your already have a card for that ressource type';
    END IF;
END;
//
DELIMITER ;









-- Trigger pour vérifier si la carte peut être utilisée pour emprunter des livres
DELIMITER //
CREATE TRIGGER CheckCardBook BEFORE INSERT ON Borrow
FOR EACH ROW 
BEGIN
    DECLARE test_ressource_type VARCHAR(50);
	DECLARE test_is_active BOOLEAN;
    
    SELECT RessourceType,is_active INTO test_ressource_type,test_is_active FROM Card WHERE idCard = NEW.idCard;
    
    IF (test_is_active = FALSE)
    THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This card is inactive';
    END IF;
    
    IF NOT (test_ressource_type = 'Book') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This card is not for books';
    END IF;
END;
//
DELIMITER ;

-- Trigger pour vérifier si la carte peut être utilisée pour utiliser un ordinateur
DELIMITER //
CREATE TRIGGER CheckCardComputer BEFORE INSERT ON UseComputer
FOR EACH ROW 
BEGIN
    DECLARE test_ressource_type VARCHAR(50);
	DECLARE test_is_active BOOLEAN;
    
    SELECT RessourceType,is_active INTO test_ressource_type,test_is_active FROM Card WHERE idCard = NEW.idCard;
    
    IF (test_is_active = FALSE)
    THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This card is inactive';
    END IF;
    
    IF NOT (test_ressource_type = 'Computer') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This card is not for computers';
    END IF;
END;
//
DELIMITER ;

-- Trigger pour vérifier si la carte peut être utilisée pour utiliser une salle
DELIMITER //
CREATE TRIGGER CheckCardRoom BEFORE INSERT ON UseRoom
FOR EACH ROW 
BEGIN
    DECLARE test_ressource_type VARCHAR(50);
	DECLARE test_is_active BOOLEAN;
    
    SELECT RessourceType,is_active INTO test_ressource_type,test_is_active FROM Card WHERE idCard = NEW.idCard;
    
    IF (test_is_active = FALSE)
    THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This card is inactive';
    END IF;
    
    IF NOT (test_ressource_type = 'MeetingRoom') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This card is not for meeting rooms';
    END IF;
END;
//
DELIMITER ;

/*Trigger to test is the user can borrow a book or not*/
DELIMITER //
CREATE TRIGGER CanBorrowBook BEFORE INSERT ON Borrow
FOR EACH ROW
BEGIN
    DECLARE number_of_books INT;
    DECLARE test_is_registered BOOLEAN;
    DECLARE test_available BOOLEAN;
    DECLARE cardID INT;
    
    SELECT availability INTO test_available FROM BookInLibrary WHERE idBookInLibrary=NEW.idBookInLibrary;
    SELECT COUNT(idCard) INTO number_of_books FROM Borrow WHERE idCard = NEW.idCard AND DateBorrowEnd IS NULL;
    SELECT is_registered INTO test_is_registered
    FROM Users JOIN Card ON Users.idUser=Card.idUser
    WHERE Card.idCard=NEW.idCard;
	IF (test_available = FALSE ) THEN 
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This book is not available';
	END IF;
	IF ((number_of_books >= 5 AND test_is_registered = TRUE) OR (number_of_books >= 1 AND test_is_registered = FALSE)) THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'You cannot borrow more books';
	END IF;
    
END;
//
DELIMITER ;


/*Trigger to test is the user can borrow a room or not*/
DELIMITER //
CREATE TRIGGER CanUseRoom BEFORE INSERT ON UseRoom
FOR EACH ROW
BEGIN
    DECLARE test_available BOOLEAN;
    
    SELECT availability INTO test_available FROM MeetingRoom WHERE idMeetingRoom=NEW.idMeetingRoom;
	IF (test_available = FALSE ) THEN 
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This room is not available';
	END IF;

END;
//
DELIMITER ;

/*Trigger to test is the user can borrow a computer or not*/
DELIMITER //
CREATE TRIGGER CanUseComputer BEFORE INSERT ON UseComputer
FOR EACH ROW
BEGIN
    DECLARE test_available BOOLEAN;
    
    SELECT availability INTO test_available FROM Computer WHERE idComputer=NEW.idComputer;
	IF (test_available = FALSE ) THEN 
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This computer is not available';
	END IF;

END;
//
DELIMITER ;

/*Trigger to change the availability after the insert of a DateBorrowStart*/
DELIMITER //
CREATE TRIGGER UpdateAvailabilityFalseBook AFTER INSERT ON Borrow
FOR EACH ROW 
BEGIN
    UPDATE BookInLibrary SET availability=False WHERE idBookInLibrary=NEW.idBookInLibrary;
END;
//
DELIMITER ;
/*Trigger to change the availability after the insert of a DateBorrowEnd*/
DELIMITER //
CREATE TRIGGER UpdateAvailabilityTrueBook AFTER UPDATE ON Borrow
FOR EACH ROW 
BEGIN
	DECLARE test_Date_Borrow_End DATE;
    SELECT DateBorrowEnd INTO test_Date_Borrow_End FROM Borrow WHERE Borrow.idBorrow=NEW.idBorrow;
	IF (test_Date_Borrow_End IS NOT NULL) 
    THEN UPDATE BookInLibrary SET availability=True WHERE idBookInLibrary=NEW.idBookInLibrary;
    END IF;
END;
//
DELIMITER ;









/*Trigger to change the availability after the insert of a DateBorrowStart (USE ROOM)*/
DELIMITER //
CREATE TRIGGER UpdateAvailabilityFalseRoom AFTER INSERT ON UseRoom
FOR EACH ROW 
BEGIN
    UPDATE MeetingRoom SET availability=False WHERE idMeetingRoom=NEW.idMeetingRoom;
END;
//
DELIMITER ;
/*Trigger to change the availability after the insert of a DateBorrowEnd (USE ROOM)*/
DELIMITER //
CREATE TRIGGER UpdateAvailabilityTrueRoom AFTER UPDATE ON UseRoom
FOR EACH ROW 
BEGIN
    DECLARE test_Date_Borrow_End DATE;
    SELECT DateBorrowEnd INTO test_Date_Borrow_End FROM UseRoom WHERE idUseRoom=NEW.idUseRoom;
    IF (test_Date_Borrow_End IS NOT NULL) THEN
        UPDATE MeetingRoom SET availability=True WHERE idMeetingRoom=NEW.idMeetingRoom;
    END IF;
END;
//
DELIMITER ;

/*Trigger to change the availability after the insert of a DateBorrowStart (USE Computer)*/
DELIMITER //
CREATE TRIGGER UpdateAvailabilityFalseComputer AFTER INSERT ON UseComputer
FOR EACH ROW 
BEGIN
    UPDATE Computer SET availability=False WHERE idComputer=NEW.idComputer;
END;
//
DELIMITER ;
/*Trigger to change the availability after the insert of a DateBorrowEnd (USE Computer)*/
DELIMITER //
CREATE TRIGGER UpdateAvailabilityTrueComputer AFTER UPDATE ON UseComputer
FOR EACH ROW 
BEGIN
    DECLARE test_Date_Borrow_End DATE;
    SELECT DateBorrowEnd INTO test_Date_Borrow_End FROM UseComputer WHERE idUseComputer=NEW.idUseComputer;
    IF (test_Date_Borrow_End IS NOT NULL) THEN
        UPDATE Computer SET availability=True WHERE idComputer=NEW.idComputer;
    END IF;
END;
//
DELIMITER ;



-- Insertion de données supplémentaires dans la table Book
INSERT INTO Book (Title, Language_, Number_Of_Pages, Year_Of_Production, Subject, rack_number) 
VALUES 
('The Great Gatsby', 'English', 180, '1925-04-10', 'Fiction', 104),
('Moby-Dick', 'English', 635, '1851-10-18', 'Adventure', 105),
('Pride and Prejudice', 'English', 279, '1813-01-28', 'Romance', 106),
('One Hundred Years of Solitude', 'Spanish', 417, '1967-05-30', 'Magic Realism', 107),
('Brave New World', 'English', 311, '1932-06-11', 'Dystopian', 108),
('The Hobbit', 'English', 310, '1937-09-21', 'Fantasy', 109),
('The Odyssey', 'Greek', 442, '1937-09-21', 'Epic', 110),
('The Iliad', 'Greek', 683, '1937-09-21', 'Epic', 111),
('Crime and Punishment', 'Russian', 671, '1866-11-11', 'Psychological Fiction', 112),
('The Lord of the Rings', 'English', 1008, '1954-07-29', 'Fantasy', 113);

-- Insertion de données supplémentaires dans la table Author
INSERT INTO Author (Author_Name) 
VALUES 
('F. Scott Fitzgerald'),
('Herman Melville'),
('Jane Austen'),
('Gabriel Garcia Marquez'),
('Aldous Huxley'),
('J.R.R. Tolkien'),
('Homer'),
('Fyodor Dostoevsky'),
('J.R.R. Tolkien');

-- Insertion de données supplémentaires dans la table Publisher
INSERT INTO Publisher (Publisher_Name) 
VALUES 
('Charles Scribner s Sons'),
('Richard Bentley'),
('Thomas Egerton'),
('Harper & Row'),
('Chatto & Windus'),
('Allen & Unwin'),
('Truc'),
('Bidule'),
('The Russian Messenger'),
('Allen & Unwin');

-- Insertion de données supplémentaires dans la table Users
INSERT INTO Users (profil, first_name, last_name, email, postal_address, phone_number, username, password, is_registered) 
VALUES 
('Administrator', 'admin_first', 'admin_last', 'admin_mail', 'admin', '00000000', 'admin', 'admin', true),
('Student', 'student_first', 'student_last', 'student_mail', 'student', '00000000', 'student', 'student', true),
('Library Agent', 'library_agent_first', 'library_agent_last', 'library_agent_mail', 'library_agent', '00000000', 'library_agent', 'library_agent', true),
('Student', 'Alice', 'Johnson', 'alice.j@example.com', '456 Student St', '123789456', 'alicej', 'studentpass', true),
('Student', 'Bob', 'Smith', 'bob.s@example.com', '789 Student St', '987654321', 'bobs', 'studentpass', true),
('Student', 'Eva', 'Davis', 'eva.d@example.com', '123 Student St', '456987123', 'evad', 'studentpass', true),
('Student', 'David', 'Wilson', 'david.w@example.com', '456 Student St', '789456123', 'davidw', 'studentpass', true),
('Student', 'Sophie', 'Brown', 'sophie.b@example.com', '789 Student St', '123456789', 'sophieb', 'studentpass', true);

-- Insertion de données supplémentaires dans la table BookInLibrary
INSERT INTO BookInLibrary (price, date_of_purchase, availability, idBook) 
VALUES 
(15, '2022-04-01', true, 1),
(15, '2022-04-01', true, 1),
(15, '2022-04-01', true, 1),
(15, '2022-04-01', true, 4),
(18, '2022-04-05', true, 5),
(22, '2022-04-10', true, 6),
(25, '2022-04-15', true, 7),
(30, '2022-04-20', true, 8),
(35, '2022-04-25', true, 9),
(40, '2022-04-30', true, 10);

-- Insertion de données supplémentaires dans la table Card
INSERT INTO Card (RessourceType, Activation_Date, is_active, idUser) 
VALUES 
('Book', '2022-04-01', True, 1),
('Computer', '2022-04-05', True, 1),
('MeetingRoom', '2022-04-10', True, 1),
('Book', '2022-04-15', True, 2),
('Computer', '2022-04-20', True, 2),
('MeetingRoom', '2022-04-25', True, 2),
('Book', '2022-04-30', True, 3),
('Computer', '2022-05-05', True, 3),
('MeetingRoom', '2022-05-10', True, 3),
('Book', '2022-05-15', True, 4);

-- Insertion de données supplémentaires dans la table Computer
INSERT INTO Computer (availability) 
VALUES 
(true),
(true),
(true);

-- Insertion de données supplémentaires dans la table MeetingRoom
INSERT INTO MeetingRoom (availability) 
VALUES 
(true),
(true),
(true);

-- Insertion de données supplémentaires dans la table Borrow
INSERT INTO Borrow (DateBorrowStart, idCard, idBookInLibrary) 
VALUES 
('2022-04-01', 4, 4),
('2022-04-01', 4, 5),
('2022-04-01', 4, 6),
('2022-04-01', 4, 7),
('2022-04-01', 4, 8);

-- Insertion de données supplémentaires dans la table UseRoom
INSERT INTO UseRoom (DateBorrowStart, idCard, idMeetingRoom) 
VALUES 
('2022-04-25', 3, 1),
('2022-04-25', 6, 2),
('2022-05-10', 9, 3);

-- Insertion de données supplémentaires dans la table UseComputer
INSERT INTO UseComputer (DateBorrowStart, idCard, idComputer) 
VALUES 
('2022-04-01', 2, 1),
('2022-04-05', 5, 2);

-- Insertion de données supplémentaires dans la table Write_
INSERT INTO Write_ (idBook, idAuthor) 
VALUES 
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 7),
(9, 8),
(10, 9);

-- Insertion de données supplémentaires dans la table Publish
INSERT INTO Publish (idBook, idPublisher) 
VALUES 
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
