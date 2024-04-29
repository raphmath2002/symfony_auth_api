CREATE TABLE IF NOT EXISTS movies (
    `id` bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
    `name` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `parution_date` date NOT NULL,
    `image_url` varchar(255) NULL,
    `rating` smallint NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `movies_name_unique` (`name`)
) ENGINE=InnoDB;