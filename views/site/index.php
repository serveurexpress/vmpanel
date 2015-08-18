<?php
/* @var $this yii\web\View */

$this->title = 'VMpanel';
$username = Yii::$app->user->identity->username;
$vmlist = Yii::$app->user->identity->vmList;
?>
<div class="site-index">
    <div class="body-content">
        <h1>Bienvenue <?= ucfirst($username) ?></h1>

    </div>
</div>
