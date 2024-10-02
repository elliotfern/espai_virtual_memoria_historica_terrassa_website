<?php
$id = $params['id'];
?>

<div id="info"> </div>

<hr>

<h6><strong>Dades vitals:</strong></h6>
<div class="tab" id="botons1"></div>
<div id="fitxa" class="fitxa-persona"> </div>

<hr>

<h6><strong>Tipus de repressió:</strong></h6>
<div class="tab" id="botons2"></div>
<div id="fitxa-categoria" class="fitxa-persona" style="margin-top:50px;display:none"> </div>

</div>

<?php
# footer
require_once(APP_ROOT . APP_DEV . '/public/php/footer.php');