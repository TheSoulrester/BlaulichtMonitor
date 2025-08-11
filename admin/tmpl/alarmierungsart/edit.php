<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

// Initialisiere die Formularvalidierung und Keepalive-Verhalten für das Backend-Formular
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

?>

<form action="<?php echo Route::_('index.php?option=com_blaulichtmonitor&view=alarmierungsart&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="alarmierungsart-form" class="form-validate">
	<div class="container">
		<div class="card mb-4">
			<div class="card-header fw-bold">Alarmierungsart</div>
			<div class="card-body">
				<div class="row">
					<?php
					// Nur sichtbare Felder (keine Hidden Fields) mit Abstand ausgeben
					foreach ($this->form->getFieldset() as $field) {
						if ($field->hidden) {
							continue;
						}
						echo '<div class="mb-3">';
						echo '<label class="form-label" for="' . $field->id . '">' . $field->label . '</label>';
						echo $field->input;
						if ($field->description) {
							echo '<small class="form-text text-muted">' . $field->description . '</small>';
						}
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<!-- Versteckte Felder für die Formularverarbeitung -->
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); // CSRF-Schutz
	?>
</form>
