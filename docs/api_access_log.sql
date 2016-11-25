CREATE TABLE `api_access_log` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `application_name` varchar(50) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `status` int(11) NOT NULL,
  `params` varchar(500) NOT NULL,
  `action` varchar(50) NOT NULL,
  `http_verb` char(10) NOT NULL,
  `url` varchar(2048) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
