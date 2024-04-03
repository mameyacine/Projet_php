-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mer. 03 avr. 2024 à 09:27
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projet_php`
--

-- --------------------------------------------------------

--
-- Structure de la table `Admin`
--

CREATE TABLE `Admin` (
  `ID_Admin` int(11) NOT NULL,
  `ID_utilisateur` int(11) NOT NULL,
  `nom_utilisateur` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Admin`
--

INSERT INTO `Admin` (`ID_Admin`, `ID_utilisateur`, `nom_utilisateur`) VALUES
(1, 4, 'admin1');

-- --------------------------------------------------------

--
-- Structure de la table `Client`
--

CREATE TABLE `Client` (
  `ID_client` int(11) NOT NULL,
  `ID_utilisateur` int(11) DEFAULT NULL,
  `nom_utilisateur` varchar(255) DEFAULT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `nom_rue` varchar(20) DEFAULT NULL,
  `num_rue` varchar(20) DEFAULT NULL,
  `code_postal` varchar(20) DEFAULT NULL,
  `ville` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Client`
--

INSERT INTO `Client` (`ID_client`, `ID_utilisateur`, `nom_utilisateur`, `nom`, `prenom`, `telephone`, `nom_rue`, `num_rue`, `code_postal`, `ville`, `email`) VALUES
(11, 20, 'sophie', 'Moreau ', 'Sophie ', '06 56 78 90 10', ' Rue des Cerisiers', '90', 'Forêtville', '33000', 'sophie@gmail.com'),
(28, 34, 'aaaa', 'aaaa', 'aaaa', NULL, NULL, NULL, NULL, NULL, NULL),
(29, 35, 'Elvire', 'Youssou', 'Elvire', '12345678', 'tyuio', '13', '3456789', 'hhhh', 'elvire@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `Commentaire`
--

CREATE TABLE `Commentaire` (
  `ID_commentaire` int(11) NOT NULL,
  `ID_utilisateur` int(11) NOT NULL,
  `ID_intervention` int(11) NOT NULL,
  `date_heure` datetime DEFAULT NULL,
  `contenu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Commentaire`
--

INSERT INTO `Commentaire` (`ID_commentaire`, `ID_utilisateur`, `ID_intervention`, `date_heure`, `contenu`) VALUES
(11, 17, 12, '2024-03-19 11:39:02', 'Optimisez la production pour moins de gaspillage'),
(13, 20, 7, '2024-03-19 11:58:13', 'Bonjour'),
(14, 20, 12, '2024-03-19 11:58:45', 'Coucou'),
(15, 19, 7, '2024-03-19 12:00:34', 'Coucou'),
(26, 17, 33, '2024-04-02 18:06:12', 'b hbunilk,'),
(37, 17, 33, '2024-04-03 09:19:34', 'jhugyftyguhiljb'),
(38, 19, 20, '2024-04-03 09:20:12', 'huyigtg\r\n');

-- --------------------------------------------------------

--
-- Structure de la table `Demande`
--

CREATE TABLE `Demande` (
  `ID_demande` int(11) NOT NULL,
  `ID_client` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `degre_urgence` enum('faible','moyen','élevé') DEFAULT NULL,
  `date_heure` datetime NOT NULL,
  `statut_demande` enum('en cours de validation','validée','refusée','') NOT NULL DEFAULT 'en cours de validation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Demande`
--

INSERT INTO `Demande` (`ID_demande`, `ID_client`, `description`, `degre_urgence`, `date_heure`, `statut_demande`) VALUES
(1, 11, 'zaefrgt', 'faible', '2024-03-07 09:11:00', 'validée'),
(2, 11, 'zaefrgt', 'faible', '2024-03-07 09:11:00', 'validée'),
(3, 11, 'uytr', 'faible', '2024-03-09 09:20:00', 'validée'),
(4, 11, 'io!èytufygyhkuj', 'faible', '2024-03-16 09:23:00', 'validée'),
(5, 11, 'f(rebut', 'faible', '2024-03-25 09:23:00', 'validée'),
(6, 11, '\"r\'etygh', 'faible', '2024-03-08 09:23:00', 'validée'),
(7, 11, 'é\"\'ertgfd', 'élevé', '2024-03-25 09:24:00', 'refusée'),
(8, 11, 'èytfgjhkn', 'moyen', '2024-03-16 09:26:00', 'validée'),
(9, 11, 'oiouytugh', 'faible', '2024-03-17 09:42:00', 'en cours de validation'),
(10, 11, 'oiouiytgyh', 'moyen', '2024-03-17 09:43:00', 'en cours de validation'),
(11, 11, 'kiuy!èhuj', 'élevé', '2024-03-17 09:43:00', 'en cours de validation'),
(16, 11, 'knhukgyfuyub', 'faible', '2024-05-25 09:20:00', 'en cours de validation'),
(17, 29, 'byvftcuygiuhoj', 'faible', '2024-04-19 09:22:00', 'en cours de validation'),
(18, 29, 'hugguggu', 'élevé', '2024-04-18 09:22:00', 'en cours de validation');

-- --------------------------------------------------------

--
-- Structure de la table `Intervenant`
--

CREATE TABLE `Intervenant` (
  `ID_intervenant` int(11) NOT NULL,
  `ID_utilisateur` int(11) NOT NULL,
  `nom_utilisateur` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Intervenant`
--

INSERT INTO `Intervenant` (`ID_intervenant`, `ID_utilisateur`, `nom_utilisateur`, `prenom`, `nom`, `email`, `telephone`) VALUES
(5, 19, 'lauraG', 'Laura', 'Garcia', NULL, NULL),
(6, 18, 'thomas', 'Thomas  ', 'Martin', NULL, NULL),
(13, 31, 'antoine', 'Antoine', 'Leroux', NULL, NULL),
(15, 32, 'yacine', 'Yacine', 'Ndiaye', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `Intervention`
--

CREATE TABLE `Intervention` (
  `ID_intervention` int(11) NOT NULL,
  `ID_client` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `ID_standardiste` int(11) DEFAULT NULL,
  `ID_intervenant` int(11) DEFAULT NULL,
  `date_heure` datetime DEFAULT NULL,
  `statut` enum('en attente','en cours','terminé') DEFAULT NULL,
  `degre_urgence` enum('Faible','Moyen','Elevé') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Intervention`
--

INSERT INTO `Intervention` (`ID_intervention`, `ID_client`, `description`, `ID_standardiste`, `ID_intervenant`, `date_heure`, `statut`, `degre_urgence`) VALUES
(7, 11, 'Audit Énergétique', NULL, 5, '2024-04-20 11:00:00', 'terminé', 'Faible'),
(9, 11, 'Optimisation de l\'Isolation Thermique ', NULL, 5, '2024-03-23 11:30:00', 'en cours', 'Moyen'),
(10, 11, 'Mise en Place de Systèmes de Gestion Énergétique', NULL, 5, '2024-03-29 11:25:00', 'en cours', 'Faible'),
(12, 11, 'Réduction des Déchets Énergétiques', 6, NULL, '2024-03-23 11:00:00', 'terminé', 'Moyen'),
(19, 11, 'zaefrgt', NULL, 5, '2024-03-07 09:11:00', 'en cours', 'Faible'),
(20, 11, 'uytr', NULL, 5, '2024-03-09 09:20:00', 'en cours', 'Faible'),
(21, 11, 'èytfgjhkn', NULL, 5, '2024-03-16 09:26:00', 'en cours', 'Faible'),
(24, 11, 'ytyfdguh', NULL, 5, '2024-03-24 09:00:00', 'en attente', 'Faible'),
(26, 11, 'hjbu', NULL, 5, '2024-03-24 09:00:00', 'en attente', 'Faible'),
(27, 11, 'ezefrde', NULL, 5, '2024-03-24 12:49:00', 'en attente', 'Faible'),
(28, 11, 'kjnnjk', NULL, 5, '2024-03-02 13:27:00', 'en attente', 'Faible'),
(33, 11, 'EcoBoost', 6, NULL, '2024-03-17 15:00:00', 'en cours', 'Faible'),
(41, 11, 'f(rebut', NULL, 5, '2024-03-25 09:23:00', 'en attente', 'Moyen');

-- --------------------------------------------------------

--
-- Structure de la table `Standardiste`
--

CREATE TABLE `Standardiste` (
  `ID_standardiste` int(11) NOT NULL,
  `ID_utilisateur` int(11) NOT NULL,
  `nom_utilisateur` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Standardiste`
--

INSERT INTO `Standardiste` (`ID_standardiste`, `ID_utilisateur`, `nom_utilisateur`, `prenom`, `nom`, `email`, `telephone`) VALUES
(5, 22, 'Lucas', 'Lucas', 'Fernandez', NULL, NULL),
(6, 17, 'emilie05', 'Emilie ', 'Dubois', NULL, NULL),
(7, 23, 'emma', 'Emma ', 'Lefebvre', NULL, NULL),
(8, 26, 'max', 'Maxime', 'Girard', NULL, NULL),
(26, 33, 'lamine', 'lamine', 'lamine', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `Utilisateur`
--

CREATE TABLE `Utilisateur` (
  `ID_utilisateur` int(11) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `nom_utilisateur` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `role` enum('admin','client','standardiste','intervenant') NOT NULL DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Utilisateur`
--

INSERT INTO `Utilisateur` (`ID_utilisateur`, `nom`, `prenom`, `nom_utilisateur`, `mdp`, `role`) VALUES
(4, NULL, NULL, 'admin1', '$2y$10$gmu4DHjC8ZKOIaj2ZOaaPeTmsHkzfdaTrd4LcSGiGRWqphfGk7D1.', 'admin'),
(17, 'Dubois', 'Emilie ', 'emilie05', '$2y$10$WKzpl4BfSLXaSVrH06eZXOO6qpaAy8MwieYGB9MBlIxWVj9rA68km', 'standardiste'),
(18, 'Martin', 'Thomas  ', 'thomas', '$2y$10$IyiGXpcWMU.9W0C/GHWOXO8Taa0kOrnBnzNICLIO/apOHM5.PHnia', 'intervenant'),
(19, 'Garcia', 'Laura', 'lauraG', '$2y$10$IVYzX46NnqzqpOsGN.e6IuziA257Aqg.qiS/rMXMI8z/RUG0185Ye', 'intervenant'),
(20, 'Moreau ', 'Sophie ', 'sophie', '$2y$10$IJpwaL2w9o58BSURx33y8eyP//6F8vBF/Ep1cqMEn8yhbOa50vnmm', 'client'),
(22, 'Fernandez', 'Lucas', 'Lucas', '$2y$10$yslsRFnroHwcAbcZy0h.K.mRy8gBRRGWUqiXCqq3Du8o5xA6c.CYK', 'standardiste'),
(23, 'Lefebvre', 'Emma ', 'emma', '$2y$10$NbnoC26ErL8LSd/jZ19fQOee0/AQyjrE1uCw6eh69GRC.b0PzzIPy', 'standardiste'),
(26, 'Girard', 'Maxime', 'max', '$2y$10$TIlW7TAQkgtePAQD/Ljr2ekE6pMbjp6hkkvbGJcPXxvz2IqQKY71C', 'standardiste'),
(31, 'Leroux', 'Antoine', 'antoine', '$2y$10$qqIv8C6Dvznm/.phiAG.SOmM23vBHpI4WCel9Tz25LbIy0KfL5T4S', 'intervenant'),
(32, 'Ndiaye', 'Yacine', 'yacine', '$2y$10$X/y2uN/9GtE6T.XYg84v4OnRHcRDJKiuxd8bF6c4sK3T33NMV25iK', 'intervenant'),
(33, 'lamine', 'lamine', 'lamine', '$2y$10$o3afghePGEMk9fx4f4XDCeUsFGoGvKnBH1ZdFCg2j0KTyTdt6.yR6', 'standardiste'),
(34, 'aaaa', 'aaaa', 'aaaa', '$2y$10$ncqN1WsKNVItJWSlrVZzqu6oCYVQUQizhHV2.NOZcBqdP/T4MLpee', 'client'),
(35, 'Youssou', 'Elvire', 'Elvire', '$2y$10$e/K9jmD9ganpp8Itjgc0RuUQjmo268xLbvns3VwR3zsqgfNgaVBwS', 'client');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `Admin`
--
ALTER TABLE `Admin`
  ADD PRIMARY KEY (`ID_Admin`),
  ADD KEY `ID_utilisateur` (`ID_utilisateur`);

--
-- Index pour la table `Client`
--
ALTER TABLE `Client`
  ADD PRIMARY KEY (`ID_client`),
  ADD KEY `ID_utilisateur` (`ID_utilisateur`);

--
-- Index pour la table `Commentaire`
--
ALTER TABLE `Commentaire`
  ADD PRIMARY KEY (`ID_commentaire`),
  ADD KEY `ID_utilisateur` (`ID_utilisateur`),
  ADD KEY `ID_intervention` (`ID_intervention`);

--
-- Index pour la table `Demande`
--
ALTER TABLE `Demande`
  ADD PRIMARY KEY (`ID_demande`),
  ADD KEY `ID_client` (`ID_client`);

--
-- Index pour la table `Intervenant`
--
ALTER TABLE `Intervenant`
  ADD PRIMARY KEY (`ID_intervenant`),
  ADD KEY `ID_utilisateur` (`ID_utilisateur`);

--
-- Index pour la table `Intervention`
--
ALTER TABLE `Intervention`
  ADD PRIMARY KEY (`ID_intervention`),
  ADD KEY `ID_standardiste` (`ID_standardiste`),
  ADD KEY `ID_intervenant` (`ID_intervenant`),
  ADD KEY `ID_client` (`ID_client`);

--
-- Index pour la table `Standardiste`
--
ALTER TABLE `Standardiste`
  ADD PRIMARY KEY (`ID_standardiste`),
  ADD KEY `ID_utilisateur` (`ID_utilisateur`);

--
-- Index pour la table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  ADD PRIMARY KEY (`ID_utilisateur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `Admin`
--
ALTER TABLE `Admin`
  MODIFY `ID_Admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `Client`
--
ALTER TABLE `Client`
  MODIFY `ID_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `Commentaire`
--
ALTER TABLE `Commentaire`
  MODIFY `ID_commentaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `Demande`
--
ALTER TABLE `Demande`
  MODIFY `ID_demande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `Intervenant`
--
ALTER TABLE `Intervenant`
  MODIFY `ID_intervenant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `Intervention`
--
ALTER TABLE `Intervention`
  MODIFY `ID_intervention` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT pour la table `Standardiste`
--
ALTER TABLE `Standardiste`
  MODIFY `ID_standardiste` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  MODIFY `ID_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `Admin`
--
ALTER TABLE `Admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`ID_utilisateur`) REFERENCES `Utilisateur` (`ID_utilisateur`);

--
-- Contraintes pour la table `Client`
--
ALTER TABLE `Client`
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`ID_utilisateur`) REFERENCES `Utilisateur` (`ID_utilisateur`);

--
-- Contraintes pour la table `Commentaire`
--
ALTER TABLE `Commentaire`
  ADD CONSTRAINT `commentaire_ibfk_1` FOREIGN KEY (`ID_utilisateur`) REFERENCES `Utilisateur` (`ID_utilisateur`),
  ADD CONSTRAINT `commentaire_ibfk_2` FOREIGN KEY (`ID_intervention`) REFERENCES `Intervention` (`ID_intervention`),
  ADD CONSTRAINT `commentaire_ibfk_3` FOREIGN KEY (`ID_utilisateur`) REFERENCES `Utilisateur` (`ID_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `commentaire_ibfk_4` FOREIGN KEY (`ID_intervention`) REFERENCES `Intervention` (`ID_intervention`) ON DELETE CASCADE;

--
-- Contraintes pour la table `Demande`
--
ALTER TABLE `Demande`
  ADD CONSTRAINT `demande_ibfk_1` FOREIGN KEY (`ID_client`) REFERENCES `Client` (`ID_client`);

--
-- Contraintes pour la table `Intervenant`
--
ALTER TABLE `Intervenant`
  ADD CONSTRAINT `intervenant_ibfk_1` FOREIGN KEY (`ID_utilisateur`) REFERENCES `Utilisateur` (`ID_utilisateur`);

--
-- Contraintes pour la table `Intervention`
--
ALTER TABLE `Intervention`
  ADD CONSTRAINT `intervention_ibfk_1` FOREIGN KEY (`ID_standardiste`) REFERENCES `Standardiste` (`ID_standardiste`),
  ADD CONSTRAINT `intervention_ibfk_2` FOREIGN KEY (`ID_intervenant`) REFERENCES `Intervenant` (`ID_intervenant`),
  ADD CONSTRAINT `intervention_ibfk_3` FOREIGN KEY (`ID_client`) REFERENCES `Client` (`ID_client`),
  ADD CONSTRAINT `intervention_ibfk_4` FOREIGN KEY (`ID_client`) REFERENCES `Client` (`ID_client`) ON DELETE CASCADE;

--
-- Contraintes pour la table `Standardiste`
--
ALTER TABLE `Standardiste`
  ADD CONSTRAINT `standardiste_ibfk_1` FOREIGN KEY (`ID_utilisateur`) REFERENCES `Utilisateur` (`ID_utilisateur`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
