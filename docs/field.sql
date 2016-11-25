CREATE TABLE `field` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` char(32) NOT NULL,
  `type` char(24) NOT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `soft_delete` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `dynamic_field` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
