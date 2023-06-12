USE A
GO

CREATE TABLE Users ( 
    id int IDENTITY PRIMARY KEY, 
    login character varying(50) NOT NULL, 
    password character varying(250) NOT NULL, 
    username character varying(250) NOT NULL,  
    isdirector bit ); 
CREATE TABLE Place ( 
    id int IDENTITY PRIMARY KEY, 
    name character varying(250) NOT NULL, 
    template text NOT NULL, 
    address character varying(250) NOT NULL); 
CREATE TABLE Expo ( 
    id int IDENTITY PRIMARY KEY, 
    title character varying(250) NOT NULL, 
    startday date NOT NULL, 
    endday date NOT NULL,  
    place_id integer NOT NULL REFERENCES Place (id) ON DELETE CASCADE ON UPDATE CASCADE, 
    priceperm2 integer NOT NULL, 
      regfee integer, 
    CHECK (endday > startday)); 
CREATE TABLE GrantManagers ( 
    id int IDENTITY PRIMARY KEY, 
    user_id integer NOT NULL REFERENCES Users (id) ON DELETE CASCADE ON UPDATE CASCADE,  
    expo_id integer NOT NULL REFERENCES Expo (id) ON DELETE CASCADE ON UPDATE CASCADE ); 
 
CREATE TABLE Company( 
    id int IDENTITY PRIMARY KEY, 
    companyname character varying(150) NOT NULL, 
    legalname character varying(150) NOT NULL, 
    legaladdress character varying(300) NOT NULL,  
    rnokpp character varying(10) NOT NULL,  
    edrpou character varying(8),  
    mfo character varying(6), 
    ipn character varying(10), 
    correspontentacc character varying(20),   
    paymentacc character varying(20),  
    bank character varying(100),  
    address character varying(300),  
    chiefpost character varying(50),  
    chiefdoc character varying(50),  
    chieffullname character varying(100) NOT NULL,  
    chiefphone character varying(15) NOT NULL,  
    chiefmail character varying(100),  
    site character varying(250)); 
 
CREATE TABLE Stand( 
    id int IDENTITY PRIMARY KEY, 
    expo_id integer NOT NULL REFERENCES Expo (id) ON DELETE CASCADE ON UPDATE CASCADE,  
    company_id integer NOT NULL REFERENCES Company (id) ON DELETE CASCADE ON UPDATE CASCADE,  
    contactfullname character varying(100) NOT NULL, 
    contactphone character varying(15) NOT NULL, 
    contactemail character varying(100), 
    coord_x float NOT NULL, 
    coord_y float NOT NULL, 
    width integer NOT NULL CHECK (width > 0), 
    height integer NOT NULL CHECK (height > 0), 
    exponumber integer NOT NULL, 
    dateagreement date NOT NULL, 
    unique(expo_id, exponumber)); 
 
CREATE TABLE Mark( 
    id int IDENTITY PRIMARY KEY, 
    expo_id integer NOT NULL REFERENCES Expo (id) ON DELETE CASCADE ON UPDATE CASCADE, 
    info text, 
    decs character varying(250) ); 
 
CREATE TABLE Docs( id int IDENTITY PRIMARY KEY, 
    expo_id integer NOT NULL REFERENCES Expo (id) ON DELETE CASCADE ON UPDATE CASCADE,  
    company_id integer NOT NULL REFERENCES Company (id) ON DELETE CASCADE ON UPDATE CASCADE,  
    title character varying(100) NOT NULL, 
    url character varying(250) NOT NULL, 
    decs character varying(250));
