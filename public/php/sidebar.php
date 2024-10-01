<style>
    .links a {
        color:white!important;
    }
    </style>
<div class="d-flex flex-column flex-shrink-0 p-3 links">
    <a href="/inici/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none links-sidebar">
        <h6>Espai Virtual de la Memòria Històrica de Terrassa - EVMHT</h6>
    <?php
    //<img src="/public/inc/img/logo3.png" alt="Logo" class="d-block mx-auto" width="150" height="80">
    ?>
    </a>
    <hr class="text-white">

    <span class="d-flex align-items-center text-decoration-none">
        <strong>
            <div id="userDiv" style="color:white"></div>
        </strong>
    </span>
   
    <nav class="navbar navbar-expand-lg bg-body-tertiary navbar-nav-scroll" style="display:block;background-color:#000000 !important;">
		<button class="navbar-toggler text-center" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon text-center"></span>
		</button>

    <div class="collapse navbar-collapse" id="navbarTogglerDemo01" style="background-color:#000000 !important;">

    <?php
    // Obtiene la URL actual
    $current_url = $_SERVER['REQUEST_URI'];

    // Define un array con los enlaces y sus detalles correspondientes
    $icon_bd = "M4.318 2.687C5.234 2.271 6.536 2 8 2s2.766.27 3.682.687C12.644 3.125 13 3.627 13 4c0 .374-.356.875-1.318 1.313C10.766 5.729 9.464 6 8 6s-2.766-.27-3.682-.687C3.356 4.875 3 4.373 3 4c0-.374.356-.875 1.318-1.313M13 5.698V7c0 .374-.356.875-1.318 1.313C10.766 8.729 9.464 9 8 9s-2.766-.27-3.682-.687C3.356 7.875 3 7.373 3 7V5.698c.271.202.58.378.904.525C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777A5 5 0 0 0 13 5.698M14 4c0-1.007-.875-1.755-1.904-2.223C11.022 1.289 9.573 1 8 1s-3.022.289-4.096.777C2.875 2.245 2 2.993 2 4v9c0 1.007.875 1.755 1.904 2.223C4.978 15.71 6.427 16 8 16s3.022-.289 4.096-.777C13.125 14.755 14 14.007 14 13zm-1 4.698V10c0 .374-.356.875-1.318 1.313C10.766 11.729 9.464 12 8 12s-2.766-.27-3.682-.687C3.356 10.875 3 10.373 3 10V8.698c.271.202.58.378.904.525C4.978 9.71 6.427 10 8 10s3.022-.289 4.096-.777A5 5 0 0 0 13 8.698m0 3V13c0 .374-.356.875-1.318 1.313C10.766 14.729 9.464 15 8 15s-2.766-.27-3.682-.687C3.356 13.875 3 13.373 3 13v-1.302c.271.202.58.378.904.525C4.978 12.71 6.427 13 8 13s3.022-.289 4.096-.777c.324-.147.633-.323.904-.525";

    $links = array(
        "/represaliats" => array(
            "label" => "Llistat complert",
            "url" => "/represaliats",
            "icon" => "bi bi-database",
            "paths" => array(
                $icon_bd
            )
        ),

        "/afusellats" => array(
            "label" => "1. Afusellats",
            "url" => "/afusellats",
            "icon" => "bi bi-database",
            "paths" => array(
                $icon_bd
            )
        ),

        "/deportats" => array(
            "label" => "2. Deportats",
            "url" => "/deportats",
            "icon" => "bi bi-database",
            "paths" => array(
                $icon_bd
            )
        ),

        "/morts-en-combat" => array(
            "label" => "3. Morts en Combat",
            "url" => "/morts-en-combat",
            "icon" => "bi bi-database",
            "paths" => array(
                $icon_bd
            )
        ),

        "/desapareguts" => array(
            "label" => "4. Desapareguts",
            "url" => "/desapareguts",
            "icon" => "bi bi-database",
            "paths" => array(
                $icon_bd
            )
        ),

        "/morts-civils" => array(
            "label" => "5. Morts civils",
            "url" => "/morts-civils",
            "icon" => "bi bi-database",
            "paths" => array(
                $icon_bd
            )
        ),

        "/processats" => array(
            "label" => "6. Processats",
            "url" => "/processats",
            "icon" => "bi bi-database",
            "paths" => array(
                $icon_bd
            )
        ),

        "/empresonats" => array(
            "label" => "7. Empresonats",
            "url" => "/empresonats",
            "icon" => "bi bi-database",
            "paths" => array(
                $icon_bd
            )
        ),

        "/depurats" => array(
            "label" => "8. Depurats",
            "url" => "/depurats",
            "icon" => "bi bi-database",
            "paths" => array(
                $icon_bd
            )
        ),

        "/investigats" => array(
            "label" => "9. Investigats",
            "url" => "/investigats",
            "icon" => "bi bi-database",
            "paths" => array(
                $icon_bd
            )
        ),

        "/exiliats" => array(
            "label" => "10. Exiliats",
            "url" => "/exiliats",
            "icon" => "bi bi-database",
            "paths" => array(
                $icon_bd
            )
        ),

    );
?>
    <ul class="nav nav-pills flex-column mb-auto">
        <?php foreach ($links as $url => $data): ?>
            <li class="nav-item">
            <a class="nav-link <?php echo (strpos($current_url, $url) === 0) ? 'active' : ''; ?>" href="<?php echo $data['url']; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-white bi <?php echo $data['icon']; ?> me-2" viewBox="0 0 16 16">
                    <?php foreach ($data['paths'] as $path): ?>
                        <path d="<?php echo $path; ?>"/>
                    <?php endforeach; ?>
                </svg>
                <span class="text-white links-sidebar"><?php echo $data['label']; ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    </nav>

    <hr class="text-white">
     
    <p><a href="#" class="links-sidebar link-sortir" id="btnSortir">Sortir</a></p>
  </div>