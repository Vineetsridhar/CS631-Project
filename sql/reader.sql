
CREATE TABLE IF NOT EXISTS `READER` (
      `READERID` int(11) NOT NULL,
      `RTYPE` varchar(30) NOT NULL,
      `RNAME` varchar(40) NOT NULL,
      `ADDRESS` varchar(50) DEFAULT NULL,
      `CARDNUM` varchar(10) NOT NULL
       PRIMARY KEY (`READERID`);
);


INSERT INTO `READER` (`READERID`, `RTYPE`, `RNAME`, `ADDRESS`, `CARDNUM`) VALUES
    (1, 'Student', 'Vineet Sridhar', 'Woodbridge', '123456789'),
    (2, 'Student', 'Samir Peshori', 'Oldbridge', '987654321'),
    (3, 'Student', 'Calin Blauth', 'Pennsylvania', '000000000');


