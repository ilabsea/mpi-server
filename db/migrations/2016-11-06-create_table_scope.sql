CREATE TABLE `scope` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `searchable_fields` text NOT NULL,
  `updatable_fields` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
