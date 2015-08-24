<?php

use kartik\icons\Icon;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;
use kartik\widgets\AlertBlock;

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
            $buttons = '<a href="/index.php?vm=' . $vm . '&action=start" class="btn btn-default" title="Démarrer" ' . $startStatus . '>' . Icon::show('play') . '</a>'
                    . '<a href="/index.php?vm=' . $vm . '&action=stop" class="btn btn-default" title="Arrêter" ' . $stopStatus . '>' . Icon::show('stop') . '</a>'
                    . '<a href="/index.php?vm=' . $vm . '&action=restart" class="btn btn-default" title="Relancer" ' . $stopStatus . '>' . Icon::show('refresh') . '</a>'
                    . '<a href="/index.php?vm=' . $vm . '&action=fsck" class="btn btn-default" title="Fsck" ' . $startStatus . '>' . Icon::show('search') . '</a>';
            $buttonsMenu = '<div class="row">' . $buttons . '</div>';
            $actionMenu = '<div id="logs' . $vm . '" class="panel-footer collapse"><div class="row"><div class="col-md-12"><label class="control-label">Dernières actions</label>' . Html::textarea($vm . 'ActionResult', $log, ['id' => $vm . 'ActionResult', 'class' => 'form-control', 'rows' => '6']) . '</div></div>'
                    . '<div class="row"><div class="col-md-12"><label class="control-label">Dernières erreurs</label>' . Html::textarea($vm . 'ActionResult', $err, ['id' => $vm . 'ActionResult', 'class' => 'form-control', 'rows' => '6']) . '</div></div></div>';
            // Vérification si une action est en cours
            $list = glob(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-*');
            // Une action est en cours
            if (count($list) > 0) {
                $buttonsMenu = '<div class="row hidden">' . $buttons . '</div>' .
                        ' <div class="progress">
                            <div id="progress-' . $vm . '" class="progress-bar progress-bar-striped active" role="progressbar"
                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:10%">
                              10%
                            </div>
                          </div>';
                $actionMenu = '<div id="logs' . $vm . '" class="panel-footer"><div class="row"><div class="col-md-12"><label class="control-label">Une action est en cours</label>' . Html::textarea($vm . 'ActionResult', $log, ['id' => $vm . 'ActionResult', 'class' => 'form-control', 'rows' => '6']) . '</div></div></div>';
                $this->registerJs('$(document).ready(function(){
                    if ($("#progress-' . $vm . '").length > 0) {
                        timeoutID' . $vm . ' = setTimeout(function(){
                            $.pjax.reload({container:"#pjax-' . $vm . '"});
                        },2000);
                    } else {
                        clearTimeout(timeoutID' . $vm . ');
                    }
              });', View::POS_END);
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
                            <div class="col-md-4">
                            <label class="control-label panel-title">' . ucfirst($vm) . '</label> 
                            <button id = "btnGraph' . $vm . '" type="button" class="btn btn-info" data-toggle="collapse" data-target="#graph' . $vm . '">
                                <span class="glyphicon glyphicon-collapse-down"></span> ' . Icon::show('area-chart') . '
                            </button>
                            <button id = "btnLogs' . $vm . '" type="button" class="btn btn-default" data-toggle="collapse" data-target="#logs' . $vm . '">
                                <span class="glyphicon glyphicon-collapse-down"></span> ' . Icon::show('files-o') . '
                            </button>
                            </div>
                            <div class="col-md-4 text-center">' . $buttonsMenu . '</div>
                            <div class="col-md-4 text-right">' . SwitchInput::widget([
                        'name' => 'status_1',
                        'value' => $status,
                        'disabled' => true,
                        'pluginOptions' => [
                            'size' => 'small',
                            'onColor' => 'success',
                            'offColor' => 'danger',
                        ],
                        'containerOptions' => [
                            'class' => 'noForm',
                        ],
                    ]) . '
                            </div>
                            </div>
                    </div>
                    <div id="graph' . $vm . '" class="panel-body collapse">
                    ' . $imgEth . '
                    </div>
                    ' . $actionMenu . '
                </div>';
            Pjax::begin(['id' => 'pjax-' . $vm]);
            echo AlertBlock::widget([
                'type' => AlertBlock::TYPE_ALERT,
                'useSessionFlash' => true,
                'delay' => false
            ]);
            echo $result;
            Pjax::end(['id' => 'pjax-' . $vm]);
            $this->registerJs('$(document).ready(function(){
                $("#graph' . $vm . '").on("hide.bs.collapse", function(){
                  $("#btnGraph' . $vm . '").html(\'<span class="glyphicon glyphicon-collapse-down"></span>  ' . Icon::show('area-chart') . '\');
                });
                $("#graph' . $vm . '").on("show.bs.collapse", function(){
                  $("#btnGraph' . $vm . '").html(\'<span class="glyphicon glyphicon-collapse-up"></span>  ' . Icon::show('area-chart') . '\');
                });
                
                $("#logs' . $vm . '").on("hide.bs.collapse", function(){
                  $("#btnLogs' . $vm . '").html(\'<span class="glyphicon glyphicon-collapse-down"></span>  ' . Icon::show('files-o') . '\');
                });
                $("#logs' . $vm . '").on("show.bs.collapse", function(){
                  $("#btnLogs' . $vm . '").html(\'<span class="glyphicon glyphicon-collapse-up"></span>  ' . Icon::show('files-o') . '\');
                });
              });', View::POS_END);
        }
        ?>
    </div>
</div>