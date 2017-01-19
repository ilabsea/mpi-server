CREATE TABLE IF NOT EXISTS `field_log` (
`id` int(11) NOT NULL,
  `field_name` char(50) NOT NULL,
  `field_code` char(50) NOT NULL,
  `modified_at` datetime NOT NULL,
  `modified_by` char(50) NOT NULL,
  `modified_fields` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
