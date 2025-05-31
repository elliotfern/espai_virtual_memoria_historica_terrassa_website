<?php require_once APP_ROOT . '/public/intranet/includes/header.php'; ?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <h2>Gestió de bases de dades auxiliars:</h2>

    <ul>
        <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-municipis">Taula llistat de municipis</a></li>
        <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-comarques">Taula llistat de comarques</a></li>
        <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-provincies">Taula llistat de províncies</a></li>
        <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-comunitats">Taula llistat de comunitats autònomes</a></li>
        <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-estats">Taula llistat de països</a></li>
        <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-partits-politics">Taula llistat de partits polítics</a></li>
        <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-sindicats">Taula llistat de sindicats</a></li>
    </ul>
    <div id="isAdminButton" style="display: none;margin-top:25px">
        <?php if (isUserAdmin()) : ?>
            <p><strong>Només visible per usuaris administratius:</strong></p>
            <h2>1. Autenticació i control d'accés</h2>
            <ul>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-usuaris">Taula usuaris registrats</a></li>
                <li>auth_users_control_acces</li>
                <li>auth_users_password_resets</li>
                <li>auth_users_tipus</li>
            </ul>

            <h2>2. Auxiliars</h2>
            <ul>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-imatges">Taula llistat imatges</a></li>
            </ul>

            <h2>3. Taules auxiliars (catàlegs i opcions)</h2>
            <ul>
                <li>aux_activitat_guerra</li>
                <li>aux_acusacions</li>
                <li>aux_bandol</li>
                <li>aux_categoria</li>
                <li>aux_causa_defuncio</li>
                <li>aux_condicio</li>
                <li>aux_cossos_militars</li>
                <li>aux_cronologia_area</li>
                <li>aux_cronologia_mes</li>
                <li>aux_cronologia_tema</li>

                <li>aux_espai</li>
                <li>aux_estat_civil</li>
                <li>aux_estudis</li>
                <li>aux_familiars</li>
                <li>aux_familiars_relacio</li>
                <li>aux_imatges</li>
                <li>aux_jutjats</li>
                <li>aux_llocs_bombardeig</li>
                <li>aux_oficis</li>
                <li>aux_ofici_carrec</li>
                <li>aux_procediment_judicial</li>
                <li>aux_sector_economic</li>
                <li>aux_sentencies</li>
                <li>aux_situacions_deportats</li>
                <li>aux_sub_sector_economic</li>
                <li>aux_tipologia_espais</li>
                <li>aux_tipus_presons</li>
            </ul>

            <h2>3. Control de canvis</h2>
            <ul>
                <li>control_registre_canvis</li>
            </ul>

            <h2>4.Fonts bibliogràfiques i arxius</h2>
            <ul>
                <li>aux_bibliografia_arxius</li>
                <li>aux_bibliografia_arxius_codis</li>
                <li>aux_bibliografia_llibres</li>
                <li>aux_bibliografia_llibre_detalls</li>
            </ul>

            <h2>4. Dades principals i biogràfiques</h2>
            <ul>
                <li>db_afusellats</li>
                <li>db_biografies</li>
                <li>db_cost_huma_morts_civils</li>
                <li>db_cost_huma_morts_front</li>
                <li>db_cronologia</li>
                <li>db_dades_personals</li>
                <li>db_deportats</li>
                <li>db_depurats</li>
                <li>db_exiliats</li>
            </ul>
        <?php endif; ?>
    </div>
</div>