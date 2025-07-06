<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class MigrationService
{
    /**
     * Maximale Anzahl Datensätze pro Batch-Insert.
     */
    private int $batchSize = 500;

    /**
     * Hilfsfunktion: Gibt einen SQL-Wert je nach Typ oder NULL zurück.
     */
    private function sqlValue($value, $db, $type = 'string')
    {
        if ($value === null || $value === '' || ($type === 'int' && $value === 0)) {
            return 'NULL';
        }
        switch ($type) {
            case 'int':
                return (int)$value;
            case 'bool':
                return (int)(bool)$value;
            case 'date':
                return $db->q(date('Y-m-d H:i:s', strtotime($value)));
            default:
                return $db->q($value);
        }
    }

    /**
     * Migration der Einheiten
     */
    public function migrateEinheiten(): array
    {
        $db = Factory::getDbo();
        $results = [];

        $query = $db->getQuery(true)
            ->select($db->qn([
                'id',
                'ordering',
                'name',
                'detail1_label',
                'detail1',
                'detail2_label',
                'detail2',
                'detail3_label',
                'detail3',
                'detail4_label',
                'detail4',
                'detail5_label',
                'detail5',
                'detail6_label',
                'detail6',
                'detail7_label',
                'detail7',
                'link',
                'desc',
                'created_by'
            ]))
            ->from($db->qn('#__eiko_organisationen'));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        foreach ($rows as $row) {
            $created = date('Y-m-d H:i:s');
            $beschreibungParts = [];

            // Detail-Label/Wert-Paare prüfen
            for ($i = 1; $i <= 7; $i++) {
                $label = $row["detail{$i}_label"] ?? '';
                $value = $row["detail{$i}"] ?? '';
                if (!empty($label) && !empty($value)) {
                    $beschreibungParts[] = '<p><span>' . $label . ': </span><span>' . $value . '</span></p>';
                }
            }
            // desc-Feld anhängen, falls vorhanden
            if (!empty($row['desc'])) {
                $beschreibungParts[] = $row['desc'];
            }
            $beschreibung = !empty($beschreibungParts)
                ? $db->q(implode("\n", $beschreibungParts))
                : 'NULL';

            $link = $this->sqlValue($row['link'], $db);

            $insert = $db->getQuery(true)
                ->insert($db->qn('#__blaulichtmonitor_einheiten'))
                ->columns(['id', 'title', 'name', 'url', 'beschreibung', 'ordering', 'created_by', 'created'])
                ->values(
                    $this->sqlValue($row['id'], $db) . ', ' .
                        $this->sqlValue($row['name'], $db) . ', ' .
                        $this->sqlValue($row['detail1'], $db) . ', ' .
                        $link . ', ' .
                        $beschreibung . ', ' .
                        $this->sqlValue($row['ordering'], $db) . ', ' .
                        $this->sqlValue($row['created_by'], $db) . ', ' .
                        $this->sqlValue($created, $db)
                );
            try {
                $db->setQuery($insert)->execute();
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_EINHEIT', $row['id'], $row['name']);
            } catch (\Exception $e) {
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_EINHEIT', $row['id'], $row['name'], $e->getMessage());
            }
        }
        return $results;
    }

    /**
     * Migration der Alarmierungsarten
     */
    public function migrateAlarmierungsarten(): array
    {
        $db = Factory::getDbo();
        $results = [];

        $query = $db->getQuery(true)
            ->select(['id', 'title', 'ordering', 'image', 'created_by'])
            ->from($db->qn('#__eiko_alarmierungsarten'));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        foreach ($rows as $row) {
            $image_url = $this->sqlValue($row['image'], $db);

            $insert = $db->getQuery(true)
                ->insert($db->qn('#__blaulichtmonitor_alarmierungsarten'))
                ->columns(['id', 'title', 'image_url', 'ordering', 'created_by'])
                ->values(
                    $this->sqlValue($row['id'], $db) . ', ' .
                        $this->sqlValue($row['title'], $db) . ', ' .
                        $image_url . ', ' .
                        $this->sqlValue($row['ordering'], $db) . ', ' .
                        $this->sqlValue($row['created_by'], $db)
                );
            try {
                $db->setQuery($insert)->execute();
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_ALARMIERUNGSART', $row['title']);
            } catch (\Exception $e) {
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_ALARMIERUNGSART', $row['title'], $e->getMessage());
            }
        }
        return $results;
    }

    /**
     * Migration der Einsatzarten
     */
    public function migrateEinsatzarten(): array
    {
        $db = Factory::getDbo();
        $results = [];

        $query = $db->getQuery(true)
            ->select(['id', 'title', 'marker', 'beschr', 'icon', 'list_icon', 'ordering', 'created_by'])
            ->from($db->qn('#__eiko_einsatzarten'));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        foreach ($rows as $row) {
            $created = date('Y-m-d H:i:s');
            $icon_url = '';
            if (!empty($row['icon'])) {
                $icon_url = $row['icon'];
            } elseif (!empty($row['list_icon'])) {
                $icon_url = $row['list_icon'];
            } elseif (!empty($row['beschr'])) {
                $icon_url = $row['beschr'];
            }
            $icon_url = $this->sqlValue($icon_url, $db);

            $insert = $db->getQuery(true)
                ->insert($db->qn('#__blaulichtmonitor_einsatzarten'))
                ->columns(['id', 'title', 'colour_marker', 'icon_url', 'ordering', 'created_by', 'created'])
                ->values(
                    $this->sqlValue($row['id'], $db) . ', ' .
                        $this->sqlValue($row['title'], $db) . ', ' .
                        $this->sqlValue($row['marker'], $db) . ', ' .
                        $icon_url . ', ' .
                        $this->sqlValue($row['ordering'], $db) . ', ' .
                        $this->sqlValue($row['created_by'], $db) . ', ' .
                        $this->sqlValue($created, $db)
                );
            try {
                $db->setQuery($insert)->execute();
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_EINSATZART', $row['title']);
            } catch (\Exception $e) {
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_EINSATZART', $row['title'], $e->getMessage());
            }
        }
        return $results;
    }

    /**
     * Migration der Einsatzkategorien
     */
    public function migrateEinsatzkategorien(): array
    {
        $db = Factory::getDbo();
        $results = [];

        $query = $db->getQuery(true)
            ->select(['id', 'title', 'image', 'ordering', 'created_by'])
            ->from($db->qn('#__eiko_tickerkat'));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        foreach ($rows as $row) {
            $created = date('Y-m-d H:i:s');
            $insert = $db->getQuery(true)
                ->insert($db->qn('#__blaulichtmonitor_einsatzkategorien'))
                ->columns(['id', 'title', 'icon_url', 'ordering', 'created_by', 'created'])
                ->values(
                    $this->sqlValue($row['id'], $db) . ', ' .
                        $this->sqlValue($row['title'], $db) . ', ' .
                        $this->sqlValue($row['image'], $db) . ', ' .
                        $this->sqlValue($row['ordering'], $db) . ', ' .
                        $this->sqlValue($row['created_by'], $db) . ', ' .
                        $this->sqlValue($created, $db)
                );
            try {
                $db->setQuery($insert)->execute();
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_EINSATZKATEGORIE', $row['title']);
            } catch (\Exception $e) {
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_EINSATZKATEGORIE', $row['title'], $e->getMessage());
            }
        }
        return $results;
    }

    /**
     * Migration der Einsatzleiter
     */
    public function migrateEinsatzleiter(): array
    {
        $db = Factory::getDbo();
        $results = [];

        $query = $db->getQuery(true)
            ->select(['boss', 'boss2'])
            ->from($db->qn('#__eiko_einsatzberichte'));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        // Alle Namen sammeln, leere Werte ignorieren
        $leiter = [];
        foreach ($rows as $row) {
            if (!empty($row['boss'])) {
                $leiter[] = trim($row['boss']);
            }
            if (!empty($row['boss2'])) {
                $leiter[] = trim($row['boss2']);
            }
        }
        $leiter = array_unique(array_filter($leiter));
        $ordering = 1;

        foreach ($leiter as $name) {
            $created = date('Y-m-d H:i:s');
            $insert = $db->getQuery(true)
                ->insert($db->qn('#__blaulichtmonitor_einsatzleiter'))
                ->columns(['name', 'ordering', 'created'])
                ->values(
                    $this->sqlValue($name, $db) . ', ' .
                        $this->sqlValue($ordering, $db) . ', ' .
                        $this->sqlValue($created, $db)
                );
            try {
                $db->setQuery($insert)->execute();
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_EINSATZLEITER', $name);
                $ordering++;
            } catch (\Exception $e) {
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_EINSATZLEITER', $name, $e->getMessage());
            }
        }
        return $results;
    }

    /**
     * Migration der Einsatzorte (Straßen)
     */
    public function migrateEinsatzort(): array
    {
        $db = Factory::getDbo();
        $results = [];

        $query = $db->getQuery(true)
            ->select(['address'])
            ->from($db->qn('#__eiko_einsatzberichte'));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        $strassen = [];
        foreach ($rows as $row) {
            $strasse = trim($row['address'] ?? '');
            if (!empty($strasse)) {
                $strassen[] = $strasse;
            }
        }
        $strassen = array_unique($strassen);

        foreach ($strassen as $strasse) {
            $insert = $db->getQuery(true)
                ->insert($db->qn('#__blaulichtmonitor_einsatzort'))
                ->columns(['strasse'])
                ->values($this->sqlValue($strasse, $db));
            try {
                $db->setQuery($insert)->execute();
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_STRASSE', $strasse);
            } catch (\Exception $e) {
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_STRASSE', $strasse, $e->getMessage());
            }
        }
        return $results;
    }

    /**
     * Migration der Fahrzeuge
     */
    public function migrateFahrzeuge(): array
    {
        $db = Factory::getDbo();
        $results = [];

        $query = $db->getQuery(true)
            ->select($db->qn([
                'id',
                'name',
                'detail1_label',
                'detail1',
                'detail2_label',
                'detail2',
                'detail3_label',
                'detail3',
                'detail4_label',
                'detail4',
                'detail5_label',
                'detail5',
                'detail6_label',
                'detail6',
                'detail7_label',
                'detail7',
                'department',
                'ausruestung',
                'link',
                'image',
                'desc',
                'ordering',
                'created_by'
            ]))
            ->from($db->qn('#__eiko_fahrzeuge'));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        foreach ($rows as $row) {
            $created = date('Y-m-d H:i:s');
            $beschreibungParts = [];
            for ($i = 1; $i <= 7; $i++) {
                $label = $row["detail{$i}_label"] ?? '';
                $value = $row["detail{$i}"] ?? '';
                if (!empty($label) && !empty($value)) {
                    $beschreibungParts[] = '<p><span>' . $label . ': </span><span>' . $value . '</span></p>';
                }
            }
            if (!empty($row['desc'])) {
                $beschreibungParts[] = $row['desc'];
            }
            $beschreibung = !empty($beschreibungParts)
                ? $db->q(implode("\n", $beschreibungParts))
                : 'NULL';

            $einheit_id = (!empty($row['department']) && is_numeric($row['department']))
                ? (int)$row['department']
                : 'NULL';

            $bild_url = $this->sqlValue($row['image'], $db);

            $insert = $db->getQuery(true)
                ->insert($db->qn('#__blaulichtmonitor_fahrzeuge'))
                ->columns(['id', 'einheit_id', 'funkrufname', 'beschreibung', 'bild_url', 'url', 'in_dienst', 'ordering', 'created_by', 'created'])
                ->values(
                    $this->sqlValue($row['id'], $db) . ', ' .
                        $einheit_id . ', ' .
                        $this->sqlValue($row['name'], $db) . ', ' .
                        $beschreibung . ', ' .
                        $bild_url . ', ' .
                        $this->sqlValue($row['link'], $db) . ', ' .
                        $this->sqlValue(1, $db) . ', ' .
                        $this->sqlValue($row['ordering'], $db) . ', ' .
                        $this->sqlValue($row['created_by'], $db) . ', ' .
                        $this->sqlValue($created, $db)
                );
            try {
                $db->setQuery($insert)->execute();
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_FAHRZEUG', $row['name']);
            } catch (\Exception $e) {
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_FAHRZEUG', $row['name'], $e->getMessage());
            }
        }
        return $results;
    }

    /**
     * Migration der Einsatzberichte
     */
    public function migrateEinsatzberichte(): array
    {
        $db = Factory::getDbo();
        $results = [];

        // Einsatzleiter-Mapping vorbereiten
        $leiterQuery = $db->getQuery(true)
            ->select(['id', 'name'])
            ->from($db->qn('#__blaulichtmonitor_einsatzleiter'));
        $db->setQuery($leiterQuery);
        $leiterRows = $db->loadAssocList();
        $leiterMap = [];
        foreach ($leiterRows as $leiter) {
            $leiterMap[trim(strtolower($leiter['name']))] = (int)$leiter['id'];
        }

        $query = $db->getQuery(true)
            ->select([
                'id',
                'article_id',
                'data1',
                'image',
                'address',
                'date1',
                'date2',
                'date3',
                'summary',
                'boss',
                'boss2',
                'people',
                'department',
                '`desc`',
                'alerting',
                'counter',
                'presse_label',
                'presse',
                'presse2_label',
                'presse2',
                'presse3_label',
                'presse3',
                'updatedate',
                'createdate',
                'tickerkat',
                'auswahl_orga',
                'vehicles',
                'ausruestung',
                'status',
                'state',
                'created_by',
                'modified_by'
            ])
            ->from($db->qn('#__eiko_einsatzberichte'));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        foreach ($rows as $row) {
            // Einsatzleiter-ID bestimmen
            $leiterName = trim(strtolower($row['boss'] ?? ''));
            $einsatzleiter_id = isset($leiterMap[$leiterName]) ? $leiterMap[$leiterName] : 'NULL';

            // Felder prüfen und ggf. NULL setzen
            $prioritaet = (isset($row['prioritaet']) && $row['prioritaet'] !== '' && is_numeric($row['prioritaet']))
                ? (int)$row['prioritaet'] : 'NULL';

            $people_count = (isset($row['people']) && $row['people'] !== '' && is_numeric($row['people']))
                ? (int)$row['people'] : 'NULL';

            $article_id = (isset($row['article_id']) && $row['article_id'] !== '' && $row['article_id'] != 0)
                ? $db->q($row['article_id']) : 'NULL';

            $ausrueckzeit = (
                empty($row['date2']) ||
                $row['date2'] === '0' ||
                $row['date2'] === '0000-00-00 00:00:00'
            ) ? 'NULL' : $db->q($row['date2']);

            $einsatzende = (
                empty($row['date3']) ||
                $row['date3'] === '0' ||
                $row['date3'] === '0000-00-00 00:00:00'
            ) ? 'NULL' : $db->q($row['date3']);

            $state = (isset($row['state']) && $row['state'] !== '' && $row['state'] != 0)
                ? $db->q($row['state']) : '0';

            $beschreibung = (isset($row['desc']) && trim($row['desc']) !== '')
                ? $db->q($row['desc']) : 'NULL';

            $insert = $db->getQuery(true)
                ->insert($db->qn('#__blaulichtmonitor_einsatzberichte'))
                ->columns([
                    'id',
                    'alarmierungsart_id',
                    'einsatzart_id',
                    'einsatzkategorie_id',
                    'einsatzkurzbericht',
                    'einsatzleiter_id',
                    'article_id',
                    'prioritaet',
                    'einsatzort_strasse',
                    'alarmierungszeit',
                    'ausrueckzeit',
                    'einsatzende',
                    'people_count',
                    'beschreibung',
                    'veroeffentlicht',
                    'counter_clicks',
                    'created_by',
                    'created',
                    'modified_by',
                    'modified'
                ])
                ->values(
                    $this->sqlValue($row['id'], $db) . ', ' .
                        $this->sqlValue($row['alerting'], $db) . ', ' .
                        $this->sqlValue($row['data1'], $db) . ', ' .
                        $this->sqlValue($row['tickerkat'], $db) . ', ' .
                        $this->sqlValue($row['summary'], $db) . ', ' .
                        $einsatzleiter_id . ', ' .
                        $article_id . ', ' .
                        $prioritaet . ', ' .
                        $this->sqlValue($row['address'], $db) . ', ' .
                        $this->sqlValue($row['date1'], $db) . ', ' .
                        $ausrueckzeit . ', ' .
                        $einsatzende . ', ' .
                        $people_count . ', ' .
                        $beschreibung . ', ' .
                        $state . ', ' .
                        $this->sqlValue($row['counter'], $db) . ', ' .
                        $this->sqlValue($row['created_by'], $db) . ', ' .
                        $this->sqlValue($row['createdate'], $db) . ', ' .
                        $this->sqlValue($row['modified_by'], $db) . ', ' .
                        $this->sqlValue($row['updatedate'], $db)
                );
            try {
                $db->setQuery($insert)->execute();
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_EINSATZBERICHT', $row['id']);
            } catch (\Exception $e) {
                $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_EINSATZBERICHT', $row['id'], $e->getMessage());
            }
        }
        return $results;
    }

    /**
     * Migration: Verknüpft Einsätze mit Einsatzleitern (Join-Tabelle)
     */
    public function migrateEinsatzleiterEinsatzbericht(): array
    {
        $db = Factory::getDbo();
        $results = [];

        // Mapping: Name → ID aus Ziel-Tabelle
        $leiterQuery = $db->getQuery(true)
            ->select(['id', 'name'])
            ->from($db->qn('#__blaulichtmonitor_einsatzleiter'));
        $db->setQuery($leiterQuery);
        $leiterRows = $db->loadAssocList();
        $leiterMap = [];
        foreach ($leiterRows as $leiter) {
            $leiterMap[trim(strtolower($leiter['name']))] = (int)$leiter['id'];
        }

        // Alle Einsatzberichte holen
        $einsatzQuery = $db->getQuery(true)
            ->select(['id', 'boss'])
            ->from($db->qn('#__eiko_einsatzberichte'));
        $db->setQuery($einsatzQuery);
        $einsatzRows = $db->loadAssocList();

        foreach ($einsatzRows as $row) {
            $einsatzbericht_id = (int)$row['id'];
            $leiterName = trim(strtolower($row['boss'] ?? ''));
            if ($leiterName && isset($leiterMap[$leiterName])) {
                $einsatzleiter_id = $leiterMap[$leiterName];

                $insert = $db->getQuery(true)
                    ->insert($db->qn('#__blaulichtmonitor_einsatzleiter_einsatzbericht'))
                    ->columns(['einsatzleiter_id', 'einsatzbericht_id'])
                    ->values($einsatzleiter_id . ', ' . $einsatzbericht_id);

                try {
                    $db->setQuery($insert)->execute();
                    $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_VERKNUEPFUNG', $einsatzbericht_id, 'Einsatzleiter', $einsatzleiter_id);
                } catch (\Exception $e) {
                    $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_VERKNUEPFUNG', $einsatzbericht_id, 'Einsatzleiter', $einsatzleiter_id, $e->getMessage());
                }
            }
        }
        return $results;
    }

    /**
     * Migration: Verknüpft Einsätze mit Einheiten (Join-Tabelle)
     * Das Feld 'auswahl_orga' in #__eiko_einsatzberichte ist kommasepariert (z.B. "1,16,14,12,33,13")
     */
    public function migrateEinsatzberichteEinheiten(): array
    {
        $db = Factory::getDbo();
        $results = [];

        // Alle gültigen Einheiten-IDs aus der Zieltabelle holen
        $einheitQuery = $db->getQuery(true)
            ->select(['id'])
            ->from($db->qn('#__blaulichtmonitor_einheiten'));
        $db->setQuery($einheitQuery);
        $einheitRows = $db->loadAssocList('id', 'id');

        // Alle Einsatzberichte holen
        $einsatzQuery = $db->getQuery(true)
            ->select(['id', 'auswahl_orga'])
            ->from($db->qn('#__eiko_einsatzberichte'));
        $db->setQuery($einsatzQuery);
        $einsatzRows = $db->loadAssocList();

        foreach ($einsatzRows as $row) {
            $einsatzbericht_id = (int)$row['id'];
            $orgaList = array_filter(array_map('trim', explode(',', $row['auswahl_orga'] ?? '')));
            foreach ($orgaList as $einheit_id) {
                if ($einheit_id !== '' && is_numeric($einheit_id) && isset($einheitRows[$einheit_id])) {
                    $insert = $db->getQuery(true)
                        ->insert($db->qn('#__blaulichtmonitor_einsatzberichte_einheiten'))
                        ->columns(['einsatzbericht_id', 'einheit_id'])
                        ->values($einsatzbericht_id . ', ' . (int)$einheit_id);
                    try {
                        $db->setQuery($insert)->execute();
                        $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_VERKNUEPFUNG', $einsatzbericht_id, 'Einheit', $einheit_id);
                    } catch (\Exception $e) {
                        $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_VERKNUEPFUNG', $einsatzbericht_id, 'Einheit', $einheit_id, $e->getMessage());
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Migration: Verknüpft Einsätze mit Fahrzeugen (Join-Tabelle)
     * Das Feld 'vehicles' in #__eiko_einsatzberichte ist kommasepariert (z.B. "2,32,5")
     */
    public function migrateEinsatzFahrzeuge(): array
    {
        $db = Factory::getDbo();
        $results = [];

        // Alle gültigen Fahrzeug-IDs aus der Zieltabelle holen
        $fahrzeugQuery = $db->getQuery(true)
            ->select(['id'])
            ->from($db->qn('#__blaulichtmonitor_fahrzeuge'));
        $db->setQuery($fahrzeugQuery);
        $fahrzeugRows = $db->loadAssocList('id', 'id');

        // Alle Einsatzberichte holen
        $einsatzQuery = $db->getQuery(true)
            ->select(['id', 'vehicles'])
            ->from($db->qn('#__eiko_einsatzberichte'));
        $db->setQuery($einsatzQuery);
        $einsatzRows = $db->loadAssocList();

        foreach ($einsatzRows as $row) {
            $einsatzbericht_id = (int)$row['id'];
            $vehicleList = array_filter(array_map('trim', explode(',', $row['vehicles'] ?? '')));
            foreach ($vehicleList as $fahrzeug_id) {
                if ($fahrzeug_id !== '' && is_numeric($fahrzeug_id) && isset($fahrzeugRows[$fahrzeug_id])) {
                    $insert = $db->getQuery(true)
                        ->insert($db->qn('#__blaulichtmonitor_einsatzberichte_fahrzeuge'))
                        ->columns(['einsatzbericht_id', 'fahrzeug_id'])
                        ->values($einsatzbericht_id . ', ' . (int)$fahrzeug_id);
                    try {
                        $db->setQuery($insert)->execute();
                        $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_VERKNUEPFUNG', $einsatzbericht_id, 'Fahrzeug', $fahrzeug_id);
                    } catch (\Exception $e) {
                        $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_VERKNUEPFUNG', $einsatzbericht_id, 'Fahrzeug', $fahrzeug_id, $e->getMessage());
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Migration: Verknüpft Einsätze mit Presseartikeln (Mehrere pro Einsatz möglich)
     * Es gibt bis zu 3 Gruppen: presse_label/presse, presse2_label/presse2, presse3_label/presse3
     */
    public function migrateEinsatzberichtePresse(): array
    {
        $db = Factory::getDbo();
        $results = [];

        // Alle Einsatzberichte holen
        $einsatzQuery = $db->getQuery(true)
            ->select(['id', 'presse_label', 'presse', 'presse2_label', 'presse2', 'presse3_label', 'presse3'])
            ->from($db->qn('#__eiko_einsatzberichte'));
        $db->setQuery($einsatzQuery);
        $einsatzRows = $db->loadAssocList();

        foreach ($einsatzRows as $row) {
            $einsatzbericht_id = (int)$row['id'];

            // Presse 1
            if (!empty($row['presse'])) {
                $insert = $db->getQuery(true)
                    ->insert($db->qn('#__blaulichtmonitor_einsatzberichte_presse'))
                    ->columns(['einsatzbericht_id', 'url', 'title'])
                    ->values(
                        $einsatzbericht_id . ', ' .
                            $db->q($row['presse']) . ', ' .
                            (!empty($row['presse_label']) ? $db->q($row['presse_label']) : 'NULL')
                    );
                try {
                    $db->setQuery($insert)->execute();
                    $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_SUCCESS_PRESSE', $einsatzbericht_id, 1);
                } catch (\Exception $e) {
                    $results[] = Text::sprintf('COM_BLAULICHTMONITOR_MIGRATION_ERROR_PRESSE', $einsatzbericht_id, 1, $e->getMessage());
                }
            }

            // Presse 2
            if (!empty($row['presse2'])) {
                $insert = $db->getQuery(true)
                    ->insert($db->qn('#__blaulichtmonitor_einsatzberichte_presse'))
                    ->columns(['einsatzbericht_id', 'url', 'title'])
                    ->values(
                        $einsatzbericht_id . ', ' .
                            $db->q($row['presse2']) . ', ' .
                            (!empty($row['presse2_label']) ? $db->q($row['presse2_label']) : 'NULL')
                    );
                try {
                    $db->setQuery($insert)->execute();
                    $results[] = "✅ Einsatz {$einsatzbericht_id} Presse 2 migriert.";
                } catch (\Exception $e) {
                    $results[] = "❌ Fehler bei Einsatz {$einsatzbericht_id} Presse 2: " . $e->getMessage();
                }
            }

            // Presse 3
            if (!empty($row['presse3'])) {
                $insert = $db->getQuery(true)
                    ->insert($db->qn('#__blaulichtmonitor_einsatzberichte_presse'))
                    ->columns(['einsatzbericht_id', 'url', 'title'])
                    ->values(
                        $einsatzbericht_id . ', ' .
                            $db->q($row['presse3']) . ', ' .
                            (!empty($row['presse3_label']) ? $db->q($row['presse3_label']) : 'NULL')
                    );
                try {
                    $db->setQuery($insert)->execute();
                    $results[] = "✅ Einsatz {$einsatzbericht_id} Presse 3 migriert.";
                } catch (\Exception $e) {
                    $results[] = "❌ Fehler bei Einsatz {$einsatzbericht_id} Presse 3: " . $e->getMessage();
                }
            }
        }
        return $results;
    }

    /**
     * Führt alle Migrationen aus
     */
    public function migrateAll(): array
    {
        /**
         * Führt alle Migrationen in sinnvoller Reihenfolge aus.
         *
         * Reihenfolge:
         * 1. Stammdaten-/Lookup-Tabellen (unabhängige Tabellen)
         * 2. Einheiten, Fahrzeuge, Einsatzorte (abhängig von Lookup)
         * 3. Haupttabelle: Einsatzberichte (benötigt Lookup, Einheiten, Fahrzeuge, Einsatzleiter)
         * 4. Join-Tabellen (Verknüpfungen zwischen Einsatzberichten und anderen Entitäten)
         * 5. Medien & Presse (benötigt Einsatzberichte)
         */
        return [
            // 1. Stammdaten-/Lookup-Tabellen
            '#__blaulichtmonitor_alarmierungsarten' => $this->migrateAlarmierungsarten(),
            '#__blaulichtmonitor_einsatzarten' => $this->migrateEinsatzarten(),
            '#__blaulichtmonitor_einsatzkategorien' => $this->migrateEinsatzkategorien(),
            '#__blaulichtmonitor_einsatzleiter' => $this->migrateEinsatzleiter(),

            // 2. Einheiten, Fahrzeuge, Einsatzorte
            '#__blaulichtmonitor_einheiten' => $this->migrateEinheiten(),
            '#__blaulichtmonitor_fahrzeuge' => $this->migrateFahrzeuge(),
            '#__blaulichtmonitor_einsatzort' => $this->migrateEinsatzort(),

            // 3. Haupttabelle: Einsatzberichte
            '#__blaulichtmonitor_einsatzberichte' => $this->migrateEinsatzberichte(),

            // 4. Join-Tabellen (Verknüpfungen)
            '#__blaulichtmonitor_einsatzleiter_einsatzbericht' => $this->migrateEinsatzleiterEinsatzbericht(),
            '#__blaulichtmonitor_einsatzberichte_einheiten' => $this->migrateEinsatzberichteEinheiten(),
            '#__blaulichtmonitor_einsatzberichte_fahrzeuge' => $this->migrateEinsatzFahrzeuge(),

            // 5. Medien & Presse
            '#__blaulichtmonitor_einsatzberichte_presse' => $this->migrateEinsatzberichtePresse(),

            // Weitere Migrationen hier ergänzen, falls benötigt
        ];
    }
}
