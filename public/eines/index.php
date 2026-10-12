<?php

declare(strict_types=1);

require_once __DIR__ . '../../../vendor/autoload.php';

use Dotenv\Dotenv;

$basePath = __DIR__ . '/../..';

if (file_exists($basePath . '/.env')) {
    Dotenv::createImmutable($basePath)->load();
}

session_start();

/*
|--------------------------------------------------------------------------
| CONFIGURACIÓ
|--------------------------------------------------------------------------
*/

const DIRECTORI_BASE =
'/home/elliot/Imatges/01.Expedients_consells-guerra';

const DIRECTORI_SCRIPTS =
'/var/www/memoriaterrassa_cat/scripts_pdf';

const PYTHON = '/usr/bin/python3';

const MAX_SORTIDA = 30000;

/*
|--------------------------------------------------------------------------
| AUTENTICACIÓ
|--------------------------------------------------------------------------
|
| Defineix EVMHT_EINES_USER i EVMHT_EINES_PASSWORD a la configuració
| del servidor web. Si no estan configurats, l'eina queda bloquejada.
|
*/

$usuariAutoritzat = $_ENV['EVMHT_EINES_USER'] ?? '';
$contrasenyaAutoritzada = $_ENV['EVMHT_EINES_PASSWORD'] ?? '';

if (
    $usuariAutoritzat === false ||
    $contrasenyaAutoritzada === false ||
    $usuariAutoritzat === '' ||
    $contrasenyaAutoritzada === ''
) {
    http_response_code(503);
    exit('L’eina EVMHT no està configurada.');
}

$usuariIntroduit = $_SERVER['PHP_AUTH_USER'] ?? '';
$contrasenyaIntroduida = $_SERVER['PHP_AUTH_PW'] ?? '';

if (
    !hash_equals($usuariAutoritzat, $usuariIntroduit) ||
    !hash_equals($contrasenyaAutoritzada, $contrasenyaIntroduida)
) {
    header('WWW-Authenticate: Basic realm="EVMHT"');
    http_response_code(401);
    exit('Cal autenticar-se per accedir a aquesta eina.');
}

/*
|--------------------------------------------------------------------------
| FUNCIONS AUXILIARS
|--------------------------------------------------------------------------
*/

function escapar(string $valor): string
{
    return htmlspecialchars(
        $valor,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

function obtenirDirectori(string $nom): string
{
    $base = realpath(DIRECTORI_BASE);

    if ($base === false || !is_dir($base)) {
        throw new RuntimeException(
            'El directori base no existeix o no és accessible.'
        );
    }

    if (
        $nom === '' ||
        $nom === '.' ||
        $nom === '..' ||
        str_contains($nom, '/') ||
        str_contains($nom, '\\') ||
        preg_match('/[\x00-\x1F\x7F]/', $nom) === 1
    ) {
        throw new InvalidArgumentException(
            'El nom del directori no és vàlid.'
        );
    }

    $directori = realpath($base . DIRECTORY_SEPARATOR . $nom);

    if (
        $directori === false ||
        !is_dir($directori) ||
        dirname($directori) !== $base
    ) {
        throw new InvalidArgumentException(
            'El directori indicat no existeix dins del directori base.'
        );
    }

    return $directori;
}

/**
 * Executa Python sense passar les dades del formulari a través del shell.
 *
 * @param list<string> $ordre
 * @return array{codi:int, sortida:string, error:string}
 */
function executarPython(
    array $ordre,
    string $entrada = ''
): array {
    $fitxerErrors = tempnam(sys_get_temp_dir(), 'evmht_err_');

    if ($fitxerErrors === false) {
        throw new RuntimeException(
            'No s’ha pogut crear el fitxer temporal d’errors.'
        );
    }

    try {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['file', $fitxerErrors, 'w'],
        ];

        $procés = proc_open(
            $ordre,
            $descriptors,
            $pipes,
            null,
            null,
            ['bypass_shell' => true]
        );

        if (!is_resource($procés)) {
            throw new RuntimeException(
                'No s’ha pogut iniciar el procés Python.'
            );
        }

        try {
            if ($entrada !== '') {
                fwrite($pipes[0], $entrada);
            }

            fclose($pipes[0]);

            $sortida = stream_get_contents($pipes[1]);

            fclose($pipes[1]);

            $codi = proc_close($procés);
            $errors = file_get_contents($fitxerErrors);

            return [
                'codi' => $codi,
                'sortida' => $sortida === false ? '' : $sortida,
                'error' => $errors === false ? '' : $errors,
            ];
        } catch (Throwable $error) {
            if (is_resource($pipes[0])) {
                fclose($pipes[0]);
            }

            if (is_resource($pipes[1])) {
                fclose($pipes[1]);
            }

            proc_terminate($procés);
            proc_close($procés);

            throw $error;
        }
    } finally {
        if (is_file($fitxerErrors)) {
            unlink($fitxerErrors);
        }
    }
}

function mostrarResultat(
    string $operacio,
    array $resultat
): void {
    $correcte = $resultat['codi'] === 0;

    echo '<div class="alert '
        . ($correcte ? 'alert-success' : 'alert-danger')
        . '">';

    echo '<h3 class="h6">'
        . escapar($operacio)
        . ': '
        . ($correcte ? 'completat' : 'error')
        . '</h3>';

    $text = trim(
        $resultat['sortida'] . "\n" . $resultat['error']
    );

    if ($text !== '') {
        if (strlen($text) > MAX_SORTIDA) {
            $text = substr($text, 0, MAX_SORTIDA)
                . "\n... Sortida truncada.";
        }

        echo '<pre class="small text-break mb-0" '
            . 'style="white-space:pre-wrap">'
            . escapar($text)
            . '</pre>';
    }

    echo '</div>';
}

/*
|--------------------------------------------------------------------------
| DESCÀRREGA SEGURA DELS PDF
|--------------------------------------------------------------------------
*/

if (isset($_GET['descarregar'], $_GET['directori'])) {
    try {
        $nomDirectori = (string) $_GET['directori'];
        $nomFitxer = (string) $_GET['descarregar'];

        $directori = obtenirDirectori($nomDirectori);

        $nomsPermesos = [
            $nomDirectori . '.pdf',
            $nomDirectori . '_comprimido.pdf',
        ];

        if (!in_array($nomFitxer, $nomsPermesos, true)) {
            throw new InvalidArgumentException(
                'El nom del PDF no és vàlid.'
            );
        }

        $fitxer = realpath(
            $directori . DIRECTORY_SEPARATOR . $nomFitxer
        );

        if (
            $fitxer === false ||
            dirname($fitxer) !== $directori ||
            !is_file($fitxer) ||
            !is_readable($fitxer)
        ) {
            throw new RuntimeException(
                'El PDF no existeix o no es pot llegir.'
            );
        }

        header('Content-Type: application/pdf');
        header(
            'Content-Disposition: attachment; filename="'
                . $nomFitxer
                . '"'
        );
        header('Content-Length: ' . filesize($fitxer));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');

        readfile($fitxer);
        exit;
    } catch (Throwable $error) {
        http_response_code(404);
        exit('No s’ha pogut descarregar el PDF.');
    }
}

/*
|--------------------------------------------------------------------------
| TOKEN CSRF
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['evmht_csrf']) ||
    !is_string($_SESSION['evmht_csrf'])
) {
    $_SESSION['evmht_csrf'] = bin2hex(random_bytes(32));
}

$csrf = $_SESSION['evmht_csrf'];

/*
|--------------------------------------------------------------------------
| PROCESSAMENT DEL FORMULARI
|--------------------------------------------------------------------------
*/

$directoriIntroduit = '';
$nomProcessat = '';
$numeroCausa = '';

$seleccionades = [
    'renombrar' => false,
    'pdf' => false,
    'comprimir' => false,
];

$resultats = [];
$errorsFormulari = [];
$directoriTreball = null;
$nomPdf = '';
$nomPdfComprimit = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $directoriIntroduit = trim(
        (string) ($_POST['directori'] ?? '')
    );

    $nomProcessat = trim(
        (string) ($_POST['nom_processat'] ?? '')
    );

    $numeroCausa = trim(
        (string) ($_POST['numero_causa'] ?? '')
    );

    $operacionsRebudes = $_POST['operacions'] ?? [];

    if (!is_array($operacionsRebudes)) {
        $operacionsRebudes = [];
    }

    $seleccionades = [
        'renombrar' => in_array(
            'renombrar',
            $operacionsRebudes,
            true
        ),
        'pdf' => in_array('pdf', $operacionsRebudes, true),
        'comprimir' => in_array(
            'comprimir',
            $operacionsRebudes,
            true
        ),
    ];

    if (
        !is_string($_POST['csrf'] ?? null) ||
        !hash_equals($csrf, $_POST['csrf'])
    ) {
        $errorsFormulari[] =
            'La sessió del formulari ha caducat. Torna-ho a provar.';
    }

    if (!array_filter($seleccionades)) {
        $errorsFormulari[] =
            'Selecciona almenys una operació.';
    }

    if ($nomProcessat === '') {
        $errorsFormulari[] =
            'El nom del processat és obligatori.';
    }

    if ($numeroCausa === '') {
        $errorsFormulari[] =
            'El número de causa és obligatori.';
    }

    if (
        preg_match('/[\x00-\x1F\x7F]/', $nomProcessat) === 1 ||
        preg_match('/[\x00-\x1F\x7F]/', $numeroCausa) === 1
    ) {
        $errorsFormulari[] =
            'Els camps de text contenen caràcters no permesos.';
    }

    try {
        $directoriTreball = obtenirDirectori($directoriIntroduit);
    } catch (Throwable $error) {
        $errorsFormulari[] = $error->getMessage();
    }

    $nomPdf = $directoriIntroduit . '.pdf';
    $nomPdfComprimit = $directoriIntroduit . '_comprimido.pdf';

    /*
    |--------------------------------------------------------------------------
    | VALIDACIONS PRÈVIES
    |--------------------------------------------------------------------------
    */

    if (
        $directoriTreball !== null &&
        !$errorsFormulari
    ) {
        $rutaPdf = $directoriTreball
            . DIRECTORY_SEPARATOR
            . $nomPdf;

        $rutaPdfComprimit = $directoriTreball
            . DIRECTORY_SEPARATOR
            . $nomPdfComprimit;

        if ($seleccionades['pdf'] && file_exists($rutaPdf)) {
            $errorsFormulari[] =
                'Ja existeix el PDF original. Mou-lo o elimina’l '
                . 'abans de tornar-lo a generar.';
        }

        if (
            $seleccionades['comprimir'] &&
            file_exists($rutaPdfComprimit)
        ) {
            $errorsFormulari[] =
                'Ja existeix el PDF comprimit. Mou-lo o elimina’l '
                . 'abans de tornar-lo a generar.';
        }

        if (
            $seleccionades['comprimir'] &&
            !$seleccionades['pdf'] &&
            !is_file($rutaPdf)
        ) {
            $errorsFormulari[] =
                'No existeix el PDF original que vols comprimir.';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXECUCIÓ SEQÜENCIAL
    |--------------------------------------------------------------------------
    */

    if (
        $directoriTreball !== null &&
        !$errorsFormulari
    ) {
        $scriptRenombrar = DIRECTORI_SCRIPTS
            . '/1_noms_imatges_data.py';

        $scriptPdf = DIRECTORI_SCRIPTS
            . '/2_crear_pdf_documents.py';

        $scriptComprimir = DIRECTORI_SCRIPTS
            . '/3_comprimir_pdf.py';

        foreach (
            [
                $scriptRenombrar,
                $scriptPdf,
                $scriptComprimir,
            ] as $script
        ) {
            if (!is_file($script) || !is_readable($script)) {
                $errorsFormulari[] =
                    'Falta un script Python o no es pot llegir: '
                    . basename($script);
            }
        }

        if (!is_executable(PYTHON)) {
            $errorsFormulari[] =
                'Python no està disponible a la ruta configurada.';
        }

        if (!function_exists('proc_open')) {
            $errorsFormulari[] =
                'La funció proc_open() no està disponible a PHP.';
        }

        if (!$errorsFormulari) {
            try {
                if ($seleccionades['renombrar']) {
                    $resultat = executarPython(
                        [
                            PYTHON,
                            $scriptRenombrar,
                            $directoriTreball,
                            '--aplicar',
                        ]
                    );

                    mostrarResultat(
                        '1. Renombrar les imatges',
                        $resultat
                    );

                    if ($resultat['codi'] !== 0) {
                        throw new RuntimeException(
                            'Ha fallat el renombrament. '
                                . 'Les operacions següents no s’executaran.'
                        );
                    }
                }

                if ($seleccionades['pdf']) {
                    $resultat = executarPython(
                        [
                            PYTHON,
                            $scriptPdf,
                            $directoriTreball,
                            '--salida',
                            $rutaPdf,
                            '--ppp',
                            '180',
                            '--calidad',
                            '85',
                            '--margen',
                            '12',
                        ],
                        $nomProcessat . "\n" . $numeroCausa . "\n"
                    );

                    mostrarResultat(
                        '2. Crear el PDF amb portada',
                        $resultat
                    );

                    if (
                        $resultat['codi'] !== 0 ||
                        !is_file($rutaPdf) ||
                        filesize($rutaPdf) === 0
                    ) {
                        throw new RuntimeException(
                            'No s’ha pogut generar el PDF. '
                                . 'La compressió no s’executarà.'
                        );
                    }
                }

                if ($seleccionades['comprimir']) {
                    $resultat = executarPython(
                        [
                            PYTHON,
                            $scriptComprimir,
                            $rutaPdf,
                            '--sortida',
                            $rutaPdfComprimit,
                        ]
                    );

                    mostrarResultat(
                        '3. Comprimir el PDF',
                        $resultat
                    );

                    if (
                        $resultat['codi'] !== 0 ||
                        !is_file($rutaPdfComprimit) ||
                        filesize($rutaPdfComprimit) === 0
                    ) {
                        throw new RuntimeException(
                            'No s’ha pogut comprimir el PDF.'
                        );
                    }
                }
            } catch (Throwable $error) {
                $errorsFormulari[] = $error->getMessage();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>EVMHT · Processament d'expedients</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #f5f6f8;
        }

        .contenidor {
            max-width: 850px;
        }

        .capcalera {
            background: #133b7c;
            color: #fff;
        }

        pre {
            max-height: 350px;
            overflow: auto;
        }
    </style>
</head>

<body>

    <main class="container contenidor py-5">

        <header class="capcalera rounded-3 p-4 mb-4">
            <h1 class="h3 mb-2">
                EVMHT · Processament d'expedients
            </h1>

            <p class="mb-0">
                Renombrament d'imatges i generació de documents PDF.
            </p>
        </header>

        <?php if ($errorsFormulari): ?>
            <div class="alert alert-danger" role="alert">
                <h2 class="h6">No s'ha pogut completar el procés</h2>

                <ul class="mb-0">
                    <?php foreach ($errorsFormulari as $error): ?>
                        <li><?= escapar($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            !$errorsFormulari
        ): ?>
            <div class="alert alert-success" role="status">
                <strong>Procés completat.</strong>
                Revisa els resultats de les operacions.
            </div>
        <?php endif; ?>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <?php if (
                isset($directoriTreball) &&
                $directoriTreball !== null
            ): ?>
                <section class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Fitxers de l'expedient</h2>

                        <?php foreach (
                            [
                                $nomPdf,
                                $nomPdfComprimit,
                            ] as $fitxerPdf
                        ): ?>
                            <?php
                            $rutaFitxer = $directoriTreball
                                . DIRECTORY_SEPARATOR
                                . $fitxerPdf;
                            ?>

                            <?php if (
                                is_file($rutaFitxer) &&
                                filesize($rutaFitxer) > 0
                            ): ?>
                                <p class="mb-2">
                                    <a
                                        href="?<?= escapar(http_build_query([
                                                    'descarregar' => $fitxerPdf,
                                                    'directori' => $directoriIntroduit,
                                                ])) ?>"
                                        class="link-primary">
                                        <span aria-hidden="true">↓</span>
                                        Descarregar <?= escapar($fitxerPdf) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>

        <section class="card shadow-sm">
            <div class="card-body p-4">

                <form method="post">

                    <input
                        type="hidden"
                        name="csrf"
                        value="<?= escapar($csrf) ?>">

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Directori base
                        </label>

                        <div class="form-control bg-light text-break">
                            <?= escapar(DIRECTORI_BASE) ?>/
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="directori" class="form-label">
                            Nom del directori
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="directori"
                            name="directori"
                            value="<?= escapar($directoriIntroduit) ?>"
                            placeholder="Ex. 1939_000123"
                            maxlength="255"
                            required>

                        <div class="form-text">
                            Escriu només el nom de la carpeta de l'expedient.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nom_processat" class="form-label">
                            Nom del processat
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nom_processat"
                            name="nom_processat"
                            value="<?= escapar($nomProcessat) ?>"
                            maxlength="500"
                            required>
                    </div>

                    <div class="mb-4">
                        <label for="numero_causa" class="form-label">
                            Número de causa
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="numero_causa"
                            name="numero_causa"
                            value="<?= escapar($numeroCausa) ?>"
                            maxlength="255"
                            required>
                    </div>

                    <fieldset class="mb-4">
                        <legend class="h5 mb-3">
                            Operacions que executar
                        </legend>

                        <div class="form-check mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="operacions[]"
                                value="renombrar"
                                id="op_renombrar"
                                <?= $seleccionades['renombrar']
                                    ? 'checked'
                                    : '' ?>>

                            <label
                                class="form-check-label"
                                for="op_renombrar">
                                1. Renombrar les imatges per data
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="operacions[]"
                                value="pdf"
                                id="op_pdf"
                                <?= $seleccionades['pdf']
                                    ? 'checked'
                                    : '' ?>>

                            <label
                                class="form-check-label"
                                for="op_pdf">
                                2. Crear el PDF amb portada
                            </label>
                        </div>

                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="operacions[]"
                                value="comprimir"
                                id="op_comprimir"
                                <?= $seleccionades['comprimir']
                                    ? 'checked'
                                    : '' ?>>

                            <label
                                class="form-check-label"
                                for="op_comprimir">
                                3. Comprimir el PDF
                            </label>
                        </div>
                    </fieldset>

                    <div class="alert alert-warning small">
                        El renombrament modifica els noms de les imatges
                        originals. Els PDF existents no se sobreescriuen.
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                        style="background:#133b7c">
                        Executar les operacions seleccionades
                    </button>

                </form>
            </div>
        </section>

        <footer class="text-center text-secondary small mt-4">
            Espai Virtual de la Memòria Històrica de Terrassa · EVMHT
        </footer>

    </main>

</body>

</html>