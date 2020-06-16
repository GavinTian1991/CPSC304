
DROP TABLE Sends_To_Account;
DROP TABLE Notification;
DROP TABLE Drink_Offered_By;
DROP TABLE Drinks;
DROP TABLE Drink_Is_Typeof;
DROP TABLE Shared_Drink_Types;
DROP TABLE Holds_Sales_Event;
DROP TABLE Open_At_Business_Hour;
DROP TABLE Favored_by;
DROP TABLE Reply_with_Feedback;
DROP TABLE Comments_from_Customer;
DROP TABLE Milk_Tea_Shop;
DROP TABLE Zipcode_To_Region;
DROP TABLE Price_Level;
DROP TABLE Region;
DROP TABLE Business_Owner_Account;
DROP TABLE Customer_Account;
DROP TABLE Account;



CREATE TABLE Account (
        Account_ID integer PRIMARY KEY,
        User_Name varchar(20) NOT NULL,
        Email varchar(30) NOT NULL UNIQUE,
        Password varchar(30) NOT NULL
);

INSERT INTO Account VALUES 
    (101, 'Allen Smith', 'AllenSmith@gmail.com', 'aaaa'),
    (102, 'Andy Davis', 'AndyDavis@gmail.com', 'bbbb'),
    (103, 'Barry White', 'BarryWhite@gmail.com', 'cccc'),
    (104, 'Bruce White', 'BruceWhite@gmail.com', 'dddd'),
    (105, 'Carson Evens', 'CarsonEvens@gmail.com', 'eeee'),
    (106, 'Carter Stark', 'CarterStark@yahoo.com', 'ffff'),
    (107, 'King Li', 'KingLi@yahoo.com', 'gggg'),
    (108, 'Kerr Tian', 'KerrTian@yahoo.com', 'hhhh'),
    (109, 'Parker Jones', 'ParkerJones@gmail.com', 'IIII'),
    (110, 'Parker Jones', 'PJones@gamil.com', 'JJJJ');


CREATE TABLE Customer_Account(
        Account_ID integer PRIMARY KEY,
        Birthdate date,
        FOREIGN KEY (Account_ID) REFERENCES Account (Account_ID) ON UPDATE CASCADE
);

INSERT INTO Customer_Account VALUES 
    (101, '2008-11-11'),
    (102, '1997-3-3'),
    (103, '2003-9-20'),
    (104, '1988-12-30'),
    (105, '1988-12-30');


CREATE TABLE Business_Owner_Account(
        Account_ID integer PRIMARY KEY,
        Business_License varchar(20) NOT NULL UNIQUE,
        FOREIGN KEY (Account_ID) REFERENCES Account(Account_ID) ON UPDATE CASCADE
);

INSERT INTO Business_Owner_Account VALUES 
    (106, '18-123456'),
    (107, '19-121314'),
    (108, '20-212223'),
    (109, '20-313233'),
    (110, '17-414243');


CREATE TABLE Region(
        Region_ID  integer PRIMARY KEY,
        Name varchar(20) UNIQUE NOT NULL
);

INSERT INTO Region
VALUES
    (401, 'Richmond'),
    (402, 'Vancouver'),
    (403, 'Burnaby'),
    (404, 'Surrey'),
    (405, 'Coquitlam'),
    (406, 'Downtown');


CREATE TABLE Price_Level (
           	Level_ID integer PRIMARY KEY,
           	Name varchar(20) UNIQUE NOT NULL
);

INSERT INTO Price_Level
VALUES
    (501, '$'),
    (502, '$$'),
    (503, '$$$'),
    (504, '$$$$'),
    (599, 'N/A');

CREATE TABLE Zipcode_To_Region (
        Zip_Code varchar(10) PRIMARY KEY,
        Region integer NOT NULL,
        FOREIGN KEY (Region) REFERENCES Region(Region_ID) ON UPDATE CASCADE
);

INSERT INTO Zipcode_To_Region
VALUES
    ('V6X 4B5', 401),
    ('V6P 4Z2', 402),
    ('V5H 2A9', 403),
    ('V6E 1C2', 406),
    ('V3K 3V9', 405);

CREATE TABLE Milk_Tea_Shop (
        Shop_ID integer PRIMARY KEY,
        Shop_Name varchar(30) NOT NULL,
        Address varchar(60) NOT NULL,
        Zip_Code varchar(10) NOT NULL,
        Phone_Number varchar(20) UNIQUE NOT NULL,
        Has_Wifi bit,
        Offer_Delivery bit,
        Good_For_Group integer,
        Price_ID integer NOT NULL DEFAULT 599,
        Owner_ID integer NOT NULL,
        Average_Rating real DEFAULT 0,
        UNIQUE (Address, Zip_Code),
        UNIQUE (Shop_Name, Zip_Code),
        FOREIGN KEY (Zip_Code) REFERENCES Zipcode_To_Region(Zip_Code) ON UPDATE CASCADE,
        FOREIGN KEY (Price_ID) REFERENCES Price_Level(Level_ID) ON UPDATE CASCADE,  
        FOREIGN KEY (Owner_ID) REFERENCES Business_Owner_Account (Account_ID) ON UPDATE CASCADE
);

INSERT INTO Milk_Tea_Shop
VALUES
      (601, 'Wushiland Boba', '1228 - 8338 Capstan Way', 'V6X 4B5','(604) 285 8668', 0, 1, 0, 502, 106, 0),              
      (602, 'Taan Char', '7908 Granville St', 'V6P 4Z2','(604) 428 8292', 1, 1, 4, 503, 107, 0),
      (603, 'Comebuy Bubble Tea', '1610 - 4500 Kingsway', 'V5H 2A9', '(778) 806 1158', 0, 1, 0, 502, 108, 0),
      (604, 'Taan Char', '1696 - 4500 Kingsway', 'V5H 2A9', '(604) 620 9069', 0, 1, 2, 502, 107, 0),
      (605, 'Meet Fresh','1232 Robson St', 'V6E 1C2', '(604) 559 7717', 1, 1, 6, 504, 109, 0),
      (606, 'Sharetea', '435Q North Rd', 'V3K 3V9', '(778) 355 9922', 1, 1, 4, 502, 110, 0);

CREATE TABLE Comments_from_Customer (
        Comment_ID integer PRIMARY KEY,
        Contents varchar(300) NOT NULL,
        Rating_Level integer NOT NULL,
        Date datetime NOT NULL,
        Account_ID integer NOT NULL,
        Shop_ID integer NOT NULL,
        UNIQUE(Account_ID, Shop_ID),
        FOREIGN KEY (Account_ID) REFERENCES Customer_Account(Account_ID) ON UPDATE CASCADE,
        FOREIGN KEY (Shop_ID) REFERENCES Milk_Tea_Shop (Shop_ID) ON UPDATE CASCADE
);

INSERT INTO Comments_from_Customer VALUES 
(701, 'The waiter is very polite and the service is good.', 5, '2020-5-30', 101, 605),
(702, 'Compared to before, it is a bit backward.', 4, '2020-5-17', 102, 606),
(703, 'Oreo dirty tea is great!', 5, '2020-5-10', 103, 602),
(704, 'The shop provides no straws.', 3, '2019-9-1', 104, 603),
(705, 'Icy Taro Ball #4 is perfect!', 5, '2020-5-30', 105, 605);


CREATE TABLE Reply_with_Feedback (
        Comment_ID integer PRIMARY KEY,
        Contents varchar(300) NOT NULL,
        Date datetime NOT NULL,
        Account_ID integer NOT NULL,
        Replied_Comment_ID integer NOT NULL,
        UNIQUE(Account_ID, Replied_Comment_ID),
        FOREIGN KEY (Account_ID) REFERENCES Business_Owner_Account(Account_ID) ON UPDATE CASCADE,
        FOREIGN KEY (Replied_Comment_ID) REFERENCES Comments_from_Customer(Comment_ID) ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO Reply_with_Feedback VALUES 
(801, 'We will continue to provide quality services.', '2020-5-30', 109, 701),
(802, 'We will improve ourself to catch up previous level.', '2020-5-18', 110, 702),
(803, 'Good to hear you like our Oreo dirty tea.', '2020-5-12', 107, 703),
(804, 'Sorry for the inconvenience, we will provide straws and other essentials.', '2019-9-1', 108, 704),
(805, 'Good to hear you like our Icy Taro Ball.', '2020-5-30', 109, 705);



CREATE TABLE Favored_by (
        Shop_ID Integer,
        Customer_Account_ID integer,
        PRIMARY KEY(Shop_ID, Customer_Account_ID),
        FOREIGN KEY (Shop_ID) REFERENCES Milk_Tea_Shop (Shop_ID) ON UPDATE CASCADE, 
        FOREIGN KEY (Customer_Account_ID) REFERENCES Customer_Account (Account_ID) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO Favored_by VALUES 
     (601, 101),
     (601, 102),
     (602, 103),
     (604, 104),
     (605, 105);

CREATE TABLE Open_At_Business_Hour (
        Shop_ID Integer,
        Business_Day varchar(30),
        Open_Time time NOT NULL,
        Close_Time time NOT NULL, 
        PRIMARY KEY (Shop_ID, Business_Day),
        FOREIGN KEY (Shop_ID) REFERENCES Milk_Tea_Shop (Shop_ID) ON UPDATE CASCADE
);

INSERT INTO Open_At_Business_Hour 
VALUES 
    (601, 'Monday', '11:00:00', '21:30:00'),
    (601, 'Tuesday', '11:00:00', '21:30:00'),
    (601, 'Wednesday', '11:00:00', '21:30:00'),
    (601, 'Thursday', '11:00:00', '21:30:00'),
    (601, 'Friday', '11:00:00', '22:30:00'),
    (601, 'Saturday', '11:00:00', '22:30:00'),
    (601, 'Sunday', '11:00:00', '21:30:00'),
    (602, 'Monday', '12:00:00', '23:00:00'),
    (602, 'Tuesday', '12:00:00', '23:00:00'),
    (602, 'Wednesday', '12:00:00', '23:00:00'),
    (602, 'Thursday', '12:00:00', '23:00:00'),
    (602, 'Friday', '12:00:00', '24:00:00'),
    (602, 'Saturday', '12:00:00', '24:00:00'),
    (602, 'Sunday', '12:00:00', '23:00:00'),
    (603, 'Monday', '11:00:00', '22:00:00'),
    (603, 'Tuesday', '11:00:00', '22:00:00'),
    (603, 'Wednesday', '11:00:00', '22:00:00'),
    (603, 'Thursday', '11:00:00', '22:00:00'),
    (603, 'Friday', '11:00:00', '22:00:00'),
    (603, 'Saturday', '11:00:00', '22:00:00'),
    (603, 'Sunday', '11:00:00', '22:00:00');

CREATE TABLE Holds_Sales_Event (
        Shop_ID Integer,
        Event_name varchar(20),
        Event_Content varchar(60) NOT NULL,
        PRIMARY KEY (Shop_ID, Event_name),
        FOREIGN KEY (Shop_ID) REFERENCES Milk_Tea_Shop (Shop_ID) ON UPDATE CASCADE
);

INSERT INTO Holds_Sales_Event VALUES
(601, 'Couple Sale', 'Buy one get one free!'), 
(602, 'Lemon Lemon Lemon', 'One Whole Lemon Half Price!'),
(603, 'Family Sale', 'Buy two get one free!'),
(604, 'Christmas Sale', '20% off  on all drinks!'),
(605, 'Happy Weekends', '50% off on all drinks in weekends!');


CREATE TABLE Shared_Drink_Types (
        Type_ID Integer PRIMARY KEY,
        Type_Name varchar(20) UNIQUE NOT NULL
);

INSERT INTO Shared_Drink_Types VALUES 
        (1301, 'Milk Tea'),
        (1302, 'Fruit Tea'),
        (1303, 'Fresh Milk'),
        (1304, 'Fresh Tea'),
        (1305, 'Slush'),
        (1306, 'Cream Cap Tea'),
        (1307, 'Coffee'),
        (1308, 'Dessert');

CREATE TABLE Drink_Is_Typeof (
        Drink_Name varchar(30) PRIMARY KEY,
        Shared_Type_ID integer NOT NULL,
        FOREIGN KEY (Shared_Type_ID) REFERENCES Shared_Drink_Types (Type_ID) ON UPDATE CASCADE
);  

INSERT INTO Drink_Is_Typeof VALUES
    ('Oolong Milk Tea', 1301),
    ('Ice Cream Green Tea Latte', 1307),
    ('One Whole Lemon', 1302),
    ('Sakura Light Oolong', 1304),
    ('Ultimate QQ Milk Tea', 1301),
    ('Passion Fruit Blast', 1302),
    ('Winter Melon Fresh Milk Tea', 1301),
    ('Herbal Tea with Fresh Milk', 1303),
    ('Cocoa Creama', 1308),
    ('Taro Ice Blended with Pudding', 1305);

CREATE TABLE Drinks (
        Drink_ID integer PRIMARY KEY,
        Drink_Name varchar(30) UNIQUE NOT NULL,
        Description varchar(200),
        Price real NOT NULL,
        Hot_or_Cold integer NOT NULL,
        Specialized_Type_Name varchar(20),
        FOREIGN KEY (Drink_Name) REFERENCES Drink_Is_Typeof(Drink_Name) ON UPDATE CASCADE
);

INSERT INTO Drinks VALUES
    (1401, 'Oolong Milk Tea', '', 5.3, 3, 'Brew Tea'),
    (1402, 'Ice Cream Green Tea Latte', '', 6.4, 3, 'Latte'),
    (1403, 'One Whole Lemon', 'Fresh Lemon Juice made from a whole lemon!', 6.55, 2, 'Ultimate Fruit Tea'),
    (1404, 'Sakura Light Oolong', 'Oolong tea dressed with Sakuara petals', 5.75, 3, 'Taan Feature'),
    (1405, 'Ultimate QQ Milk Tea', '', 5.9, 3, 'Milk Tea'),
    (1406, 'Passion Fruit Blast', '', 4.8, 2, 'Latte'),
    (1407, 'Winter Melon Fresh Milk Tea', 'Milk tea made from winter melon tea', 6.00, 3, 'Winter Melon Tea' ),
    (1408, 'Herbal Tea with Fresh Milk', '', 6.00, 3, 'Herbal Tea'),
    (1409, 'Cocoa Creama', 'Cream Cap tea with Coco chips', 6.44, 3, 'Creama'),
    (1410, 'Taro Ice Blended with Pudding', 'Served with large only, Non-Caffeinated', 7.65, 2, 'Ice Blended');

CREATE TABLE Drink_Offered_By (
    Drink_ID integer,
    Shop_ID integer,
    PRIMARY KEY (Drink_ID, Shop_ID),
    FOREIGN KEY (Drink_ID) REFERENCES Drinks (Drink_ID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Shop_ID) REFERENCES Milk_Tea_Shop (Shop_ID) ON UPDATE CASCADE
);

INSERT INTO Drink_Offered_By VALUES
    (1401, 601),
    (1402, 601),
    (1403, 602),
    (1404, 602),
    (1403, 604),
    (1404, 604),
    (1405, 603),
    (1406, 603),
    (1407, 605),
    (1408, 605),
    (1409, 606),
    (1410, 606);
    
CREATE TABLE Notification (
        Notification_ID Integer PRIMARY KEY,
        Type varchar(20),
        Contents varchar(400) NOT NULL
);

INSERT INTO Notification VALUES
    (1701, 'comment', 'You have a new customer comment from Allen Smith.'),
    (1702, 'comment', 'You have a new customer comment from Andy Davis.'),
    (1703, 'feedback', 'You have a new shop feedback from Wushiland Boba.'),
    (1704, 'feedback', 'You have a new shop feedback from Taan Char.'),
    (1705, 'sale', 'You have a new sale from Sharetea');

CREATE TABLE Sends_To_Account (
        Notification_ID Integer,
        Account_ID Integer,
        Send_Date datetime,
        If_On_Read bit NOT NULL,
        PRIMARY KEY (Notification_ID, Account_ID),
        FOREIGN KEY (Account_ID) REFERENCES Account (Account_ID) ON UPDATE CASCADE,
        FOREIGN KEY (Notification_ID) REFERENCES Notification (Notification_ID) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO Sends_To_Account VALUES
    (1701, 106, '2020-5-30',0),
    (1702, 107, '2020-5-17',0),
    (1703, 101, '2020-5-30',0),
    (1704, 102, '2020-5-17',0),
    (1705, 103, '2020-5-30',0);
    






