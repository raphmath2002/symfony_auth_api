CREATE TABLE IF NOT EXISTS `movie_category` (
    `id` bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
    `movie_id` bigint(20) UNSIGNED NOT NULL,
    `category_id` bigint(20) UNSIGNED NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `movie_category_movie_id_category_id_unique` (`movie_id`, `category_id`),
    KEY `movie_category_movie_id_foreign` (`movie_id`),
    KEY `movie_category_category_id_foreign` (`category_id`)
) ENGINE=InnoDB;