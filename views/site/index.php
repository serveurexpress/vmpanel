<?php
use kartik\icons\Icon;
use kartik\helpers\Html;

Icon::map($this);
$this->title = 'VMpanel';
?>
<div class="site-index">
    <div class="body-content">
        <?php
        $result = "";
        foreach ($vmlist as &$vm) {
            $result = '
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">'.ucfirst($vm).'</h3>
                    </div>
                    <div class="panel-body">
                        Les graphs ici
                    </div>
                    <div class="panel-footer">
                       <a href="#" class="btn btn-default">'.Icon::show('play').'</a>'
                    . '<a href="#" class="btn btn-default">'.Icon::show('pause').'</a>'
                    . '<a href="#" class="btn btn-default">'.Icon::show('stop').'</a>'
                    . '<a href="#" class="btn btn-default">'.Icon::show('refresh').'</a>'
                    . '<a href="#" class="btn btn-default">'.Icon::show('search').'</a>
                    </div>
                </div>';
            echo $result;
        }
        ?>
    </div>
</div>
