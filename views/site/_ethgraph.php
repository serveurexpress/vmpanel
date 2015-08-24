<?php
$imgEthDaily = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-daily.png';
$imgEthWeekly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-weekly.png';
$imgEthMonthly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-monthly.png';
$imgEthYearly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-yearly.png';
?>
<p><img class="img-responsive" src="<?= $imgEthDaily ?>"></p>
<p><img class="img-responsive" src="<?= $imgEthWeekly ?>"></p>
<p><img class="img-responsive" src="<?= $imgEthMonthly ?>"></p>
<p><img class="img-responsive" src="<?= $imgEthYearly ?>"></p>