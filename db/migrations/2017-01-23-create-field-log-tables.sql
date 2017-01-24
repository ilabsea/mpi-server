
CREATE TABLE `field_log` (
  `id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_code` varchar(50) CHARACTER SET utf8 NOT NULL,
  `field_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_at` datetime NOT NULL,
  `modified_attrs` text CHARACTER SET utf8 NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `field_log`
--
ALTER TABLE `field_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `field_log`
--
ALTER TABLE `field_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

ALTER TABLE `field_log` ADD `application_name` VARCHAR(30) NOT NULL AFTER `modified_by`;
ALTER TABLE `field_log` CHANGE `modified_by` `application_id` INT(11) NOT NULL;
