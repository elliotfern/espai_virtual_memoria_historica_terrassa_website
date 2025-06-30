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

    <hr>

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

            <hr>

            <h2>2. Auxiliars</h2>
            <ul>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-imatges">Taula llistat imatges</a></li>
            </ul>

            <h4>1. General</h4>
            <ul>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-categories-repressio">Taula llistat categories de repressió</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-causa-mort">Taula llistat causes de defunció</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-espais">Taula llistat d'espais</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-estats-civils">Taula d'estats civils</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-nivells-estudis">Taula d'estudis</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-oficis">Taula d'oficis</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-carrecs-empresa">Taula llistat càrrecs d'empresa</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-subsectors-economics">Taula llistat sub-sectors econòmics</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-sectors-economics">Taula llistat sectors econòmics</a></li>
                <li>aux_tipologia_espais</li>
            </ul>

            <h4>2. Represaliats 1939-1979</h4>
            <h6>Processats</h6>
            <ul>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-acusacions-judicials">Taula llistat d'acusacions judicials</a></li>
                <li>aux_jutjats</li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-tipus-procediments-judicials">Taula llistat tipus procediments judicials</a></li>
                <li>aux_sentencies</li>
                <li>aux_tipus_presons</li>
            </ul>

            <h4>3. Exiliats / Deportats</h4>
            <ul>
                <li>aux_situacions_deportats</li>
            </ul>

            <h4>4. Cost humà guerra civil</h4>
            <ul>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-bandols-guerra">Taula llistat de bàndols Guerra civil</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-condicions-militars">Taula llistat de condicions militars</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['auxiliars']; ?>/llistat-cossos-militars">Taula llistat de cossos militars</a></li>
                <li>aux_llocs_bombardeig</li>
            </ul>

            <h4>5. Cronologia</h4>
            <ul>
                <li>db_cronologia</li>
                <li>aux_cronologia_area</li>
                <li>aux_cronologia_mes</li>
                <li>aux_cronologia_tema</li>
            </ul>

            <h4>6. Relacions familiars</h4>
            <ul>
                <li>aux_familiars</li>
                <li>aux_familiars_relacio</li>
            </ul>

            <h4>7.Fonts bibliogràfiques i arxius</h4>
            <ul>
                <li>aux_bibliografia_arxius</li>
                <li>aux_bibliografia_llibres</li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['fonts']; ?>/llistat-arxius">Taula llistat d'arxius</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['fonts']; ?>/llistat-llibres">Taula llistat de bibliografia</a></li>
            </ul>

            <h4>8. Bases de dades col·lectius repressió</h4>
            <ul>
                <li>db_dades_personals</li>
                <li>db_afusellats</li>
                <li>db_cost_huma_morts_civils</li>
                <li>db_cost_huma_morts_front</li>
                <li>db_deportats</li>
                <li>db_depurats</li>
                <li>db_exiliats</li>
                <li>db_biografies</li>
            </ul>

            <h4>9. Registre i Control de canvis</h4>
            <ul>
                <li>control_registre_canvis</li>
            </ul>

        <?php endif; ?>
    </div>
</div>