<?php require_once APP_ROOT . '/public/intranet/includes/header.php'; ?>

<div class="container">
    <?php if ($isAdmin || $isAutor): ?>
        <h2>Llistat quadre general de víctimes</h2>

        <div class="table-responsive" style="margin-top:30px">
            <table class="table table-striped table-hover" id="represaliatsTable">
                <thead class="table-dark">
                    <tr>
                        <th>Grup repressió</th>
                        <th>Tipus</th>
                        <th>Número</th>
                        <th>Percentatge</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Las filas serán llenadas por JavaScript -->
                    <tr>
                        <td id="grup1"><strong>Represaliats 1939-1979:</strong></td>
                        <td id="email-1"></td>
                        <td id="edad-1"><strong><span id="totalRepresaliats"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup1"></td>
                        <td id="email-1">Processats / Detinguts</td>
                        <td id="edad-1"><strong><span id="totalProcessats"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup1"></td>
                        <td id="email-1">Afusellats</td>
                        <td id="edad-1"><strong><span id="totalAfusellats"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup2"><strong>Exiliats i deportats:</strong></td>
                        <td id="email-2"></td>
                        <td id="edad-2"><strong><span id="totalExiliatsDeportatsTotal"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup2"></td>
                        <td id="email-2">Exiliats</td>
                        <td id="edad-2"><strong><span id="totalExiliatsTotal"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup2"></td>
                        <td id="email-2">Deportats (total)</td>
                        <td id="edad-2"><strong><span id="totalDeportatsTotal"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup2"></td>
                        <td id="email-2">Deportats (morts)</td>
                        <td id="edad-2"><strong><span id="totalDeportatsMorts"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup2"></td>
                        <td id="email-2">Deportats (alliberats)</td>
                        <td id="edad-2"><strong><span id="totalDeportatsAlliberats"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>
                    <tr>
                        <td id="grup3"><strong>Cost humà guerra civil:</strong></td>
                        <td id="email-3"></td>
                        <td id="edad-3"><strong><span id="totalCostHuma"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup3"><strong></strong></td>
                        <td id="email-3">Milicians i combatents de l'exèrcit de la República</td>
                        <td id="edad-3"><strong><span id="totalCombatentsRepublica"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup3"><strong></strong></td>
                        <td id="email-3">Combatents de l'exèrcit sollevat</td>
                        <td id="edad-3"><strong><span id="totalCombatentsSollevats"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup3"><strong></strong></td>
                        <td id="email-3">Sense definir bàndol</td>
                        <td id="edad-3"><strong><span id="totalCombatentsSenseDefinir"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup3"><strong></strong></td>
                        <td id="email-3">Víctimes civils de bombardeigos, accidents i altres causes desconegudes derivades de la guerra</td>
                        <td id="edad-3"><strong><span id="totalCivilsBombardeigs"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                    <tr>
                        <td id="grup3"><strong></strong></td>
                        <td id="email-3">Acció incontrolats - violència revolucionària - repressió rereguarda</td>
                        <td id="edad-3"><strong><span id="totalCivilsRepresaliaRepublicana"></span></strong></td>
                        <td id="edad-1"></td>
                    </tr>

                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p><strong>L'accés a aquesta pàgina està restringit només a usuaris administratius.</strong></p>
    <?php endif; ?>
</div>