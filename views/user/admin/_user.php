<?php

/**
 * @var yii\widgets\ActiveForm    $form
 * @var dektrium\user\models\User $user
 */

?>

<?= $form->field($user, 'username')->textInput(['maxlength' => 25]) ?>
<?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'password')->passwordInput() ?>
<?= $form->field($user, 'vmlist')->textInput(['maxlength' => 255]) ?>
<div class="form-group">
<label class="control-label col-sm-12">VMLIST : liste de nom de machine séparé par des virgules sans espace (EX : t134,t135,t136)</label>
</div>