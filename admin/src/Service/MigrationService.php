<?php

namespace AlexanderGropp\Component\BlaulichtMonitor\Administrator\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class MigrationService
{
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
            '#__blaulichtmonitor_einsatzarten' => $this->migrateEinsatzarten(),
            '#__blaulichtmonitor_einsatzkategorien' => $this->migrateEinsatzkategorien(),
            // weitere Migrationen hier ergänzen
        ];
    }
}
