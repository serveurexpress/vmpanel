<?php
$imgEthDaily = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-daily.png';
$imgEthWeekly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-weekly.png';
$imgEthMonthly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-monthly.png';
$imgEthYearly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-yearly.png';
?>
<div class="row">
    <div class="col-md-6 col-lg-6"><img class="img-responsive" src="<?= $imgEthDaily ?>"></div>
    <div class="col-md-6 col-lg-6"><img class="img-responsive" src="<?= $imgEthWeekly ?>"></div>
</div>
<div class="row">
    <div class="col-md-6 col-lg-6"><img class="img-responsive" src="<?= $imgEthMonthly ?>"></div>
    <div class="col-md-6 col-lg-6"><img class="img-responsive" src="<?= $imgEthYearly ?>"></div>
</div>'
