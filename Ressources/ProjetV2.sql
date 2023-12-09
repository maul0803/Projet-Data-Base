SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Creation of the database
DROP DATABASE IF EXISTS project;
CREATE DATABASE project;
USE project;

-- Creation of the table book
CREATE TABLE Book (
    idBook INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(50) NOT NULL,
    Language_ VARCHAR(50) NOT NULL,
    Number_Of_Pages INT NOT NULL,
    Year_Of_Production DATE NOT NULL,
    Subject VARCHAR(50) NOT NULL,
    rack_number INT NOT NULL
);

-- Creation of the table Author
CREATE TABLE Author (
    idAuthor INT PRIMARY KEY AUTO_INCREMENT,
    Author_Name VARCHAR(50) NOT NULL
);

-- Creation of the table publisher
CREATE TABLE Publisher (
    idPublisher INT PRIMARY KEY AUTO_INCREMENT,
    Publisher_Name VARCHAR(50) NOT NULL
);

-- Creation of the table Users
CREATE TABLE Users (
    idUser INT PRIMARY KEY AUTO_INCREMENT,
    profil VARCHAR(50) NOT NULL CHECK (profil IN ('Administrator', 'Library Agent', 'Student')),
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    postal_address VARCHAR(50) NOT NULL,
    phone_number VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    is_registered BOOLEAN NOT NULL
);

-- Creation of the table BookInLibrary
CREATE TABLE BookInLibrary (
    idBookInLibrary INT PRIMARY KEY AUTO_INCREMENT,
    price INT NOT NULL,
    date_of_purchase DATE NOT NULL,
    availability BOOLEAN NOT NULL,
    idBook INT NOT NULL,
    CONSTRAINT fk_Book
        FOREIGN KEY (idBook) 
        REFERENCES Book(idBook) ON DELETE CASCADE
);

-- Creation of the table Card
CREATE TABLE Card (
    idCard INT PRIMARY KEY AUTO_INCREMENT,
    RessourceType VARCHAR(50) CHECK (RessourceType IN ('Book', 'Computer', 'MeetingRoom')) NOT NULL,
    Activation_Date DATE NOT NULL,
    is_active BOOLEAN NOT NULL,
    idUser INT NOT NULL,
    CONSTRAINT fk_User
        FOREIGN KEY (idUser) 
        REFERENCES Users(idUser) ON DELETE CASCADE
);

-- Creation of the table Computer
CREATE TABLE Computer (
    idComputer INT PRIMARY KEY AUTO_INCREMENT,
    availability BOOLEAN NOT NULL
);

-- Creation of the table MeetingRoom
CREATE TABLE MeetingRoom (
    idMeetingRoom INT PRIMARY KEY AUTO_INCREMENT,
    availability BOOLEAN NOT NULL
);

-- Creation of the table Borrow
CREATE TABLE Borrow (
    idBorrow INT PRIMARY KEY AUTO_INCREMENT,
    DateBorrowStart DATE NOT NULL,
    DateBorrowEnd DATE NOT NULL,
    idCard INT NOT NULL,
    idBookInLibrary INT NOT NULL,
    CONSTRAINT fk_Card
        FOREIGN KEY (idCard) 
        REFERENCES Card(idCard) ON DELETE CASCADE,
    CONSTRAINT fk_BookInLibrary
        FOREIGN KEY (idBookInLibrary) 
        REFERENCES BookInLibrary(idBookInLibrary) ON DELETE CASCADE
);

-- Creation of the table UseRoom
CREATE TABLE UseRoom (
    idUseRoom INT PRIMARY KEY AUTO_INCREMENT,
    DateBorrowStart DATE NOT NULL,
    DateBorrowEnd DATE NOT NULL,
    idCard INT NOT NULL,
    idMeetingRoom INT NOT NULL,
    CONSTRAINT fk_Card_UseRoom
        FOREIGN KEY (idCard) 
        REFERENCES Card(idCard) ON DELETE CASCADE,
    CONSTRAINT fk_MeetingRoom_UseRoom
        FOREIGN KEY (idMeetingRoom) 
        REFERENCES MeetingRoom(idMeetingRoom) ON DELETE CASCADE
);

-- Creation of the table UseComputer
CREATE TABLE UseComputer (
    idUseComputer INT PRIMARY KEY AUTO_INCREMENT,
    DateBorrowStart DATE NOT NULL,
    DateBorrowEnd DATE NOT NULL,
    idCard INT NOT NULL,
    idComputer INT NOT NULL,
    CONSTRAINT fk_Card_UseComputer
        FOREIGN KEY (idCard) 
        REFERENCES Card(idCard) ON DELETE CASCADE,
    CONSTRAINT fk_Computer_UseComputer
        FOREIGN KEY (idComputer) 
        REFERENCES Computer(idComputer) ON DELETE CASCADE
);

-- Creation of the table Write
CREATE TABLE Write_ (
    idBook INT NOT NULL,
    idAuthor INT NOT NULL,
    PRIMARY KEY (idBook, idAuthor),
    CONSTRAINT fk_Book_Write
        FOREIGN KEY (idBook) 
        REFERENCES Book(idBook) ON DELETE CASCADE,
    CONSTRAINT fk_Author_Write
        FOREIGN KEY (idAuthor) 
        REFERENCES Author(idAuthor) ON DELETE CASCADE
);

-- Creation of the table Publish
CREATE TABLE Publish (
    idBook INT NOT NULL,
    idPublisher INT NOT NULL,
    PRIMARY KEY (idBook, idPublisher),
    CONSTRAINT fk_Book_Publish
        FOREIGN KEY (idBook) 
        REFERENCES Book(idBook) ON DELETE CASCADE,
    CONSTRAINT fk_Publisher_Publish
        FOREIGN KEY (idPublisher) 
        REFERENCES Publisher(idPublisher) ON DELETE CASCADE
);



-- Trigger delete Author->Delete Books
DELIMITER //
CREATE TRIGGER OnDeleteAuthor AFTER DELETE ON Author
FOR EACH ROW
BEGIN
    DELETE FROM Book WHERE idBook IN (SELECT idBook FROM Write_ WHERE idAuthor = OLD.idAuthor);
END;
//
DELIMITER ;
-- Trigger delete Publisher->Delete Books
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

-- Trigger to check if the card can be used to borrow books
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

-- Trigger to check if the card can be used to borrow a computer
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

-- Trigger to check if the card can be used to borrow a room
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

-- Trigger to test is the user can borrow a book or not
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


-- Trigger to test is the user can borrow a room or not
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

-- Trigger to test is the user can borrow a computer or not
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

-- Trigger to change the availability after the insert of a DateBorrowStart
DELIMITER //
CREATE TRIGGER UpdateAvailabilityFalseBook AFTER INSERT ON Borrow
FOR EACH ROW 
BEGIN
    UPDATE BookInLibrary SET availability=False WHERE idBookInLibrary=NEW.idBookInLibrary;
END;
//
DELIMITER ;
-- Trigger to change the availability after the insert of a DateBorrowEnd
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

-- Trigger to check if the DateBorrowEnd is after DateBorrowStart
DELIMITER //
CREATE TRIGGER CheckDateBook BEFORE UPDATE ON Borrow
FOR EACH ROW 
BEGIN
	IF NEW.DateBorrowEnd < NEW.DateBorrowStart THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The book cannot be borrowed for a negative duration';
    END IF;
END;
//
DELIMITER ;





-- Trigger to change the availability after the insert of a DateBorrowStart (USE ROOM)
DELIMITER //
CREATE TRIGGER UpdateAvailabilityFalseRoom AFTER INSERT ON UseRoom
FOR EACH ROW 
BEGIN
    UPDATE MeetingRoom SET availability=False WHERE idMeetingRoom=NEW.idMeetingRoom;
END;
//
DELIMITER ;
-- Trigger to change the availability after the insert of a DateBorrowEnd (USE ROOM)
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

-- Trigger to check if the DateBorrowEnd is after DateBorrowStart (USE ROOM)
DELIMITER //
CREATE TRIGGER CheckDateRoom BEFORE UPDATE ON UseRoom
FOR EACH ROW 
BEGIN
	IF NEW.DateBorrowEnd < NEW.DateBorrowStart THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The room cannot be borrowed for a negative duration';
    END IF;
END;
//
DELIMITER ;

-- Trigger to change the availability after the insert of a DateBorrowStart (USE Computer)
DELIMITER //
CREATE TRIGGER UpdateAvailabilityFalseComputer AFTER INSERT ON UseComputer
FOR EACH ROW 
BEGIN
    UPDATE Computer SET availability=False WHERE idComputer=NEW.idComputer;
END;
//
DELIMITER ;
-- Trigger to change the availability after the insert of a DateBorrowEnd (USE Computer)
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
-- Trigger to check if the DateBorrowEnd is after DateBorrowStart (USE Computer)
DELIMITER //
CREATE TRIGGER CheckDateComputer BEFORE UPDATE ON UseComputer
FOR EACH ROW 
BEGIN
	IF NEW.DateBorrowEnd < NEW.DateBorrowStart THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The room cannot be borrowed for a negative duration';
    END IF;
END;
//
DELIMITER ;

-- Data Insertion
INSERT INTO Book (Title, Language_, Number_Of_Pages, Year_Of_Production, Subject, rack_number) 
VALUES 
('1984', 'English', 328, '1949-06-08', 'Dystopian', 114),
('To Kill a Mockingbird', 'English', 324, '1960-07-11', 'Fiction', 115),
('The Catcher in the Rye', 'English', 277, '1951-07-16', 'Coming-of-Age', 116),
('The Brothers Karamazov', 'Russian', 824, '1880-11-06', 'Philosophical Fiction', 117),
('The Picture of Dorian Gray', 'English', 254, '1890-07-20', 'Gothic Fiction', 118),
('The Alchemist', 'Portuguese', 197, '1988-01-01', 'Philosophical Fiction', 119),
('The Book Thief', 'English', 552, '2005-03-14', 'Historical Fiction', 120),
('The Road', 'English', 287, '2006-09-26', 'Post-Apocalyptic', 121),
('The Kite Runner', 'English', 371, '2003-05-29', 'Historical Fiction', 122),
('The Art of War', 'Chinese', 100, '5th Century BCE', 'Military Strategy', 123),
('The Silent Patient', 'English', 336, '2019-02-05', 'Psychological Thriller', 124),
('The Girl on the Train', 'English', 323, '2015-01-13', 'Mystery', 125),
('Sapiens: A Brief History of Humankind', 'English', 443, '2011-02-10', 'History', 126),
('Educated', 'English', 334, '2018-02-20', 'Memoir', 127),
('The Da Vinci Code', 'English', 454, '2003-03-18', 'Mystery', 128),
('The Hunger Games', 'English', 374, '2008-09-14', 'Dystopian', 129),
('The Shining', 'English', 447, '1977-01-28', 'Horror', 130),
('The Girl with the Dragon Tattoo', 'Swedish', 590, '2005-08-23', 'Mystery', 131),
('The Martian', 'English', 369, '2011-09-27', 'Science Fiction', 132),
('The Fault in Our Stars', 'English', 313, '2012-01-10', 'Young Adult', 133);

-- Insertion in the table table Author
INSERT INTO Author (Author_Name) 
VALUES 
('George Orwell'),
('Harper Lee'),
('J.D. Salinger'),
('Fyodor Dostoevsky'),
('Oscar Wilde'),
('Paulo Coelho'),
('Markus Zusak'),
('Cormac McCarthy'),
('Khaled Hosseini'),
('Sun Tzu'),
('Alex Michaelides'),
('Paula Hawkins'),
('Yuval Noah Harari'),
('Tara Westover'),
('Dan Brown'),
('Suzanne Collins'),
('Stephen King'),
('Stieg Larsson'),
('Andy Weir'),
('John Green');

-- Insertion in that table Publisher
INSERT INTO Publisher (Publisher_Name) 
VALUES 
('Secker & Warburg'),
('J.B. Lippincott & Co.'),
('Little, Brown and Company'),
('The Russian Messenger'),
('Ward, Lock & Co.'),
('HarperCollins'),
('Random House'),
('Knopf'),
('Riverhead Books'),
('Ancient Chinese Wisdom Publishing'),
('Celadon Books'),
('Riverhead Books'),
('HarperCollins'),
('Random House'),
('Doubleday'),
('Scholastic Press'),
('Doubleday'),
('Norstedts förlag'),
('Crown Publishing'),
('Dutton Books');


-- Insertion in the table Users
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

-- Insertion in the table table BookInLibrary
-- Insertion of 30 additional books in the library
INSERT INTO BookInLibrary (price, date_of_purchase, availability, idBook) 
VALUES 
(15, '2023-04-01', true, 1),
(18, '2023-04-05', true, 2),
(22, '2023-04-10', true, 3),
(15, '2023-04-01', true, 4),
(18, '2023-04-05', true, 5),
(22, '2023-04-10', true, 6),
(25, '2023-04-15', true, 7),
(30, '2023-04-20', true, 8),
(35, '2023-04-25', true, 9),
(40, '2023-04-30', true, 10),
(20, '2023-05-05', true, 11),
(25, '2023-05-10', true, 12),
(30, '2023-05-15', true, 13),
(18, '2023-05-20', true, 14),
(20, '2023-05-25', true, 15),
(25, '2023-06-01', true, 16),
(30, '2023-06-05', true, 17),
(35, '2023-06-10', true, 18),
(40, '2023-06-15', true, 19),
(45, '2023-06-20', true, 20),
(15, '2023-06-25', true, 1),
(18, '2023-07-01', true, 1),
(22, '2023-07-05', true, 1),
(15, '2023-07-10', true, 2),
(18, '2023-07-15', true, 2),
(22, '2023-07-20', true, 6),
(25, '2023-07-25', true, 7),
(30, '2023-07-30', true, 8),
(35, '2023-08-01', true, 9),
(40, '2023-08-05', true, 10);


-- Insertion in the table Card
INSERT INTO Card (RessourceType, Activation_Date, is_active, idUser) 
VALUES 
('Book', '2023-04-01', True, 1),
('Computer', '2023-04-05', True, 1),
('MeetingRoom', '2023-04-10', True, 1),
('Book', '2023-04-15', True, 2),
('Computer', '2023-04-20', True, 2),
('MeetingRoom', '2023-04-25', True, 2),
('Book', '2023-04-30', True, 3),
('Computer', '2023-05-05', True, 3),
('MeetingRoom', '2023-05-10', True, 3),
('Book', '2023-05-15', True, 4),
('MeetingRoom', '2023-06-01', True, 4),
('Book', '2023-06-05', True, 5),
('Computer', '2023-06-10', True, 5),
('Book', '2023-06-20', True, 6),
('Computer', '2023-06-25', True, 6),
('MeetingRoom', '2023-07-01', True, 6),
('Computer', '2023-07-10', True, 7),
('MeetingRoom', '2023-07-15', True, 7),
('Book', '2023-07-20', True, 8),
('Computer', '2023-08-05', True, 9),
('MeetingRoom', '2023-08-10', True, 9),
('Book', '2023-08-15', True, 10),
('MeetingRoom', '2023-08-25', True, 10);


-- Insertion in the table Computer
INSERT INTO Computer (availability) 
VALUES 
(true),
(true),
(true),
(true),
(true),
(true),
(true);

-- Insertion in the table MeetingRoom
INSERT INTO MeetingRoom (availability) 
VALUES 
(true),
(true),
(true),
(true),
(true),
(true),
(true);

-- Insertion in the table Borrow
INSERT INTO Borrow (DateBorrowStart, idCard, idBookInLibrary) 
VALUES 
('2023-04-01', 1, 1),
('2023-04-05', 1, 2),
('2023-04-10', 4, 3),
('2023-04-15', 4, 4),
('2023-04-20', 4, 5),
('2023-04-25', 7, 6),
('2023-04-30', 7, 7),
('2023-05-05', 7, 8),
('2023-05-10', 10, 9),
('2023-05-15', 12, 10),
('2023-05-20', 12, 11),
('2023-05-25', 12, 12),
('2023-06-01', 12, 13),
('2023-06-05', 12, 14),
('2023-06-10', 14, 15),
('2023-06-15', 10, 16),
('2023-06-20', 14, 17),
('2023-06-25', 10, 18),
('2023-07-01', 10, 19),
('2023-07-05', 10, 20);

-- Insertion in the table UseRoom
INSERT INTO UseRoom (DateBorrowStart, idCard, idMeetingRoom) 
VALUES 
('2023-04-25', 3, 1),
('2023-05-10', 9, 3),
('2023-05-10', 6, 4),
('2023-05-10', 9, 5);

-- Insertion in the table UseComputer
INSERT INTO UseComputer (DateBorrowStart, idCard, idComputer) 
VALUES 
('2023-04-01', 2, 1),
('2023-04-05', 5, 2);

-- Insertion of corrected data in the Write_ table
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
(10, 9),
(11, 10),
(12, 11),
(13, 12),
(14, 13),
(15, 14),
(16, 15),
(17, 16),
(18, 17),
(19, 18),
(20, 19);

-- Insertion of corrected data in the Publish table
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
(10, 10),
(11, 11),
(12, 12),
(13, 13),
(14, 14),
(15, 15),
(16, 16),
(17, 17),
(18, 18),
(19, 19),
(20, 20);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

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
GRANT SELECT ON Project.UseRoom TO 'student'@'localhost';-- Maybe shouldn't be possible
GRANT SELECT ON Project.UseComputer TO 'student'@'localhost';-- Maybe shouldn't be possible
GRANT SELECT ON Project.BookInLibrary TO 'student'@'localhost';
GRANT SELECT ON Project.Book TO 'student'@'localhost';-- Necessary to get the titles of the books the student has borrowed
GRANT SELECT ON Project.Computer TO 'student'@'localhost';
GRANT SELECT ON Project.MeetingRoom TO 'student'@'localhost';

GRANT INSERT, UPDATE ON Project.Users TO 'student'@'localhost';
FLUSH PRIVILEGES;