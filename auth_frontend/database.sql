-- Create the database
CREATE DATABASE UserProfileDB;

-- Use the database
USE UserProfileDB;

-- Create the user profile table
CREATE TABLE UserProfile (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    DateOfBirth DATE,
    Gender ENUM('Male', 'Female', 'Other'),
    Address VARCHAR(255),
    City VARCHAR(100),
    State VARCHAR(100),
    Country VARCHAR(100),
    PostalCode VARCHAR(20),
    PhoneNumber VARCHAR(20),
    MainEmail VARCHAR(255) NOT NULL UNIQUE,
    Email2 VARCHAR(255),
    Email3 VARCHAR(255),
    Email4 VARCHAR(255),
    Email5 VARCHAR(255),
    Email6 VARCHAR(255),
    MainPassword VARCHAR(255) NOT NULL,
    AdditionalPassword1 VARCHAR(255),
    AdditionalPassword2 VARCHAR(255),
    AdditionalPassword3 VARCHAR(255),
    AdditionalPassword4 VARCHAR(255),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create indexes for faster search
CREATE INDEX idx_mainemail ON UserProfile(MainEmail);
CREATE INDEX idx_email2 ON UserProfile(Email2);
CREATE INDEX idx_email3 ON UserProfile(Email3);
CREATE INDEX idx_email4 ON UserProfile(Email4);
CREATE INDEX idx_email5 ON UserProfile(Email5);
CREATE INDEX idx_email6 ON UserProfile(Email6);

