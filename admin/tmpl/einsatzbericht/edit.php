<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;

// Initialisiere die Formularvalidierung und Keepalive-Verhalten für das Backend-Formular
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Lade die Einstellungen aus der Komponentenkonfiguration
$params = ComponentHelper::getParams('com_blaulichtmonitor');

// Definiere alle Felder, die über die Konfiguration ausgeblendet werden können
$configFields = [
	'alarmierungsart_id',
	'einsatzart_id',
	'einsatzkategorie_id',
	'prioritaet',
	'einsatzort_strasse',
	'einsatzort_strasse_select',
	'einsatzort_hausnummer',
	'einsatzort_plz',
	'einsatzort_stadt',
	'alarmierungszeit',
	'ausrueckzeit',
	'einsatzende',
	'einsatzleiter_id',
	'people_count',
	'einheiten_id',
	'fahrzeuge_id',
	'einsatzkurzbericht',
	'beschreibung',
	'einsatzbilder',
	'presseberichte',
	'published'
];

// Prüfe, welche Felder laut Konfiguration ausgeblendet werden sollen
$hideFields = [];
foreach ($configFields as $fieldName) {
	if ($params->get('show_' . $fieldName, 1) == 0) {
		$hideFields[] = $fieldName;
	}
}

/**
 * Hilfsfunktion: Prüft, ob mindestens ein Feld aus einer Feldgruppe angezeigt werden soll.
 *
 * @param array $fields     Feldnamen der Gruppe
 * @param array $hideFields Felder, die ausgeblendet werden sollen
 * @param \JForm $form      Formularobjekt
 * @return bool             true, wenn mindestens ein Feld sichtbar ist
 */
function hasVisibleFields($fields, $hideFields, $form)
{
	foreach ($fields as $fieldName) {
		$field = $form->getField($fieldName);
		if ($field && !in_array($fieldName, $hideFields)) {
			return true;
		}
	}
	return false;
}
?>

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einsatzbericht&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="project-form" class="form-validate">
	<div class="container">
		<?php
		// Abschnitt: Einsatzart & Kategorie
		$fieldsEinsatzart = ['alarmierungsart_id', 'einsatzart_id', 'einsatzkategorie_id', 'prioritaet'];
		if (hasVisibleFields($fieldsEinsatzart, $hideFields, $this->form)) {
			echo '<div class="card mb-4"><div class="card-header fw-bold">Einsatzart & Kategorie</div><div class="card-body"><div class="row">';
			foreach ($fieldsEinsatzart as $fieldName) {
				$field = $this->form->getField($fieldName);
				if ($field && !in_array($fieldName, $hideFields)) {
					echo '<div class="col-md-6 mb-3">';
					echo '<label class="form-label" for="' . $field->id . '">' . $field->label . '</label>';
					echo $field->input;
					echo '</div>';
				}
			}
			echo '</div></div></div>';
		}

		// Abschnitt: Einsatzort
		$fieldsEinsatzort = ['einsatzort_strasse_select', 'einsatzort_strasse', 'einsatzort_hausnummer', 'einsatzort_plz', 'einsatzort_stadt'];
		if (hasVisibleFields($fieldsEinsatzort, $hideFields, $this->form)) {
			echo '<div class="card mb-4"><div class="card-header fw-bold">Einsatzort</div><div class="card-body">';
			// Auswahlfeld für vorhandene Einsatzorte (volle Breite)
			$field = $this->form->getField('einsatzort_strasse_select');
			if ($field && !in_array('einsatzort_strasse_select', $hideFields)) {
				echo '<div class="row"><div class="col-12 mb-3">';
				echo '<label class="form-label fw-bold" for="' . $field->id . '">' . $field->label . '</label>';
				echo $field->input;
				echo '</div></div>';
			}
			// Straße und Hausnummer nebeneinander
			echo '<div class="row">';
			$fieldStrasse = $this->form->getField('einsatzort_strasse');
			$fieldHausnummer = $this->form->getField('einsatzort_hausnummer');
			if ($fieldStrasse && !in_array('einsatzort_strasse', $hideFields)) {
				echo '<div class="col-md-8 mb-3">';
				echo '<label class="form-label" for="' . $fieldStrasse->id . '">' . $fieldStrasse->label . '</label>';
				echo $fieldStrasse->input;
				echo '</div>';
			}
			if ($fieldHausnummer && !in_array('einsatzort_hausnummer', $hideFields)) {
				echo '<div class="col-md-4 mb-3">';
				echo '<label class="form-label" for="' . $fieldHausnummer->id . '">' . $fieldHausnummer->label . '</label>';
				echo $fieldHausnummer->input;
				echo '</div>';
			}
			echo '</div>';
			// PLZ und Stadt nebeneinander
			echo '<div class="row">';
			$fieldPlz = $this->form->getField('einsatzort_plz');
			$fieldStadt = $this->form->getField('einsatzort_stadt');
			if ($fieldPlz && !in_array('einsatzort_plz', $hideFields)) {
				echo '<div class="col-md-4 mb-3">';
				echo '<label class="form-label" for="' . $fieldPlz->id . '">' . $fieldPlz->label . '</label>';
				echo $fieldPlz->input;
				echo '</div>';
			}
			if ($fieldStadt && !in_array('einsatzort_stadt', $hideFields)) {
				echo '<div class="col-md-8 mb-3">';
				echo '<label class="form-label" for="' . $fieldStadt->id . '">' . $fieldStadt->label . '</label>';
				echo $fieldStadt->input;
				echo '</div>';
			}
			echo '</div>';
			// Hinweis zur Einsatzort-Auswahl
			echo '<div class="row"><div class="col-12"><small class="text-muted">Tipp: Wähle zuerst einen vorhandenen Einsatzort aus der Liste. Die Felder werden automatisch ausgefüllt und können bei Bedarf angepasst werden.</small></div></div>';
			echo '</div></div>';
		}

		// Abschnitt: Zeitangaben
		$fieldsZeiten = ['alarmierungszeit', 'ausrueckzeit', 'einsatzende'];
		if (hasVisibleFields($fieldsZeiten, $hideFields, $this->form)) {
			echo '<div class="card mb-4"><div class="card-header fw-bold">Zeitangaben</div><div class="card-body"><div class="row">';
			foreach ($fieldsZeiten as $fieldName) {
				$field = $this->form->getField($fieldName);
				if ($field && !in_array($fieldName, $hideFields)) {
					echo '<div class="col-md-4 mb-3">';
					echo '<label class="form-label" for="' . $field->id . '">' . $field->label . '</label>';
					echo $field->input;
					echo '</div>';
				}
			}
			echo '</div></div></div>';
		}

		// Abschnitt: Einsatzleiter & Mannschaft
		$fieldsLeiter = ['einsatzleiter_id', 'people_count'];
		if (hasVisibleFields($fieldsLeiter, $hideFields, $this->form)) {
			echo '<div class="card mb-4"><div class="card-header fw-bold">Einsatzleiter & Mannschaft</div><div class="card-body"><div class="row">';
			foreach ($fieldsLeiter as $fieldName) {
				$field = $this->form->getField($fieldName);
				if ($field && !in_array($fieldName, $hideFields)) {
					echo '<div class="col-md-6 mb-3">';
					echo '<label class="form-label" for="' . $field->id . '">' . $field->label . '</label>';
					echo $field->input;
					echo '</div>';
				}
			}
			echo '</div></div></div>';
		}

		// Abschnitt: Einheiten & Fahrzeuge
		$fieldsEinheiten = ['einheiten_id', 'fahrzeuge_id'];
		if (hasVisibleFields($fieldsEinheiten, $hideFields, $this->form)) {
			echo '<div class="card mb-4"><div class="card-header fw-bold">Einheiten & Fahrzeuge</div><div class="card-body"><div class="row">';
			foreach ($fieldsEinheiten as $fieldName) {
				$field = $this->form->getField($fieldName);
				if ($field && !in_array($fieldName, $hideFields)) {
					echo '<div class="col-md-6 mb-3">';
					echo '<label class="form-label" for="' . $field->id . '">' . $field->label . '</label>';
					echo $field->input;
					echo '</div>';
				}
			}
			echo '</div></div></div>';
		}

		// Abschnitt: Bericht & Bilder
		$fieldsBericht = ['einsatzkurzbericht', 'beschreibung', 'einsatzbilder', 'presseberichte'];
		if (hasVisibleFields($fieldsBericht, $hideFields, $this->form)) {
			echo '<div class="card mb-4"><div class="card-header fw-bold">Bericht & Bilder</div><div class="card-body"><div class="row">';
			foreach ($fieldsBericht as $fieldName) {
				$field = $this->form->getField($fieldName);
				if ($field && !in_array($fieldName, $hideFields)) {
					echo '<div class="col-12 mb-3">';
					echo '<label class="form-label" for="' . $field->id . '">' . $field->label . '</label>';
					echo $field->input;
					echo '</div>';
				}
			}
			echo '</div></div></div>';
		}

		// Abschnitt: Veröffentlichungsstatus
		$field = $this->form->getField('published');
		if ($field && !in_array('published', $hideFields)) {
			echo '<div class="card mb-4"><div class="card-header fw-bold">Status</div><div class="card-body">';
			echo '<label class="form-label" for="' . $field->id . '">' . $field->label . '</label>';
			echo $field->input;
			echo '</div></div>';
		}
		?>
	</div>
	<!-- Versteckte Felder für die Formularverarbeitung -->
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); // CSRF-Schutz
	?>
</form>

<script>
	// Dynamisches Ausfüllen der Einsatzort-Felder beim Wechsel des Auswahlfeldes
	document.addEventListener('DOMContentLoaded', function() {
		const select = document.getElementById('jform_einsatzort_strasse_select');
		if (select) {
			select.addEventListener('change', function() {
				const id = this.value;
				if (id) {
					// Hole die Einsatzort-Daten per AJAX und fülle die Felder automatisch aus
					fetch('index.php?option=com_blaulichtmonitor&task=einsatzbericht.getEinsatzort&id=' + id)
						.then(response => response.json())
						.then(data => {
							document.getElementById('jform_einsatzort_strasse').value = data.strasse || '';
							document.getElementById('jform_einsatzort_hausnummer').value = data.hausnummer || '';
							document.getElementById('jform_einsatzort_plz').value = data.plz || '';
							document.getElementById('jform_einsatzort_stadt').value = data.stadt || '';
						});
				} else {
					// Felder leeren, falls kein Einsatzort ausgewählt ist
					document.getElementById('jform_einsatzort_strasse').value = '';
					document.getElementById('jform_einsatzort_hausnummer').value = '';
					document.getElementById('jform_einsatzort_plz').value = '';
					document.getElementById('jform_einsatzort_stadt').value = '';
				}
			});
		}
	});
</script>