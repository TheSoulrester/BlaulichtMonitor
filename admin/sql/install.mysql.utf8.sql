-- =========================================
-- === Stammdaten-Tabellen (Lookup)      ===
-- =========================================

CREATE TABLE `#__blaulichtmonitor_alarmierungsarten` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title                VARCHAR(255) NOT NULL,
    image_url            VARCHAR(255) DEFAULT NULL,
    ordering             INT UNSIGNED DEFAULT NULL,
    created              DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by           INT,
    modified             DATETIME,
    modified_by          INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__blaulichtmonitor_einsatzarten` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title                VARCHAR(255) NOT NULL,
    colour_marker        VARCHAR(255) DEFAULT NULL,
    icon_url             VARCHAR(255) DEFAULT NULL,
    ordering             INT UNSIGNED DEFAULT NULL,
    created              DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by           INT,
    modified             DATETIME,
    modified_by          INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__blaulichtmonitor_einsatzkategorien` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title                VARCHAR(255) NOT NULL,
    icon_url             VARCHAR(255) DEFAULT NULL,
    ordering             INT UNSIGNED DEFAULT NULL,
    created              DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by           INT,
    modified             DATETIME,
    modified_by          INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__blaulichtmonitor_organisation` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name                 VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__blaulichtmonitor_einsatzleiter` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name                 VARCHAR(255) NOT NULL,
    ordering             INT UNSIGNED DEFAULT NULL,
    created              DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by           INT,
    modified             DATETIME,
    modified_by          INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

/*
-- === Tabelle: Dispo-Gruppen ===
CREATE TABLE `#__blaulichtmonitor_dispogruppen` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title                VARCHAR(255) NOT NULL,
    created              DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by           INT,
    modified             DATETIME,
    modified_by          INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
*/

-- =========================================
-- === Einheiten, Fahrzeuge, Einsatzort  ===
-- =========================================

CREATE TABLE `#__blaulichtmonitor_einheiten` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title                VARCHAR(255) NOT NULL,
    name                 VARCHAR(255) DEFAULT NULL,
    url                  VARCHAR(1024) DEFAULT NULL,
    beschreibung         TEXT DEFAULT NULL,
    standort_title       VARCHAR(255) DEFAULT NULL,
    standort_strasse     VARCHAR(255) DEFAULT NULL,
    standort_hausnummer  INT UNSIGNED DEFAULT NULL,
    standort_plz         VARCHAR(10) DEFAULT NULL,
    standort_ort         VARCHAR(255) DEFAULT NULL,
--	dispogruppe_id       INT UNSIGNED,
    organisation_id      INT UNSIGNED DEFAULT NULL,
    ordering             INT UNSIGNED DEFAULT NULL,
    created              DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by           INT,
    modified             DATETIME,
    modified_by          INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__blaulichtmonitor_fahrzeuge` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    einheit_id           INT UNSIGNED DEFAULT NULL,
    funkrufname          VARCHAR(100) NOT NULL,
    beschreibung         TEXT DEFAULT NULL,
    bild_url             VARCHAR(255) DEFAULT NULL,
    url                  VARCHAR(1024) DEFAULT NULL,
    in_dienst            TINYINT DEFAULT 1 NOT NULL,
    ordering             INT UNSIGNED DEFAULT NULL,
    created              DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by           INT,
    modified             DATETIME,
    modified_by          INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__blaulichtmonitor_einsatzorte` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    strasse              VARCHAR(255) NOT NULL,
    hausnummer           INT UNSIGNED DEFAULT NULL,
    plz                  VARCHAR(10) DEFAULT NULL,
    stadt                VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- === Haupttabelle: Einsätze            ===
-- =========================================

CREATE TABLE `#__blaulichtmonitor_einsatzberichte` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    alarmierungsart_id   INT UNSIGNED DEFAULT NULL,
    einsatzart_id        INT UNSIGNED DEFAULT NULL,
    einsatzkategorie_id  INT UNSIGNED DEFAULT NULL,
    einsatzkurzbericht   VARCHAR(255) DEFAULT NULL,
    einsatzleiter_id     INT UNSIGNED DEFAULT NULL,
    article_id           INT UNSIGNED DEFAULT NULL,
    prioritaet           TINYINT DEFAULT NULL,
    einsatzort_strasse   TEXT DEFAULT NULL,
    alarmierungszeit     DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    ausrueckzeit         DATETIME DEFAULT NULL,
    einsatzende          DATETIME DEFAULT NULL,
    people_count         INT UNSIGNED DEFAULT NULL,
    beschreibung         TEXT DEFAULT NULL,
    published            TINYINT DEFAULT 0,
    counter_clicks       INT UNSIGNED DEFAULT 0,
    created              DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by           INT,
    modified             DATETIME,
    modified_by          INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

/*
-- === Tabelle: Kurzberichte (Mehrfach verwendbar) ===
CREATE TABLE `#__blaulichtmonitor_kurzbericht` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    beschreibung         TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
*/

-- =========================================
-- === Zuordnungstabellen (Joins)        ===
-- =========================================

CREATE TABLE `#__blaulichtmonitor_einsatzberichte_einheiten` (
    einsatzbericht_id    INT UNSIGNED NOT NULL,
    einheit_id           INT UNSIGNED NOT NULL,
    CONSTRAINT `pk_#__blaulichtmonitor_einsatzberichte_einheiten` PRIMARY KEY (einsatzbericht_id, einheit_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__blaulichtmonitor_einsatzberichte_fahrzeuge` (
    einsatzbericht_id    INT UNSIGNED NOT NULL,
    fahrzeug_id          INT UNSIGNED NOT NULL,
    CONSTRAINT `pk_#__blaulichtmonitor_einsatzberichte_fahrzeuge` PRIMARY KEY (einsatzbericht_id, fahrzeug_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__blaulichtmonitor_einsatzleiter_einsatzbericht` (
    einsatzleiter_id     INT UNSIGNED NOT NULL,
    einsatzbericht_id    INT UNSIGNED NOT NULL,
    CONSTRAINT `pk_#__blaulichtmonitor_einsatzleiter_einsatzbericht` PRIMARY KEY (einsatzleiter_id, einsatzbericht_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

/*
-- === Join-Tabelle: Einsatz <-> Kurzbericht ===
CREATE TABLE `#__blaulichtmonitor_einsatz_kurzbericht` (
    einsatzbericht_id    INT UNSIGNED NOT NULL,
    kurzbeschreibung_id  INT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
*/

-- =========================================
-- === Medien & Presse                    ===
-- =========================================

CREATE TABLE `#__blaulichtmonitor_einsatzberichte_presse` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    einsatzbericht_id    INT UNSIGNED NOT NULL,
    url                  VARCHAR(1024) NOT NULL,
    title                VARCHAR(255) NOT NULL
--	quelle               VARCHAR(255),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__blaulichtmonitor_einsatzbilder` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    einsatzbericht_id    INT UNSIGNED NOT NULL,
    filename             VARCHAR(255) NOT NULL,
    thumbnail            VARCHAR(255) DEFAULT NULL,
    wasserzeichen        TINYINT DEFAULT 1,
    fotograf             VARCHAR(255) DEFAULT NULL,
    quelle               VARCHAR(255) DEFAULT NULL,
    created              DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by           INT,
    modified             DATETIME,
    modified_by          INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

/*
-- === Tabelle: Einsatzleiter-Zeiträume (Aktiv-Zeiten) ===
CREATE TABLE `#__blaulichtmonitor_einsatzleiter_zeitraum` (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    einsatzleiter_id     INT UNSIGNED NOT NULL,
    rang                 VARCHAR(255),
    von                  DATE NOT NULL,
    bis                  DATE,
    aktiv                TINYINT DEFAULT 0,
    created              DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by           INT,
    modified             DATETIME,
    modified_by          INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
*/

-- =========================================
-- === Fremdschlüssel (auskommentiert)   ===
-- =========================================

/*
-- Fremdschlüssel siehe Originaldatei, Reihenfolge ggf. anpassen nach obiger Sortierung.
*/
