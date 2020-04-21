
CREATE TABLE `PUBLISHER` (
      `PUBLISHERID` int(11) NOT NULL,
      `PUBNAME` varchar(50) NOT NULL,
      `ADDRESS` varchar(50) NOT NULL,
      PRIMARY KEY (`PUBLISHERID`);
);

INSERT INTO `PUBLISHER` (`PUBLISHERID`, `PUBNAME`, `ADDRESS`) VALUES
(1, 'Hachette', 'California'),
(2, 'HarperCollins', 'Washington'),
(3, 'Macmillan', 'Oregon'),
(4, 'Penguin Random House', 'Arizona'),
(5, 'Simon & Schuster', 'New Jersey');



