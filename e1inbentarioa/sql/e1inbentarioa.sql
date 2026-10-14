-- ============================================================
-- DATU-BASEAREN ESKEMA: Eskolako Inbentario Kudeatzailea
-- Motorra: MariaDB / MySQL
-- Erronka: Erronka 1 (e1inbentarioa)
-- ============================================================

-- Datu-basea sortu (dagoeneko existitzen ez bada)
CREATE DATABASE IF NOT EXISTS e1inbentarioa 
  DEFAULT CHARACTER SET utf8mb4 
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE e1inbentarioa;

-- Kanpo-gakoen egiaztapena aldi baterako desgaitu, taulak garbi sortzeko
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- 1. Taula: kategoriak (Ekipamendu mota orokorrak)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS kategoriak;
CREATE TABLE kategoriak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    izena VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 2. Taula: baliabideak (Baliabide profila / eredua)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS baliabideak;
CREATE TABLE baliabideak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategoriaId INT NOT NULL,
    izena VARCHAR(150) NOT NULL,
    deskribapena TEXT NULL,
    marka VARCHAR(100) NULL,
    modeloa VARCHAR(100) NULL,
    CONSTRAINT fk_baliabideak_kategoriak 
        FOREIGN KEY (kategoriaId) REFERENCES kategoriak(id) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 3. Taula: gailuak (Unitate fisikoak / inbentariatutako elementuak)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS gailuak;
CREATE TABLE gailuak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    baliabideId INT NOT NULL,
    etiketa VARCHAR(50) NOT NULL,
    erosketaData DATE NULL,
    egoera VARCHAR(50) DEFAULT 'Operatiboa',
    CONSTRAINT uq_gailuak_etiketa UNIQUE (etiketa),
    CONSTRAINT fk_gailuak_baliabideak 
        FOREIGN KEY (baliabideId) REFERENCES baliabideak(id) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 4. Taula: gelak (Ikasgelak edo espazio fisikoak)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS gelak;
CREATE TABLE gelak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    izena VARCHAR(100) NOT NULL,
    taldea VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 5. Taula: erabiltzaileak (Sistemaren erabiltzaileak)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS erabiltzaileak;
CREATE TABLE erabiltzaileak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    pasahitza VARCHAR(255) NOT NULL, -- Bcrypt bidez zifratutako pasahitzak gordetzeko luzera
    rol VARCHAR(30) NOT NULL DEFAULT 'teknikaria',
    CONSTRAINT uq_erabiltzaileak_username UNIQUE (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 6. Taula: mugimenduak (Kokapenen eta lekualdatzeen historiala)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS mugimenduak;
CREATE TABLE mugimenduak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gelaId INT NOT NULL,
    gailuId INT NOT NULL,
    erabiltzaileId INT NOT NULL,
    hasieraData DATE NOT NULL,
    amaieraData DATE NULL, -- NULL bada, gailua gela horretan dagoela adierazten du
    CONSTRAINT fk_mugimenduak_gelak 
        FOREIGN KEY (gelaId) REFERENCES gelak(id) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_mugimenduak_gailuak 
        FOREIGN KEY (gailuId) REFERENCES gailuak(id) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_mugimenduak_erabiltzaileak 
        FOREIGN KEY (erabiltzaileId) REFERENCES erabiltzaileak(id) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kanpo-gakoen egiaztapena berriro aktibatu
SET FOREIGN_KEY_CHECKS = 1;
