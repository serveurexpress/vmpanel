<?php

use kartik\icons\Icon;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;
use kartik\widgets\AlertBlock;
use app\components\se;

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
            $status = se::getStatus($vm);
            if ($status == '1') {
                $status = '<i class="fa fa-power-off poweron"></i>';
                $startStatus = 'disabled';
            } else {
                $status = '<i class="fa fa-power-off poweroff"></i>';
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
            // Vérification si une action est en cours
            $list = glob(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-*');
            // Une action est en cours
            if (count($list) > 0) {
                $buttonsMenu = '<div class="row hidden">' . $buttons . '</div>' .
                        ' <div class="progress">
                            <div id="progress-' . $vm . '" class="progress-bar progress-bar-striped active" role="progressbar"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                            </div>
                          </div>';
                $actionMenu = '<div id="logs' . $vm . '" class="panel-footer"><div class="row"><div class="col-md-12"><label class="control-label">Une action est en cours</label>' . Html::textarea($vm . 'ActionResult', $log, ['id' => $vm . 'ActionResult', 'class' => 'form-control', 'rows' => '6']) . '</div></div></div>';
                $buttonsGraphLog = '<button id = "btnGraph' . $vm . '" type="button" class="btn btn-info hidden" data-toggle="collapse" data-target="#graph' . $vm . '">
                                <span class="glyphicon glyphicon-collapse-down"></span> ' . Icon::show('area-chart') . '
                            </button>
                            <button id = "btnLogs' . $vm . '" type="button" class="btn btn-default hidden" data-toggle="collapse" data-target="#logs' . $vm . '">
                                <span class="glyphicon glyphicon-collapse-down"></span> ' . Icon::show('files-o') . '
                            </button>';
                $this->registerJs('$(document).ready(function(){
                    
                        intervalID' . $vm . ' = setInterval(function(){
                            if ($("#progress-' . $vm . '").length > 0) {
                                $.pjax.reload({container:"#pjax-' . $vm . '"});
                            } else {
                                clearInterval(intervalID' . $vm . ');
                            }
                        },'.Yii::$app->params['refreshDelay'].');
              });', View::POS_END);
            } else {
                $buttonsMenu = '<div class="row">' . $buttons . '</div>';
                $actionMenu = '<div id="logs' . $vm . '" class="panel-footer collapse"><div class="row"><div class="col-md-12"><label class="control-label">Dernières actions</label>' . Html::textarea($vm . 'ActionResult', $log, ['id' => $vm . 'ActionResult', 'class' => 'form-control', 'rows' => '6']) . '</div></div>'
                        . '<div class="row"><div class="col-md-12"><label class="control-label">Dernières erreurs</label>' . Html::textarea($vm . 'ActionResult', $err, ['id' => $vm . 'ActionResult', 'class' => 'form-control', 'rows' => '6']) . '</div></div></div>';
                $buttonsGraphLog = '<button id = "btnGraph' . $vm . '" type="button" class="btn btn-info" data-toggle="collapse" data-target="#graph' . $vm . '">
                                <span class="glyphicon glyphicon-collapse-down"></span> ' . Icon::show('area-chart') . '
                            </button>
                            <button id = "btnLogs' . $vm . '" type="button" class="btn btn-default" data-toggle="collapse" data-target="#logs' . $vm . '">
                                <span class="glyphicon glyphicon-collapse-down"></span> ' . Icon::show('files-o') . '
                            </button>';
            }
            $imgEthDaily = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-daily.png';
            $imgEthWeekly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-weekly.png';
            $imgEthMonthly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-monthly.png';
            $imgEthYearly = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-yearly.png';
            if (se::checkRemoteFile($imgEthDaily)) {
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
                                <div class="col-md-12 text-center">
                                    <h4>' . ucfirst($vm) . '</h4> 
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">' . $buttonsGraphLog . '</div>
                            <div class="col-md-4 text-center">' . $buttonsMenu . '</div>
                            <div class="col-md-4 text-right">' . $status . ' </div>
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