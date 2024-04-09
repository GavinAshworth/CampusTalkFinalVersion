DROP TABLE IF EXISTS UserCourse;
DROP TABLE IF EXISTS UserDept;
DROP TABLE IF EXISTS Comments;
DROP TABLE IF EXISTS Likes;
DROP TABLE IF EXISTS Posts;
DROP TABLE IF EXISTS Course;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Department;




-- Table for storing user information
CREATE TABLE Users (
    userID INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    userpassword VARCHAR(255) NOT NULL,
    profileimage LONGBLOB, -- this stores the image in our system
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    isAdmin BOOLEAN DEFAULT FALSE,
    isEnabled BOOLEAN DEFAULT TRUE
);

-- Table for storing groups (e.g., majors and minors)
-- Think Computer Science, Data Science, Engineering etc.
CREATE TABLE Department (
    departmentID INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    shorthand CHAR(4) NOT NULL,
    description TEXT
);

-- Table for storing class sections within subgroups
-- Think COSC 101, COSC 111 etc.
CREATE TABLE Course (
    courseID INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    departmentID INT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (departmentID) REFERENCES Department(departmentID) ON DELETE CASCADE
);

-- Table for storing posts
CREATE TABLE Posts (
    postID INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    userID INT,
    departmentID INT NOT NULL,
    courseID INT,
    postImage LONGBLOB, -- this stores the image in our system
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (courseID) REFERENCES Course(courseID) ON DELETE CASCADE,
    FOREIGN KEY (departmentID) REFERENCES Department(departmentID) ON DELETE CASCADE
);

-- Table for storing comments on posts
CREATE TABLE Comments (
    commentID INT PRIMARY KEY AUTO_INCREMENT,
    content TEXT NOT NULL,
    title TEXT NOT NULL,
    userID INT,
    postID INT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES Posts(postID) ON DELETE CASCADE
);

-- Table for mapping users to departments (many-to-many relationship)
CREATE TABLE UserDept (
    userID INT,
    departmentID INT,
    PRIMARY KEY (userID, departmentID),
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (departmentID) REFERENCES Department(departmentID) ON DELETE CASCADE
);

-- Table for mapping users to courses (many-to-many relationship)
CREATE TABLE UserCourse (
    userID INT,
    courseID INT,
    PRIMARY KEY (userID, courseID),
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (courseID) REFERENCES Course(courseID) ON DELETE CASCADE
);

-- Table for the likes a post has
CREATE TABLE Likes(
    userID INT,
    postID INT,
    PRIMARY KEY(userID, postID),
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES Posts(postID) ON DELETE CASCADE
);


INSERT INTO Users(username, firstname, lastname, email, userpassword, isAdmin) VALUES('admin', 'Admin', 'User', 'adminuser@gmail.com', 'b2d4310caf97cee4c7929241380aae57', TRUE);   -- password = thegoat@13

INSERT INTO Department (name, shorthand, description) 
VALUES 
	('Computer Science', 'COSC', 'We make the computer compute things'),
	('Data Science', 'DATA', 'We analyze and provide meaningful information about large data sets'),
	('Mathematics', 'MATH', 'We compute things ourselves'),
	('Statistics', 'STAT', 'We figure out the probability of things happening'),
	('Biology', 'BIOL', 'Something about blood, cells, animals, etc.'),
	('Chemistry', 'CHEM', 'How much benzene can we add to this before it goes boom?'),
	('Physics', 'PHYS', 'I can find out Kinematics, Velocity, Vectors etc.'),
	('Managemnet', 'MGMT', 'We manage things'),
	('Health and Exercise Science', 'HES', 'IDK, something about being in good shape.'),
	('Nursing', 'NRSG', 'Can I take your blood?'),
	('Engineering', 'ENGR', 'We bring solutions to real world problems');


