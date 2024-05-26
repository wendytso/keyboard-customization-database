-- Drop Table Statements
DROP TABLE COLOR_DETERMINES_BRIGHTNESS;
DROP TABLE RATING_HAS_FEEDBACK;
DROP TABLE KEYCAP;
DROP TABLE SWITCHES_ATTACH_TO;
DROP TABLE BOARD_HAS_LAYOUT;
DROP TABLE ACCESSORY_INCLUDED_ON;
DROP TABLE CONFIGURES;
DROP TABLE LIGHTING_NORMALIZED;
DROP TABLE MADE_BY;
DROP TABLE GAMING;
DROP TABLE MECHANICAL;
DROP TABLE KEYBOARD_CONTAINS;
DROP TABLE PLACES_ORDER;
DROP TABLE USER_NORMALIZED;
DROP TABLE KEYBOARD_ASSEMBLER_NORMALIZED;
DROP TABLE COMPLETE_EXPERIENCE_DETERMINES_SALARY;
DROP TABLE EMAIL_DETERMINES_NAME;

-- CREATE TABLE Statements 
CREATE TABLE EMAIL_DETERMINES_NAME (
    Email VARCHAR(100) PRIMARY KEY,
    Name VARCHAR(100)
);

CREATE TABLE USER_NORMALIZED (
    UserID VARCHAR(100) PRIMARY KEY,
    Email VARCHAR(100),
    Experience_level VARCHAR(100) NOT NULL,
    FOREIGN KEY (Email) REFERENCES EMAIL_DETERMINES_NAME (Email) ON DELETE CASCADE
);

CREATE TABLE PLACES_ORDER (
    OrderID VARCHAR(100) PRIMARY KEY,
    UserID VARCHAR(100),
    TodayDate DATE,
    Total_cost REAL,
    FOREIGN KEY (UserID) REFERENCES USER_NORMALIZED(UserID) ON DELETE CASCADE
);

CREATE TABLE KEYBOARD_CONTAINS (
    KeyboardName VARCHAR(100) PRIMARY KEY,
    OrderID VARCHAR(100),
    KeyboardConnection VARCHAR(100),
    TodayDate DATE,
    FOREIGN KEY (OrderID) REFERENCES PLACES_ORDER(OrderID) ON DELETE CASCADE
);

CREATE TABLE MECHANICAL (
    KeyboardName VARCHAR(100) PRIMARY KEY,
    Customizable_actuation_force VARCHAR(100),
    FOREIGN KEY (KeyboardName) REFERENCES KEYBOARD_CONTAINS(KeyboardName) ON DELETE CASCADE
);

CREATE TABLE GAMING (
    KeyboardName VARCHAR(100) PRIMARY KEY,
    Gaming_software_integration VARCHAR(100),
    FOREIGN KEY (KeyboardName) REFERENCES KEYBOARD_CONTAINS(KeyboardName) ON DELETE CASCADE
);

CREATE TABLE COMPLETE_EXPERIENCE_DETERMINES_SALARY (
    Experience_level VARCHAR(100),
    Salary REAL NOT NULL,
    Number_of_completed_keyboards INTEGER,
    PRIMARY KEY (Experience_level, Number_of_completed_keyboards)
);

CREATE TABLE KEYBOARD_ASSEMBLER_NORMALIZED (
    AssemblerID INTEGER,
    Name VARCHAR(100),
    Experience_level VARCHAR(100),
    Salary REAL NOT NULL,
    Number_of_completed_keyboards INTEGER,
    PRIMARY KEY (AssemblerID),
    FOREIGN KEY (Experience_level, Number_of_completed_keyboards)
        REFERENCES COMPLETE_EXPERIENCE_DETERMINES_SALARY(Experience_level, Number_of_completed_keyboards) ON DELETE CASCADE
);

CREATE TABLE MADE_BY (
    AssemblerID INTEGER,
    KeyboardName VARCHAR(100),
    Date_of_completion DATE,
    PRIMARY KEY (AssemblerID, KeyboardName),
    FOREIGN KEY (AssemblerID) REFERENCES KEYBOARD_ASSEMBLER_NORMALIZED(AssemblerID) ON DELETE CASCADE,
    FOREIGN KEY (KeyboardName) REFERENCES KEYBOARD_CONTAINS(KeyboardName) ON DELETE CASCADE
);

CREATE TABLE RATING_HAS_FEEDBACK (
    RatingID VARCHAR(100) PRIMARY KEY,
    Rate INTEGER,
    KeyboardName VARCHAR(100),
    TodayDate DATE,
    FOREIGN KEY (KeyboardName) REFERENCES KEYBOARD_CONTAINS(KeyboardName) ON DELETE CASCADE
);

CREATE TABLE LIGHTING_NORMALIZED (
    LightingID INTEGER PRIMARY KEY,
    Price REAL,
    Color VARCHAR(100) NOT NULL,
    Effect VARCHAR(100) NOT NULL
);

CREATE TABLE COLOR_DETERMINES_BRIGHTNESS (
    Color VARCHAR(100),
    Brightness INTEGER,
    PRIMARY KEY (Color)
);

CREATE TABLE CONFIGURES (
    LightingID INTEGER,
    KeyboardName VARCHAR(100),
    PRIMARY KEY (LightingID, KeyboardName),
    FOREIGN KEY (LightingID) REFERENCES LIGHTING_NORMALIZED(LightingID) ON DELETE CASCADE,
    FOREIGN KEY (KeyboardName) REFERENCES KEYBOARD_CONTAINS(KeyboardName) ON DELETE CASCADE
);

CREATE TABLE ACCESSORY_INCLUDED_ON (
    AccessoryID VARCHAR(100),
    Price REAL,
    BoardType VARCHAR(100),
    KeyboardName VARCHAR(100),
    PRIMARY KEY (AccessoryID, KeyboardName),
    FOREIGN KEY (KeyboardName) REFERENCES KEYBOARD_CONTAINS(KeyboardName) ON DELETE CASCADE
);

CREATE TABLE BOARD_HAS_LAYOUT (
    BoardID INTEGER,
    Brand VARCHAR(100),
    Material VARCHAR(100),
    Price REAL,
    BoardSize REAL,
    BoardType VARCHAR(100),
    KeyboardName VARCHAR(100) UNIQUE,
    PRIMARY KEY (BoardID),
    FOREIGN KEY (KeyboardName) REFERENCES KEYBOARD_CONTAINS(KeyboardName) ON DELETE CASCADE
);

CREATE TABLE SWITCHES_ATTACH_TO (
    SwitchID INTEGER,
    Operating_force VARCHAR(100),
    Brand VARCHAR(100),
    BoardType VARCHAR(100),
    Price REAL,
    Lifespan INTEGER,
    BoardID INTEGER,
    KeyID VARCHAR(100),
    PRIMARY KEY (SwitchID),
    FOREIGN KEY (BoardID) REFERENCES BOARD_HAS_LAYOUT(BoardID) ON DELETE CASCADE
);

CREATE TABLE KEYCAP (
    KeyID VARCHAR(100) PRIMARY KEY,
    Price REAL,
    Material VARCHAR(100),
    Brand VARCHAR(100),
    SwitchID INTEGER UNIQUE,
    FOREIGN KEY (SwitchID) REFERENCES SWITCHES_ATTACH_TO(SwitchID) ON DELETE CASCADE
);


-- Insert Statements
insert into color_determines_brightness (color, brightness) values ('RGB', 200000);
insert into color_determines_brightness (color, brightness) values ('Backlight', 300);
insert into color_determines_brightness (color, brightness) values ('White', 294849);
insert into color_determines_brightness (color, brightness) values ('Purple', 55);
insert into color_determines_brightness (color, brightness) values ('Blue', 71);

insert into email_determines_name (email, name) values ('ambikamod@hotmail.com', 'Ambika Mod');
insert into email_determines_name (email, name) values ('leo@gmail.com', 'Leo Woodall');
insert into email_determines_name (email, name) values ('ellie@gmail.com', 'Eleanor Tomlinson');
insert into email_determines_name (email, name) values ('jon@ubc.com', 'Jonny Weldon');
insert into email_determines_name (email, name) values ('essiedavis@gmail.com', 'Essie Davis');
insert into email_determines_name (email, name) values ('minag@yahoo.com', 'Mina Gourdi');
insert into email_determines_name (email, name) values ('anniewang@gmail.com', 'Annie Wang');
insert into email_determines_name (email, name) values ('jessiej@gmail.com', 'Jessie Jay');
insert into email_determines_name (email, name) values ('lawrenceholstaff@ubc.com', 'Lawrence Holstaff');
insert into email_determines_name (email, name) values ('donolo@gmail.com', 'Donolo Miller');

insert into user_normalized (userId, email, experience_level) values ('user1', 'ambikamod@hotmail.com', 'Intermediate');
insert into user_normalized (userId, email, experience_level) values ('user2', 'leo@gmail.com', 'Advanced');
insert into user_normalized (userId, email, experience_level) values ('user3', 'ellie@gmail.com', 'Beginner');
insert into user_normalized (userId, email, experience_level) values ('user4', 'jon@ubc.com', 'Intermediate');
insert into user_normalized (userId, email, experience_level) values ('user5', 'essiedavis@gmail.com', 'Advanced');
insert into user_normalized (userId, email, experience_level) values ('user6', 'minag@yahoo.com', 'Beginner');
insert into user_normalized (userId, email, experience_level) values ('user7', 'anniewang@gmail.com', 'Beginner');
insert into user_normalized (userId, email, experience_level) values ('user8', 'jessiej@gmail.com', 'Advanced');
insert into user_normalized (userId, email, experience_level) values ('user9', 'lawrenceholstaff@ubc.com', 'Intermediate');
insert into user_normalized (userId, email, experience_level) values ('user10', 'donolo@gmail.com', 'Beginner');

insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order1', 'user1', TO_DATE('2024-03-19', 'YYYY-MM-DD'), 150.99);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order2', 'user2', TO_DATE('2024-03-18', 'YYYY-MM-DD'), 299.99);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order3', 'user3', TO_DATE('2024-03-17', 'YYYY-MM-DD'), 1900.99);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order4', 'user4', TO_DATE('2024-03-16', 'YYYY-MM-DD'), 75.50);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order5', 'user5', TO_DATE('2024-03-15', 'YYYY-MM-DD'), 500.00);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order6', 'user6', TO_DATE('2023-02-10', 'YYYY-MM-DD'), 650.99);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order7', 'user5', TO_DATE('2023-08-18', 'YYYY-MM-DD'), 82.45);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order8', 'user8', TO_DATE('2023-12-17', 'YYYY-MM-DD'), 365.87);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order9', 'user7', TO_DATE('2024-02-02', 'YYYY-MM-DD'), 64.40);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order10', 'user9', TO_DATE('2024-01-31', 'YYYY-MM-DD'), 200.00);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order11', 'user10', TO_DATE('2023-09-22', 'YYYY-MM-DD'), 119.62);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order12', 'user2', TO_DATE('2024-01-02', 'YYYY-MM-DD'), 88.88);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order13', 'user8', TO_DATE('2023-12-09', 'YYYY-MM-DD'), 1000.50);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order14', 'user9', TO_DATE('2024-04-01', 'YYYY-MM-DD'), 220.05);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order15', 'user3', TO_DATE('2024-04-02', 'YYYY-MM-DD'), 103.72);

insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order16', 'user3', TO_DATE('2024-01-31', 'YYYY-MM-DD'), 200.00);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order17', 'user3', TO_DATE('2023-09-22', 'YYYY-MM-DD'), 119.62);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order18', 'user3', TO_DATE('2024-01-02', 'YYYY-MM-DD'), 88.88);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order19', 'user8', TO_DATE('2023-12-09', 'YYYY-MM-DD'), 1000.50);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order20', 'user8', TO_DATE('2024-04-01', 'YYYY-MM-DD'), 220.05);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order21', 'user8', TO_DATE('2024-04-02', 'YYYY-MM-DD'), 103.72);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order22', 'user8', TO_DATE('2024-01-31', 'YYYY-MM-DD'), 200.00);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order23', 'user7', TO_DATE('2023-09-22', 'YYYY-MM-DD'), 119.62);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order24', 'user7', TO_DATE('2024-01-02', 'YYYY-MM-DD'), 88.88);
insert into places_order (OrderID, UserID, TodayDate, Total_cost) values ('order25', 'user7', TO_DATE('2023-12-09', 'YYYY-MM-DD'), 5000.50);


insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard1', 'order1', 'USB', TO_DATE('2024-03-19', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard2', 'order2', 'Wireless', TO_DATE('2024-03-18', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard3', 'order3', 'Bluetooth', TO_DATE('2024-03-17', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard4', 'order4', 'USB', TO_DATE('2024-03-16', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard5', 'order5', 'Wireless', TO_DATE('2024-03-15', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard6', 'order6', 'USB', TO_DATE('2023-02-10', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard7', 'order7', 'Wireless', TO_DATE('2023-08-18', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard8', 'order8', 'Bluetooth', TO_DATE('2023-12-17', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard9', 'order9', 'USB', TO_DATE('2024-02-02', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard10', 'order10', 'Wireless', TO_DATE('2024-01-31', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard11', 'order11', 'USB', TO_DATE('2023-09-22', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard12', 'order12', 'Bluetooth', TO_DATE('2024-01-02', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard13', 'order13', 'Bluetooth', TO_DATE('2023-12-09', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard14', 'order14', 'Wireless', TO_DATE('2024-04-01', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard15', 'order15', 'Wireless', TO_DATE('2024-04-02', 'YYYY-MM-DD'));

insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard16', 'order16', 'USB', TO_DATE('2023-02-10', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard17', 'order17', 'Wireless', TO_DATE('2023-08-18', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard18', 'order18', 'Bluetooth', TO_DATE('2023-12-17', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard19', 'order19', 'USB', TO_DATE('2024-02-02', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard20', 'order20', 'Wireless', TO_DATE('2024-01-31', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard21', 'order21', 'USB', TO_DATE('2023-09-22', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard22', 'order22', 'Bluetooth', TO_DATE('2024-01-02', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard23', 'order23', 'Bluetooth', TO_DATE('2023-12-09', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard24', 'order24', 'Wireless', TO_DATE('2024-04-01', 'YYYY-MM-DD'));
insert into keyboard_contains (KeyboardName, OrderID, KeyboardConnection, TodayDate) values ('keyboard25', 'order25', 'Wireless', TO_DATE('2024-04-02', 'YYYY-MM-DD'));

insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard1', '60g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard2', '65g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard3', '55g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard4', '70g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard5', '45g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard6', '60g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard7', '65g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard8', '55g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard9', '70g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard10', '45g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard11', '60g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard12', '65g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard13', '55g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard14', '70g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard15', '45g');

insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard16', '60g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard17', '65g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard18', '55g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard19', '70g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard20', '45g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard21', '60g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard22', '65g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard23', '55g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard24', '70g');
insert into mechanical (KeyboardName, Customizable_actuation_force) values ('keyboard25', '45g');

insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard1', 'Razer Synapse');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard2', 'Logitech G HUB');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard3', 'Corsair iCUE');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard4', 'SteelSeries Engine');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard5', 'ASUS Armoury Crate');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard6', 'Razer Synapse');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard7', 'Logitech G HUB');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard8', 'Corsair iCUE');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard9', 'SteelSeries Engine');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard10', 'ASUS Armoury Crate');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard11', 'Razer Synapse');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard12', 'Logitech G HUB');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard13', 'Corsair iCUE');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard14', 'SteelSeries Engine');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard15', 'ASUS Armoury Crate');

insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard16', 'Razer Synapse');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard17', 'Logitech G HUB');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard18', 'Corsair iCUE');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard19', 'SteelSeries Engine');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard20', 'ASUS Armoury Crate');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard21', 'Razer Synapse');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard22', 'Logitech G HUB');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard23', 'Corsair iCUE');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard24', 'SteelSeries Engine');
insert into gaming (KeyboardName, Gaming_software_integration) values ('keyboard25', 'ASUS Armoury Crate');

insert into complete_experience_determines_salary (Experience_level, Salary, Number_of_completed_keyboards) values ('Beginner', 800.00, 10);
insert into complete_experience_determines_salary (Experience_level, Salary, Number_of_completed_keyboards) values ('Intermediate', 2400.00, 20);
insert into complete_experience_determines_salary (Experience_level, Salary, Number_of_completed_keyboards) values ('Advanced', 3200.00, 30);
insert into complete_experience_determines_salary (Experience_level, Salary, Number_of_completed_keyboards) values ('Intermediate', 2500.00, 25);
insert into complete_experience_determines_salary (Experience_level, Salary, Number_of_completed_keyboards) values ('Advanced', 3600.00, 35);
insert into complete_experience_determines_salary (Experience_level, Salary, Number_of_completed_keyboards) values ('Beginner', 1800.00, 8);
insert into complete_experience_determines_salary (Experience_level, Salary, Number_of_completed_keyboards) values ('Intermediate', 3400.00, 24);
insert into complete_experience_determines_salary (Experience_level, Salary, Number_of_completed_keyboards) values ('Intermediate', 3200.00, 30);
insert into complete_experience_determines_salary (Experience_level, Salary, Number_of_completed_keyboards) values ('Beginner', 500.00, 5);
insert into complete_experience_determines_salary (Experience_level, Salary, Number_of_completed_keyboards) values ('Advanced', 4000.00, 50);


insert into keyboard_assembler_normalized (AssemblerID, Name, Experience_level, Salary, Number_of_completed_keyboards) values (1, 'Thelonius Ellison', 'Beginner', 800.00, 10);
insert into keyboard_assembler_normalized (AssemblerID, Name, Experience_level, Salary, Number_of_completed_keyboards) values (2, 'Sinatra Golden', 'Intermediate', 2400.00, 20);
insert into keyboard_assembler_normalized (AssemblerID, Name, Experience_level, Salary, Number_of_completed_keyboards) values (3, 'Carl Brunt', 'Advanced', 3200.00, 30);
insert into keyboard_assembler_normalized (AssemblerID, Name, Experience_level, Salary, Number_of_completed_keyboards) values (4, 'Aileen Hoover', 'Intermediate', 2500.00, 25);
insert into keyboard_assembler_normalized (AssemblerID, Name, Experience_level, Salary, Number_of_completed_keyboards) values (5, 'Coraline Alexander', 'Advanced', 3600.00, 35);
insert into keyboard_assembler_normalized (AssemblerID, Name, Experience_level, Salary, Number_of_completed_keyboards) values (6, 'George Mcyntire', 'Beginner', 1800.00, 8);
insert into keyboard_assembler_normalized (AssemblerID, Name, Experience_level, Salary, Number_of_completed_keyboards) values (7, 'Vivian Lee', 'Intermediate', 3400.00, 24);
insert into keyboard_assembler_normalized (AssemblerID, Name, Experience_level, Salary, Number_of_completed_keyboards) values (8, 'Carl Brunt', 'Advanced', 4000.00, 50);
insert into keyboard_assembler_normalized (AssemblerID, Name, Experience_level, Salary, Number_of_completed_keyboards) values (9, 'Aileen Hoover', 'Intermediate', 3100.00, 30);
insert into keyboard_assembler_normalized (AssemblerID, Name, Experience_level, Salary, Number_of_completed_keyboards) values (10, 'Roger Ted', 'Advanced', 3650.00, 30);

insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (1, 'keyboard1', TO_DATE('2024-03-19', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (2, 'keyboard2', TO_DATE('2024-03-18', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (3, 'keyboard3', TO_DATE('2024-03-17', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (4, 'keyboard4', TO_DATE('2024-03-20', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (5, 'keyboard5', TO_DATE('2024-03-25', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (1, 'keyboard6', TO_DATE('2024-02-26', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (2, 'keyboard7', TO_DATE('2023-09-10', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (3, 'keyboard8', TO_DATE('2023-12-23', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (4, 'keyboard9', TO_DATE('2024-02-19', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (5, 'keyboard10', TO_DATE('2024-03-07', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (1, 'keyboard11', TO_DATE('2023-10-11', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (2, 'keyboard12', TO_DATE('2024-01-17', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (3, 'keyboard13', TO_DATE('2023-12-17', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (4, 'keyboard14', TO_DATE('2024-04-13', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (5, 'keyboard15', TO_DATE('2024-04-13', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (1, 'keyboard16', TO_DATE('2024-02-26', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (2, 'keyboard17', TO_DATE('2023-09-10', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (3, 'keyboard18', TO_DATE('2023-12-23', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (4, 'keyboard19', TO_DATE('2024-02-19', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (5, 'keyboard20', TO_DATE('2024-03-07', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (9, 'keyboard21', TO_DATE('2023-10-11', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (2, 'keyboard22', TO_DATE('2024-01-17', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (7, 'keyboard23', TO_DATE('2023-12-17', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (4, 'keyboard24', TO_DATE('2024-04-13', 'YYYY-MM-DD'));
insert into made_by (AssemblerID, KeyboardName, Date_of_completion) values (6, 'keyboard25', TO_DATE('2024-04-13', 'YYYY-MM-DD'));


insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating1', 9, 'keyboard1', TO_DATE('2024-03-19', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating2', 8, 'keyboard2', TO_DATE('2024-03-18', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating3', 1, 'keyboard3', TO_DATE('2024-03-17', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating4', 2, 'keyboard4', TO_DATE('2024-03-16', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating5', 3, 'keyboard5', TO_DATE('2024-03-15', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating6', 8, 'keyboard6', TO_DATE('2023-02-10', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating7', 9, 'keyboard7', TO_DATE('2023-08-18', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating8', 10, 'keyboard8', TO_DATE('2023-12-17', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating9', 4, 'keyboard9', TO_DATE('2024-02-02', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating10', 5, 'keyboard10', TO_DATE('2024-01-31', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating11', 6, 'keyboard11', TO_DATE('2023-09-22', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating12', 7, 'keyboard12', TO_DATE('2024-01-02', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating13', 8, 'keyboard13', TO_DATE('2023-12-09', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating14', 3, 'keyboard14', TO_DATE('2024-04-01', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating15', 2, 'keyboard15', TO_DATE('2024-04-02', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating16', 2, 'keyboard16', TO_DATE('2023-02-10', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating17', 4, 'keyboard17', TO_DATE('2023-08-18', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating18', 9, 'keyboard18', TO_DATE('2023-12-17', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating19', 7, 'keyboard19', TO_DATE('2024-02-02', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating20', 6, 'keyboard20', TO_DATE('2024-01-31', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating21', 5, 'keyboard21', TO_DATE('2023-09-22', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating22', 5, 'keyboard22', TO_DATE('2024-01-02', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating23', 8, 'keyboard23', TO_DATE('2023-12-09', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating24', 8, 'keyboard24', TO_DATE('2024-04-01', 'YYYY-MM-DD'));
insert into rating_has_feedback (RatingID, Rate, KeyboardName, TodayDate) values ('rating25', 4, 'keyboard25', TO_DATE('2024-04-02', 'YYYY-MM-DD'));

insert into lighting_normalized (LightingID, Price, Color, Effect) values (1, 49.99, 'RGB', 'Wave');
insert into lighting_normalized (LightingID, Price, Color, Effect) values (2, 29.99, 'White', 'Wave');
insert into lighting_normalized (LightingID, Price, Color, Effect) values (3, 39.99, 'Blue', 'Wave');
insert into lighting_normalized (LightingID, Price, Color, Effect) values (4, 19.99, 'Purple', 'Wave');
insert into lighting_normalized (LightingID, Price, Color, Effect) values (5, 59.99, 'RGB', 'Rainbow');
insert into lighting_normalized (LightingID, Price, Color, Effect) values (6, 49.99, 'RGB', 'Breathing');
insert into lighting_normalized (LightingID, Price, Color, Effect) values (7, 29.99, 'White', 'Cycle');
insert into lighting_normalized (LightingID, Price, Color, Effect) values (8, 39.99, 'Blue', 'Cycle');
insert into lighting_normalized (LightingID, Price, Color, Effect) values (9, 19.99, 'Purple', 'Cycle');


insert into configures (LightingID, KeyboardName) values (1, 'keyboard1');
insert into configures (LightingID, KeyboardName) values (2, 'keyboard2');
insert into configures (LightingID, KeyboardName) values (3, 'keyboard3');
insert into configures (LightingID, KeyboardName) values (4, 'keyboard4');
insert into configures (LightingID, KeyboardName) values (5, 'keyboard5');
insert into configures (LightingID, KeyboardName) values (6, 'keyboard6');
insert into configures (LightingID, KeyboardName) values (7, 'keyboard7');
insert into configures (LightingID, KeyboardName) values (8, 'keyboard8');
insert into configures (LightingID, KeyboardName) values (9, 'keyboard9');
insert into configures (LightingID, KeyboardName) values (1, 'keyboard10');
insert into configures (LightingID, KeyboardName) values (1, 'keyboard11');
insert into configures (LightingID, KeyboardName) values (2, 'keyboard12');
insert into configures (LightingID, KeyboardName) values (3, 'keyboard13');
insert into configures (LightingID, KeyboardName) values (4, 'keyboard14');
insert into configures (LightingID, KeyboardName) values (1, 'keyboard15');
insert into configures (LightingID, KeyboardName) values (2, 'keyboard16');
insert into configures (LightingID, KeyboardName) values (3, 'keyboard17');
insert into configures (LightingID, KeyboardName) values (4, 'keyboard18');
insert into configures (LightingID, KeyboardName) values (1, 'keyboard19');
insert into configures (LightingID, KeyboardName) values (2, 'keyboard20');
insert into configures (LightingID, KeyboardName) values (3, 'keyboard21');
insert into configures (LightingID, KeyboardName) values (4, 'keyboard22');
insert into configures (LightingID, KeyboardName) values (7, 'keyboard23');
insert into configures (LightingID, KeyboardName) values (8, 'keyboard24');
insert into configures (LightingID, KeyboardName) values (9, 'keyboard25');

insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc1', 19.99, 'Full-size', 'keyboard1');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc2', 14.99, 'Tenkeyless', 'keyboard2');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc3', 9.99, 'Compact', 'keyboard3');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc4', 24.99, 'Full-size', 'keyboard4');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc5', 29.99, 'Tenkeyless', 'keyboard5');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc6', 19.99, 'Full-size', 'keyboard6');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc7', 14.99, 'Tenkeyless', 'keyboard7');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc8', 9.99, 'Compact', 'keyboard8');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc9', 24.99, 'Full-size', 'keyboard9');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc10', 29.99, 'Tenkeyless', 'keyboard10');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc11', 19.99, 'Full-size', 'keyboard11');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc12', 14.99, 'Tenkeyless', 'keyboard12');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc13', 9.99, 'Compact', 'keyboard13');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc14', 24.99, 'Full-size', 'keyboard14');
insert into accessory_included_on (AccessoryID, Price, BoardType, KeyboardName) values ('acc15', 29.99, 'Tenkeyless', 'keyboard15');

insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (1, 'Logitech', 'Plastic', 99.99, 17.3, 'Full-size', 'keyboard1');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (2, 'Corsair', 'Aluminum', 149.99, 15.6, 'Tenkeyless', 'keyboard2');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (3, 'Razer', 'Plastic', 79.99, 14.0, 'Compact', 'keyboard3');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (4, 'SteelSeries', 'Aluminum', 129.99, 17.3, 'Full-size', 'keyboard4');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (5, 'Ducky', 'Plastic', 109.99, 15.6, 'Tenkeyless', 'keyboard5');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (6, 'Logitech', 'Plastic', 99.99, 17.3, 'Full-size', 'keyboard6');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (7, 'Corsair', 'Aluminum', 149.99, 15.6, 'Tenkeyless', 'keyboard7');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (8, 'Razer', 'Plastic', 79.99, 14.0, 'Compact', 'keyboard8');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (9, 'SteelSeries', 'Aluminum', 129.99, 17.3, 'Full-size', 'keyboard9');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (10, 'Ducky', 'Plastic', 109.99, 15.6, 'Tenkeyless', 'keyboard10');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (11, 'Logitech', 'Plastic', 99.99, 17.3, 'Full-size', 'keyboard11');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (12, 'Corsair', 'Aluminum', 149.99, 15.6, 'Tenkeyless', 'keyboard12');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (13, 'Razer', 'Plastic', 79.99, 14.0, 'Compact', 'keyboard13');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (14, 'SteelSeries', 'Aluminum', 129.99, 17.3, 'Full-size', 'keyboard14');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (15, 'Ducky', 'Plastic', 109.99, 15.6, 'Tenkeyless', 'keyboard15');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (16, 'Logitech', 'Plastic', 99.99, 17.3, 'Full-size', 'keyboard16');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (17, 'Corsair', 'Aluminum', 149.99, 15.6, 'Tenkeyless', 'keyboard17');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (18, 'Razer', 'Plastic', 79.99, 14.0, 'Compact', 'keyboard18');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (19, 'SteelSeries', 'Aluminum', 129.99, 17.3, 'Full-size', 'keyboard19');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (20, 'Ducky', 'Plastic', 109.99, 15.6, 'Tenkeyless', 'keyboard20');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (21, 'Logitech', 'Plastic', 99.99, 17.3, 'Full-size', 'keyboard21');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (22, 'Corsair', 'Aluminum', 149.99, 15.6, 'Tenkeyless', 'keyboard22');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (23, 'Razer', 'Plastic', 79.99, 14.0, 'Compact', 'keyboard23');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (24, 'SteelSeries', 'Aluminum', 129.99, 17.3, 'Full-size', 'keyboard24');
insert into board_has_layout (BoardID, Brand, Material, Price, BoardSize, BoardType, KeyboardName) values (25, 'Ducky', 'Plastic', 109.99, 15.6, 'Tenkeyless', 'keyboard25');

insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (1, '50g', 'Cherry', 'Full-size', 5.99, 5000000, 1, 'key1');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (2, '60g', 'Razer', 'Tenkeyless', 6.99, 6000000, 2, 'key2');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (3, '70g', 'Logitech', 'Compact', 7.99, 7000000, 3, 'key3');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (4, '80g', 'SteelSeries', 'Full-size', 8.99, 8000000, 4, 'key4');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (5, '90g', 'Ducky', 'Tenkeyless', 9.99, 9000000, 5, 'key5');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (6, '50g', 'Cherry', 'Full-size', 5.99, 5000000, 6, 'key6');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (7, '60g', 'Razer', 'Tenkeyless', 6.99, 6000000, 7, 'key7');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (8, '70g', 'Logitech', 'Compact', 7.99, 7000000, 8, 'key8');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (9, '80g', 'SteelSeries', 'Full-size', 8.99, 8000000, 9, 'key9');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (10, '90g', 'Ducky', 'Tenkeyless', 9.99, 9000000, 10, 'key10');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (11, '50g', 'Cherry', 'Full-size', 5.99, 5000000, 11, 'key11');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (12, '60g', 'Razer', 'Tenkeyless', 6.99, 6000000, 12, 'key12');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (13, '70g', 'Logitech', 'Compact', 7.99, 7000000, 13, 'key13');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (14, '80g', 'SteelSeries', 'Full-size', 8.99, 8000000, 14, 'key14');
insert into switches_attach_to (SwitchID, Operating_force, Brand, BoardType, Price, Lifespan, BoardID, KeyID) values (15, '90g', 'Ducky', 'Tenkeyless', 9.99, 9000000, 15, 'key15');

insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap1', 2.99, 'ABS', 'Generic', 1);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap2', 3.99, 'PBT', 'Corsair', 2);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap3', 4.99, 'ABS', 'Razer', 3);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap4', 5.99, 'PBT', 'SteelSeries', 4);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap5', 6.99, 'ABS', 'Ducky', 5);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap6', 2.99, 'ABS', 'Generic', 6);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap7', 3.99, 'PBT', 'Generic', 7);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap8', 4.99, 'ABS', 'Razer', 8);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap9', 5.99, 'PBT', 'Ducky', 9);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap10', 6.99, 'ABS', 'Ducky', 10);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap11', 2.99, 'ABS', 'Generic', 11);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap12', 3.99, 'PBT', 'Ducky', 12);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap13', 4.99, 'ABS', 'Razer', 13);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap14', 5.99, 'PBT', 'Generic', 14);
insert into keycap (KeyID, Price, Material, Brand, SwitchID) values ('keycap15', 6.99, 'ABS', 'Ducky', 15);

commit;