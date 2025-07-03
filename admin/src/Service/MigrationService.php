<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class MigrationService
{
    public function migrateOrganisationen(): array
    {
        $db = Factory::getDbo();
        $results = [];

        $query = $db->getQuery(true)
            ->select($db->qn(['id', 'detail1', 'name', 'link', 'desc', 'created_by']))
            ->from($db->qn('#__eiko_organisationen'));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        foreach ($rows as $row) {
            $created = date('Y-m-d H:i:s');
            $insert = $db->getQuery(true)
                ->insert($db->qn('#__blaulichtmonitor_einheiten'))
                ->columns(['id', 'title', 'name', 'url', 'beschreibung', 'created_by', 'created'])
                ->values(
                    $db->q($row['id']) . ', ' .
                        $db->q($row['name']) . ', ' .
                        $db->q($row['detail1']) . ', ' .
                        $db->q($row['link']) . ', ' .
                        $db->q($row['desc']) . ', ' .
                        $db->q($row['created_by']) . ', ' .
                        $db->q($created)
                );
            try {
                $db->setQuery($insert)->execute();
                $results[] = "✅ ID {$row['id']} - Name {$row['name']} migriert.";
            } catch (\Exception $e) {
                $results[] = "❌ Fehler bei ID {$row['id']} - Name {$row['name']}: " . $e->getMessage();
            }
        }

        return $results;
    }

    public function migrateEinsatzarten(): array
    {
        $db = Factory::getDbo();
        $results = [];

        $query = $db->getQuery(true)
            ->select(['id', 'title', 'created_by'])
            ->from($db->qn('#__eiko_einsatzarten'));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        foreach ($rows as $row) {
            $created = date('Y-m-d H:i:s');
            $insert = $db->getQuery(true)
                ->insert($db->qn('#__blaulichtmonitor_einsatzarten'))
                ->columns(['id', 'title', 'created_by', 'created'])
                ->values(
                    $db->q($row['id']) . ', ' .
                        $db->q($row['title']) . ', ' .
                        $db->q($row['created_by']) . ', ' .
                        $db->q($created)
                );
            try {
                $db->setQuery($insert)->execute();
                $results[] = "✅ ID {$row['id']} - Title {$row['title']} migriert.";
            } catch (\Exception $e) {
                $results[] = "❌ Fehler bei ID {$row['id']} - Title {$row['title']}: " . $e->getMessage();
            }
        }

        return $results;
    }

    public function migrateEinsatzkategorien(): array
    {
        $db = Factory::getDbo();
        $results = [];

        $query = $db->getQuery(true)
            ->select(['id', 'title', 'created_by'])
            ->from($db->qn('#__eiko_tickerkat'));
        $db->setQuery($query);
        $rows = $db->loadAssocList();

        foreach ($rows as $row) {
            $created = date('Y-m-d H:i:s');
            $insert = $db->getQuery(true)
                ->insert($db->qn('#__blaulichtmonitor_einsatzkategorien'))
                ->columns(['id', 'title', 'created_by', 'created'])
                ->values(
                    $db->q($row['id']) . ', ' .
                        $db->q($row['title']) . ', ' .
                        $db->q($row['created_by']) . ', ' .
                        $db->q($created)
                );
            try {
                $db->setQuery($insert)->execute();
                $results[] = "✅ ID {$row['id']} - Title {$row['title']} migriert.";
            } catch (\Exception $e) {
                $results[] = "❌ Fehler bei ID {$row['id']} - Title {$row['title']}: " . $e->getMessage();
            }
        }

        return $results;
    }

    public function migrateAll(): array
    {
        return [
            '#__blaulichtmonitor_einheiten' => $this->migrateOrganisationen(),
            '#__blaulichtmonitor_einsatzarten' => $this->migrateEinsatzarten(),
            '#__blaulichtmonitor_einsatzkategorien' => $this->migrateEinsatzkategorien(),
            // weitere Migrationen hier ergänzen
        ];
    }
}
