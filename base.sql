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

CREATE TABLE `proprietaire` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `nom` VARCHAR(100) NOT NULL, -- Local / Autres
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `operateur` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `nom` VARCHAR(100) NOT NULL, -- Yas, Orange, Airtel, ...
    `id_proprietaire` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_operateur_proprietaire`
        FOREIGN KEY (`id_proprietaire`) REFERENCES `proprietaire` (`id`)
        ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `prefixe` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `prefixe` VARCHAR(10) NOT NULL, -- ex: 32, 33, 34, 37, 38
    `id_operateur` INT UNSIGNED NOT NULL,
    `actif` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_prefixe` (`prefixe`),
    CONSTRAINT `fk_prefixe_operateur`
        FOREIGN KEY (`id_operateur`) REFERENCES `operateur` (`id`)
        ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `commission` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `id_operateur` INT UNSIGNED NOT NULL, -- operateur destinataire (non proprietaire)
    `pourcentage` DECIMAL(5,2) NOT NULL,
    `date_creation` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_commission_operateur`
        FOREIGN KEY (`id_operateur`) REFERENCES `operateur` (`id`)
        ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `operation` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `id_type` INT UNSIGNED NOT NULL,
    `id_user_source` INT,
    `id_user_destination` INT,
    `id_operateur` INT UNSIGNED, -- operateur du destinataire quand celui-ci n'a pas de compte MVola (transfert externe)
    `montant` DECIMAL(10,2) NOT NULL,
    `frais` DECIMAL(10,2) DEFAULT 0.00,
    `pourcentage_commission` DECIMAL(5,2) DEFAULT 0.00,
    `statut` ENUM('VALIDE', 'ECHEC') NOT NULL DEFAULT 'VALIDE',
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
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT `fk_operation_operateur`
        FOREIGN KEY (`id_operateur`) REFERENCES `operateur` (`id`)
        ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bareme_frais` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `id_type` INT UNSIGNED NOT NULL DEFAULT 3,
    `montant_min` DECIMAL(10,2) NOT NULL,
    `montant_max` DECIMAL(10,2) NOT NULL,
    `frais` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_bareme_frais_type`
        FOREIGN KEY (`id_type`) REFERENCES `type` (`id`)
        ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
=======
CREATE TABLE bareme_frais (
    id INT UNSIGNED AUTO_INCREMENT,
    montant_min DECIMAL(10,2) NOT NULL,
    montant_max DECIMAL(10,2) NOT NULL,
    frais DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (`id`),
    FOREIGN KEY (id_type) REFERENCES type(id),
);

CREATE TABLE operateur (
    id INT UNSIGNED AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    id_proprietaire INT UNSIGNED,
    PRIMARY KEY (`id`),
    FOREIGN KEY (id_proprietaire) REFERENCES proprietaire(id)
);

CREATE TABLE prefixe (
    id INT UNSIGNED AUTO_INCREMENT,
    prefixe VARCHAR(10) NOT NULL,
    id_operateur INT UNSIGNED,
    PRIMARY KEY (`id`),
    FOREIGN KEY (id_operateur) REFERENCES operateur(id)
);

CREATE TABLE commission (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `pourcentage` DECIMAL(5,2) NOT NULL,
    `date_creation` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE proprietaire (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `nom` VARCHAR(100) NOT NULL, -- local / autres
    PRIMARY KEY (`id`)
);
>>>>>>> 1dbd47d96fe7d5ce98919a676fb09f4b351c662a
