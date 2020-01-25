create database Ecommerce;
use Ecommerce;

create table Users(idno INT AUTO_INCREMENT, UserID VARCHAR(30), Pass VARCHAR(30), Email VARCHAR(30), Permission INT NOT NULL, 
	PRIMARY KEY(idno));
	INSERT INTO Users (UserID, Pass, Email, Permission) VALUES ('cs405', 'cs405', 'cs405@gmail.com', 3);
	INSERT INTO Users (UserID, Pass, Email, Permission) VALUES ('staff', 'staff', 'staff@gmail.com', 3);

create table Cart(uid INT NOT NULL,pid INT NOT NULL, pname VARCHAR(30), pquantity INT NOT NULL, pprice DOUBLE, pdiscount DOUBLE, UNIQUE (uid, pname));

create table Orders(orderID INT AUTO_INCREMENT, uid INT NOT NULL, productName VARCHAR(30), productID INT NOT NULL, total DOUBLE NOT NULL, shipped BOOLEAN, 
	PRIMARY KEY(orderID));

create table Shipped(uid INT NOT NULL, orderID INT NOT NULL, quantity INT NOT NULL, productID INT NOT NULL, date DATE, 
	PRIMARY KEY(orderID));

create table Items(Product_Id INT NOT NULL, Product_name VARCHAR(30), Product_Quantity INT NOT NULL, Product_Price DOUBLE, Discount DOUBLE, Image LONGBLOB, 
	PRIMARY KEY (Product_Id));
	INSERT INTO Items (Product_Id, Product_name, Product_Quantity, Product_Price, Discount) VALUES (123, 'Doll', 30, 10.99, 0);
	INSERT INTO Items (Product_Id, Product_name, Product_Quantity, Product_Price, Discount) VALUES (124, 'Lego', 40, 15.99, 10);
	INSERT INTO Items (Product_Id, Product_name, Product_Quantity, Product_Price, Discount) VALUES (125, 'Ball', 50, 20.99, 10);
	INSERT INTO Items (Product_Id, Product_name, Product_Quantity, Product_Price, Discount) VALUES (126, 'Crayon', 60, 25.99, 0);
	INSERT INTO Items (Product_Id, Product_name, Product_Quantity, Product_Price, Discount) VALUES (127, 'Car', 70, 30.99, 0);
	INSERT INTO Items (Product_Id, Product_name, Product_Quantity, Product_Price, Discount) VALUES (128, 'Plane', 25, 40.99, 15);
	INSERT INTO Items (Product_Id, Product_name, Product_Quantity, Product_Price, Discount) VALUES (129, 'Phone', 25, 44.99, 10);
	INSERT INTO Items (Product_Id, Product_name, Product_Quantity, Product_Price, Discount) VALUES (130, 'Bat', 35, 19.99, 3);
	INSERT INTO Items (Product_Id, Product_name, Product_Quantity, Product_Price, Discount) VALUES (131, 'Baseball', 45, 9.99, 0);

create table Pending(orderID INT AUTO_INCREMENT, uid INT NOT NULL, productName VARCHAR(30), productid INT NOT NULL, 
	pquantity INT NOT NULL, total DOUBLE, shipped BOOL, 
	PRIMARY KEY(orderID));
