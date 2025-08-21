<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

// Initialisiere die Formularvalidierung und Keepalive-Verhalten für das Backend-Formular
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

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

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=einsatzfahrzeug&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="einsatzfahrzeug-form" class="form-validate">
	<div class="container">
		<?php
		// Abschnitt: Funkrufname &  Fahrzeug imn Dienst
		$fieldsEinsatzart = ['funkrufname', 'in_dienst', 'einheiten_id'];
		if (hasVisibleFields($fieldsEinsatzart, $hideFields, $this->form)) {
			echo '<div class="card mb-4"><div class="card-header fw-bold">Fahrzeug & Einheit</div><div class="card-body"><div class="row">';
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

		// Abschnitt: Beschreibung & Bilder
		$fieldsBericht = ['beschreibung', 'bild_url', 'url'];
		if (hasVisibleFields($fieldsBericht, $hideFields, $this->form)) {
			echo '<div class="card mb-4"><div class="card-header fw-bold">Beschreibung & Bilder</div><div class="card-body"><div class="row">';
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
		?>
	<!-- Versteckte Felder für die Formularverarbeitung -->
	<input type="hidden" name="task" value="einsatzfahrzeug.save">
	<?php echo HTMLHelper::_('form.token'); // CSRF-Schutz
	?>
</form>
