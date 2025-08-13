<?php
// Ajusta la ruta si tu dist está en otro sitio:
$bundlePath = __DIR__ . '/../../dist/bundle.js';

// Si no existiera por algún motivo, usa '1' como fallback
$ver = file_exists($bundlePath) ? filemtime($bundlePath) : 1;
?>
<script src="/dist/bundle.js?v=<?= $ver ?>"></script>
</body>