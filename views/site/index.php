<?php

use kartik\icons\Icon;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\web\View;

function checkRemoteFile($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (curl_exec($ch) !== FALSE) {
        return true;
    } else {
        return false;
    }
}

$this->title = 'VMpanel';
?>
<div class="site-index">
    <div class="body-content">
        <?php
        $result = "";
        foreach ($vmlist as &$vm) {
            $startStatus = '';
            $stopStatus = '';
            $log = '';
            $imgEth = '';
            $err = '';
            $status = trim(shell_exec('sudo ' . Yii::$app->params['scriptDir'] . Yii::$app->params['scriptStatus'] . ' ' . $vm));
            if ($status == '1') {
                $status = true;
                $startStatus = 'disabled';
            } else {
                $status = false;
                $stopStatus = 'disabled';
            }
            if (file_exists(Yii::$app->params['logDir'] . '/' . $vm . '.log')) {
                $log = file_get_contents(Yii::$app->params['logDir'] . '/' . $vm . '.log');
            }
            if (file_exists(Yii::$app->params['logDir'] . '/' . $vm . '.err')) {
                $err = file_get_contents(Yii::$app->params['logDir'] . '/' . $vm . '.err');
            }
            $buttonMenu = '<a href="/index.php?vm=' . $vm . '&action=start" class="btn btn-default" title="Démarrer" ' . $startStatus . '>' . Icon::show('play') . '</a>'
                    . '<a href="/index.php?vm=' . $vm . '&action=stop" class="btn btn-default" title="Arrêter" ' . $stopStatus . '>' . Icon::show('stop') . '</a>'
                    . '<a href="/index.php?vm=' . $vm . '&action=restart" class="btn btn-default" title="Relancer" ' . $stopStatus . '>' . Icon::show('refresh') . '</a>'
                    . '<a href="/index.php?vm=' . $vm . '&action=fsck" class="btn btn-default" title="Fsck" ' . $startStatus . '>' . Icon::show('search') . '</a>';
            $actionMenu = '<div class="row"><div class="col-md-12">' . $buttonMenu . '</div></div><br />';
            $actionMenu .= '<div class="row"><div class="col-md-12"><label class="control-label">Dernières actions</label>' . Html::textarea($vm . 'ActionResult', $log, ['id' => $vm . 'ActionResult', 'class' => 'form-control', 'rows' => '6']) . '</div></div>';
            $actionMenu .= '<div class="row"><div class="col-md-12"><label class="control-label">Dernières erreurs</label>' . Html::textarea($vm . 'ActionResult', $err, ['id' => $vm . 'ActionResult', 'class' => 'form-control', 'rows' => '6']) . '</div></div>';
            $list = glob(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-*');
            if (count($list) > 0) {
                $actionMenu = '<div class="row hidden"><div class="col-md-12">' . $buttonMenu . '</div></div><br />';
                $actionMenu .= '<div class="row"><div class="col-md-12"><label class="control-label">Une action est en cours</label>' . Html::textarea($vm . 'ActionResult', '', ['id' => $vm . 'ActionResult', 'class' => 'form-control', 'rows' => '6']) . '</div></div>';
            }
            $imgEthDaily = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-daily.png';
            $imgEthWeekly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-weekly.png';
            $imgEthMonthly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-monthly.png';
            $imgEthYearly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-yearly.png';
            if (checkRemoteFile($imgEthDaily)) {
                $imgEth = '<div class="row">
                        <div class="col-md-6 col-lg-6"><img class="img-responsive" src="' . $imgEthDaily . '"></div>
                        <div class="col-md-6 col-lg-6"><img class="img-responsive" src="' . $imgEthWeekly . '"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6"><img class="img-responsive" src="' . $imgEthMonthly . '"></div>
                        <div class="col-md-6 col-lg-6"><img class="img-responsive" src="' . $imgEthYearly . '"></div>
                    </div>';
            }
            $result = '
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                            <h3 class="panel-title"><label class="control-label">' . ucfirst($vm) . '</label></h3>  
                            </div>
                            <div class="col-md-6 text-right">
                            <div class="row"><div class="col-md-6"><button id = "btnGraph'.$vm.'" type="button" class="btn btn-info" data-toggle="collapse" data-target="#graph' . $vm . '">
                                <span class="glyphicon glyphicon-collapse-down"></span> ' . Icon::show('area-chart') . '
                              </button></div><div class="col-md-6">' . SwitchInput::widget([
                        'name' => 'status_1',
                        'value' => $status,
                        'disabled' => true,
                        'pluginOptions' => [
                            'size' => 'small',
                            'onColor' => 'success',
                            'offColor' => 'danger',
                        ]
                    ]) . '</div></div>
                            </div>
                            </div>
                    </div>
                    <div id="graph' . $vm . '" class="panel-body collapse">
                    ' . $imgEth . '
                    </div>
                    <div class="panel-footer">
                       ' . $actionMenu . '
                    </div>
                </div>';
            echo $result;
            $this->registerJs('$(document).ready(function(){
                $("#graph' . $vm . '").on("hide.bs.collapse", function(){
                  $("#btnGraph'.$vm.'").html(\'<span class="glyphicon glyphicon-collapse-down"></span>  ' . Icon::show('area-chart') . '\');
                });
                $("#graph' . $vm . '").on("show.bs.collapse", function(){
                  $("#btnGraph'.$vm.'").html(\'<span class="glyphicon glyphicon-collapse-up"></span>  ' . Icon::show('area-chart') . '\');
                });
              });', View::POS_END);
        }
        ?>
    </div>
</div>