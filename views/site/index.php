<?php
use kartik\icons\Icon;
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
                        '.Icon::show('play').'
                    </div>
                </div>';
            echo $result;
        }
        ?>
    </div>
</div>
