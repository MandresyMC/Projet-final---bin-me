CREATE TABLE `user` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `numero_telephone` VARCHAR(100) NOT NULL,
    `solde` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_numero_telephone` (`numero_telephone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `type` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `nom` VARCHAR(100) NOT NULL, -- depot, retrait, transfert
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `operation` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `id_type` INT UNSIGNED NOT NULL,
    `id_user_source` INT UNSIGNED NOT NULL,
    `id_user_destination` INT UNSIGNED NOT NULL,
    `montant` DECIMAL(10,2) NOT NULL,
    `frais` DECIMAL(10,2) DEFAULT 0.00,
    `date_creation` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_operation_type` 
        FOREIGN KEY (`id_type`) REFERENCES `type` (`id`) 
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk_operation_user` 
        FOREIGN KEY (`id_user_source`) REFERENCES `user` (`id`) 
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk_operation_user_destination` 
        FOREIGN KEY (`id_user_destination`) REFERENCES `user` (`id`) 
        ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
