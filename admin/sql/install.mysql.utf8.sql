-- === Tabelle: Alarmierungsarten ===
CREATE  TABLE `#__blaulichtmonitor_alarmierungsarten` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	title          		 VARCHAR(255)    NOT NULL   ,
	created              DATETIME  DEFAULT (CURRENT_TIMESTAMP)     ,
	created_by           INT UNSIGNED      ,
	modified             DATETIME       ,
	modified_by          INT UNSIGNED
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Dispo-Gruppen ===
CREATE  TABLE `#__blaulichtmonitor_dispogruppen` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	title          		 VARCHAR(255)    NOT NULL   ,
	created              DATETIME  DEFAULT (CURRENT_TIMESTAMP)     ,
	created_by           INT UNSIGNED      ,
	modified             DATETIME       ,
	modified_by          INT UNSIGNED
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Einsatzarten ===
CREATE  TABLE `#__blaulichtmonitor_einsatzarten` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	title         		 VARCHAR(255)    NOT NULL   ,
	created              DATETIME  DEFAULT (CURRENT_TIMESTAMP)     ,
	created_by           INT      ,
	modified             DATETIME       ,
	modified_by          INT UNSIGNED
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Einsatzkategorien ===
CREATE  TABLE `#__blaulichtmonitor_einsatzkategorien` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	title      		     VARCHAR(255)    NOT NULL   ,
	created              DATETIME  DEFAULT (CURRENT_TIMESTAMP)     ,
	created_by           INT UNSIGNED      ,
	modified             DATETIME       ,
	modified_by          INT UNSIGNED
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Einsatzleiter ===
CREATE  TABLE `#__blaulichtmonitor_einsatzleiter` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	name                 VARCHAR(255)    NOT NULL   ,
	created              DATETIME  DEFAULT (CURRENT_TIMESTAMP)     ,
	created_by           INT UNSIGNED      ,
	modified             DATETIME       ,
	modified_by          INT UNSIGNED
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Einsatzleiter-Zeiträume (Aktiv-Zeiten) ===
CREATE  TABLE `#__blaulichtmonitor_einsatzleiter_zeitraum` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	einsatzleiter_id     INT UNSIGNED   NOT NULL   ,
	rang                 VARCHAR(255)       ,
	von                  DATE    NOT NULL   ,
	bis                  DATE       ,
	aktiv                TINYINT  DEFAULT (0)     ,
	created              DATETIME  DEFAULT (CURRENT_TIMESTAMP)     ,
	created_by           INT UNSIGNED      ,
	modified             DATETIME       ,
	modified_by          INT UNSIGNED
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Einsatzort (nur Straße/PLZ/Stadt) ===
CREATE  TABLE `#__blaulichtmonitor_einsatzort` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	strasse              TEXT    NOT NULL   ,
	plz                  INT    NOT NULL   ,
	stadt                TEXT    NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Kurzberichte (Mehrfach verwendbar) ===
CREATE  TABLE `#__blaulichtmonitor_kurzbericht` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	beschreibung         TEXT    NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Organisationen (z. B. Feuerwehr Stadt XY) ===
CREATE  TABLE `#__blaulichtmonitor_organisation` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	name                 VARCHAR(100)    NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Einheiten ===
CREATE  TABLE `#__blaulichtmonitor_einheiten` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	name                 VARCHAR(255)    NOT NULL   ,
	standort_title 		 VARCHAR(255)       ,
	standort_strasse     VARCHAR(255)       ,
	standort_plz         VARCHAR(10)       ,
	standort_ort         VARCHAR(255)       ,
	dispogruppe_id       INT UNSIGNED      ,
    organisation_id      INT UNSIGNED      ,
	created              DATETIME  DEFAULT (CURRENT_TIMESTAMP)     ,
	created_by           INT UNSIGNED      ,
	modified             DATETIME       ,
	modified_by          INT UNSIGNED
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Einsätze (Haupttabelle) ===
CREATE  TABLE `#__blaulichtmonitor_einsaetze` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	alarmierungsart_id   INT UNSIGNED   NOT NULL   ,
	einsatzart_id        INT UNSIGNED   NOT NULL   ,
	einsatzkategorie_id  INT UNSIGNED   NOT NULL   ,
	article_id           INT UNSIGNED DEFAULT (NULL)     ,
	prioritaet           TINYINT       ,
	einsatzort_id        INT UNSIGNED       ,
	alarmierungszeit     DATETIME  DEFAULT (CURRENT_TIMESTAMP)  NOT NULL   ,
	ausrueckzeit         DATETIME       ,
	einsatzende          DATETIME       ,
	beschreibung         TEXT       ,
	veroeffentlicht      TINYINT  DEFAULT (0)     ,
	counter              INT UNSIGNED DEFAULT (0)     ,
	created              DATETIME  DEFAULT (CURRENT_TIMESTAMP)     ,
	created_by           INT UNSIGNED      ,
	modified             DATETIME       ,
	modified_by          INT UNSIGNED
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Join-Tabelle: Einsatz <-> Einheiten ===
CREATE  TABLE `#__blaulichtmonitor_einsatz_einheiten` (
	einsatz_id           INT UNSIGNED   NOT NULL   ,
	einheit_id           INT UNSIGNED   NOT NULL   ,
	CONSTRAINT `pk_#__blaulichtmonitor_einsatz_einheiten` PRIMARY KEY ( einsatz_id, einheit_id )
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Join-Tabelle: Einsatz <-> Kurzbericht ===
CREATE  TABLE `#__blaulichtmonitor_einsatz_kurzbericht` (
	einsatz_id           INT UNSIGNED   NOT NULL   ,
	kurzbeschreibung_id  INT UNSIGNED   NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Einsatz-Presseartikel ===
CREATE  TABLE `#__blaulichtmonitor_einsatz_presse` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	einsatz_id           INT UNSIGNED   NOT NULL   ,
	url                  VARCHAR(1024)    NOT NULL   ,
	titel                VARCHAR(255)       ,
	quelle               VARCHAR(255)       ,
	created              DATETIME  DEFAULT (CURRENT_TIMESTAMP)     ,
	created_by           INT UNSIGNED      ,
	modified             DATETIME       ,
	modified_by          INT UNSIGNED
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Einsatzbilder ===
CREATE  TABLE `#__blaulichtmonitor_einsatzbilder` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	einsatz_id           INT UNSIGNED   NOT NULL   ,
	filename             VARCHAR(255)    NOT NULL   ,
	thumbnail            VARCHAR(255)       ,
	wasserzeichen        TINYINT  DEFAULT (0)     ,
	fotograf             VARCHAR(255)       ,
	quelle               VARCHAR(255)       ,
	created              DATETIME  DEFAULT (CURRENT_TIMESTAMP)     ,
	created_by           INT UNSIGNED      ,
	modified             DATETIME       ,
	modified_by          INT UNSIGNED
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Join-Tabelle: Einsatz <-> Einsatzleiter ===
CREATE  TABLE `#__blaulichtmonitor_einsatzleiter_einsatz` (
	einsatzleiter_id     INT UNSIGNED   NOT NULL   ,
	einsatz_id           INT UNSIGNED   NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Tabelle: Fahrzeuge ===
CREATE  TABLE `#__blaulichtmonitor_fahrzeuge` (
	id                   INT UNSIGNED   NOT NULL AUTO_INCREMENT  PRIMARY KEY,
	einheit_id           INT UNSIGNED   NOT NULL   ,
	funkrufname          VARCHAR(100)    NOT NULL   ,
	beschreibung         TEXT       ,
	bild                 VARCHAR(255)       ,
	in_dienst            TINYINT  DEFAULT (1)  NOT NULL   ,
	created              DATETIME  DEFAULT (CURRENT_TIMESTAMP)     ,
	created_by           INT UNSIGNED      ,
	modified             DATETIME       ,
	modified_by          INT UNSIGNED
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Join-Tabelle: Einsatz <-> Fahrzeuge ===
CREATE  TABLE `#__blaulichtmonitor_einsatz_fahrzeuge` (
	einsatz_id           INT UNSIGNED   NOT NULL   ,
	fahrzeug_id          INT UNSIGNED   NOT NULL   ,
	CONSTRAINT `pk_#__blaulichtmonitor_einsatz_fahrzeuge` PRIMARY KEY ( einsatz_id, fahrzeug_id )
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

-- === Fremdschlüssel: Einheiten ===
ALTER TABLE `#__blaulichtmonitor_einheiten`
  ADD CONSTRAINT `fk_einheiten_dispogruppe` FOREIGN KEY (dispogruppe_id) REFERENCES `#__blaulichtmonitor_dispogruppen`(id) ON DELETE SET NULL ON UPDATE NO ACTION;

ALTER TABLE `#__blaulichtmonitor_einheiten`
  ADD CONSTRAINT `fk_einheiten_organisation` FOREIGN KEY (organisation_id) REFERENCES `#__blaulichtmonitor_organisation`(id) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- === Fremdschlüssel: Einsätze ===
ALTER TABLE `#__blaulichtmonitor_einsaetze`
  ADD CONSTRAINT `fk_einsaetze_alarmierungsart` FOREIGN KEY (alarmierungsart_id) REFERENCES `#__blaulichtmonitor_alarmierungsarten`(id) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `#__blaulichtmonitor_einsaetze`
  ADD CONSTRAINT `fk_einsaetze_art` FOREIGN KEY (einsatzart_id) REFERENCES `#__blaulichtmonitor_einsatzarten`(id) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `#__blaulichtmonitor_einsaetze`
  ADD CONSTRAINT `fk_einsaetze_kategorie` FOREIGN KEY (einsatzkategorie_id) REFERENCES `#__blaulichtmonitor_einsatzkategorien`(id) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `#__blaulichtmonitor_einsaetze`
  ADD CONSTRAINT `fk_einsaetze_ort` FOREIGN KEY (einsatzort_id) REFERENCES `#__blaulichtmonitor_einsatzort`(id) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- === Fremdschlüssel für Einsatz-Einheiten (Join-Tabelle) ===
ALTER TABLE `#__blaulichtmonitor_einsatz_einheiten`
  ADD CONSTRAINT `fk_ee_einsatz` FOREIGN KEY (einsatz_id) REFERENCES `#__blaulichtmonitor_einsaetze`(id) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `#__blaulichtmonitor_einsatz_einheiten`
  ADD CONSTRAINT `fk_ee_einheit` FOREIGN KEY (einheit_id) REFERENCES `#__blaulichtmonitor_einheiten`(id) ON DELETE CASCADE ON UPDATE NO ACTION;

-- === Fremdschlüssel für Einsatz-Fahrzeuge (Join-Tabelle) ===
ALTER TABLE `#__blaulichtmonitor_einsatz_fahrzeuge`
  ADD CONSTRAINT `fk_ef_einsatz` FOREIGN KEY (einsatz_id) REFERENCES `#__blaulichtmonitor_einsaetze`(id) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `#__blaulichtmonitor_einsatz_fahrzeuge`
  ADD CONSTRAINT `fk_ef_fahrzeug` FOREIGN KEY (fahrzeug_id) REFERENCES `#__blaulichtmonitor_fahrzeuge`(id) ON DELETE CASCADE ON UPDATE NO ACTION;

-- === Fremdschlüssel für Einsatz-Kurzbericht (Join-Tabelle) ===
ALTER TABLE `#__blaulichtmonitor_einsatz_kurzbericht`
  ADD CONSTRAINT `fk_ek_einsatz` FOREIGN KEY (einsatz_id) REFERENCES `#__blaulichtmonitor_einsaetze`(id) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `#__blaulichtmonitor_einsatz_kurzbericht`
  ADD CONSTRAINT `fk_ek_kurzbericht` FOREIGN KEY (kurzbeschreibung_id) REFERENCES `#__blaulichtmonitor_kurzbericht`(id) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- === Fremdschlüssel für Einsatz-Presse ===
ALTER TABLE `#__blaulichtmonitor_einsatz_presse`
  ADD CONSTRAINT `fk_ep_einsatz` FOREIGN KEY (einsatz_id) REFERENCES `#__blaulichtmonitor_einsaetze`(id) ON DELETE CASCADE ON UPDATE NO ACTION;

-- === Fremdschlüssel für Einsatz-Bilder ===
ALTER TABLE `#__blaulichtmonitor_einsatzbilder`
  ADD CONSTRAINT `fk_eb_einsatz` FOREIGN KEY (einsatz_id) REFERENCES `#__blaulichtmonitor_einsaetze`(id) ON DELETE CASCADE ON UPDATE NO ACTION;

-- === Fremdschlüssel für Einsatzleiter-Zuordnung (Join-Tabelle) ===
ALTER TABLE `#__blaulichtmonitor_einsatzleiter_einsatz`
  ADD CONSTRAINT `fk_ele_einsatz` FOREIGN KEY (einsatz_id) REFERENCES `#__blaulichtmonitor_einsaetze`(id) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `#__blaulichtmonitor_einsatzleiter_einsatz`
  ADD CONSTRAINT `fk_ele_leiter` FOREIGN KEY (einsatzleiter_id) REFERENCES `#__blaulichtmonitor_einsatzleiter`(id) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- === Fremdschlüssel für Einsatzleiter-Zeitraum ===
ALTER TABLE `#__blaulichtmonitor_einsatzleiter_zeitraum`
  ADD CONSTRAINT `fk_zeitraum_leiter` FOREIGN KEY (einsatzleiter_id) REFERENCES `#__blaulichtmonitor_einsatzleiter`(id) ON DELETE CASCADE ON UPDATE NO ACTION;

-- === Fremdschlüssel für Fahrzeuge ===
ALTER TABLE `#__blaulichtmonitor_fahrzeuge`
  ADD CONSTRAINT `fk_fahrzeuge_einheit` FOREIGN KEY (einheit_id) REFERENCES `#__blaulichtmonitor_einheiten`(id) ON DELETE CASCADE ON UPDATE NO ACTION;

-- === Fremdschlüssel für created_by ===
ALTER TABLE `#__blaulichtmonitor_einsatzarten`
  ADD CONSTRAINT `fk_created_by_einsatzarten` FOREIGN KEY (created_by) REFERENCES `#__users`(id) ON DELETE SET NULL ON UPDATE NO ACTION;