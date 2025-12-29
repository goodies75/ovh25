-- Script SQL pour créer la table comics
-- À exécuter une seule fois dans phpMyAdmin

CREATE TABLE IF NOT EXISTS `comics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `editeur` varchar(100) DEFAULT NULL,
  `collection` varchar(100) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `date_publication` date DEFAULT NULL,
  `prix_achat` decimal(10,2) DEFAULT NULL,
  `etat` enum('Neuf','Très bon','Bon','Moyen','Mauvais') DEFAULT NULL,
  `cote_actuelle` decimal(10,2) DEFAULT NULL,
  `notes` text,
  `image_url` varchar(500) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_titre` (`titre`),
  KEY `idx_editeur` (`editeur`),
  KEY `idx_collection` (`collection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données d'exemple (optionnel)
INSERT INTO `comics` (`titre`, `editeur`, `collection`, `numero`, `date_publication`, `prix_achat`, `etat`, `cote_actuelle`, `notes`) VALUES
('Spider-Man: Into the Spider-Verse', 'Marvel', 'Spider-Verse', 1, '2023-01-15', 15.99, 'Neuf', 18.50, 'Edition collector avec couverture brillante'),
('Batman: The Dark Knight Returns', 'DC Comics', 'Batman', 1, '2022-12-01', 12.99, 'Très bon', 25.00, 'Classique de Frank Miller, état impeccable');
