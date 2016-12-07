CREATE TABLE `application` (
  `id` int(11) NOT NULL,
  `name` char(50) NOT NULL,
  `api_key` char(64) DEFAULT NULL,
  `api_secret` char(64) DEFAULT NULL,
  `scope_id` int(11) NOT NULL,
  `whitelist` varchar(250) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
