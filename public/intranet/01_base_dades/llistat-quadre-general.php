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
                        <th>Completats (visibles al web)</th>
                        <th>Total (pendents, en revisió i completats)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Las filas serán llenadas por JavaScript -->
                    <tr>
                        <td><strong>1) Represaliats 1939-1979:</strong></td>
                        <td></td>
                        <td><strong><span id="totalRepresaliats_completades"></span></strong></td>
                        <td><strong><span id="totalRepresaliats"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>a) Processats / Detinguts</td>
                        <td><strong><span id="totalProcessats_completades"></span></strong></td>
                        <td><strong><span id="totalProcessats"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>a.1) Processats en Consell de Guerra</td>
                        <td><strong><span id="totalProcessatsConsellGuerra_completades"></span></strong></td>
                        <td><strong><span id="totalProcessatsConsellGuerra"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>b) Afusellats</td>
                        <td><strong><span id="totalAfusellats_completades"></span></strong></td>
                        <td><strong><span id="totalAfusellats"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>c) Empresonats Presó Model de Barcelona</td>
                        <td><strong><span id="totalPresoModel_completades"></span></strong></td>
                        <td><strong><span id="totalPresoModel"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>d) Detinguts Guàrdia Urbana de Terrassa / dipòsit de Sant Llàtzer</td>
                        <td><strong><span id="totalGUTerrassa_completades"></span></strong></td>
                        <td><strong><span id="totalGUTerrassa"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>e) Afectats Llei Responsabilitats Polítiques</td>
                        <td><strong><span id="totalResponsabilitats_completades"></span></strong></td>
                        <td><strong><span id="totalResponsabilitats"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>f) Processats Tribunal Orden Público</td>
                        <td><strong><span id="totalTPO_completades"></span></strong></td>
                        <td><strong><span id="totalTPO"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>g) Detinguts Comitè Relacions de Solidaritat (1939-1940)</td>
                        <td><strong><span id="totalComiteRelacions_completades"></span></strong></td>
                        <td><strong><span id="totalComiteRelacions"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>h) Detinguts Comitè Solidaritat (1971-1977)</td>
                        <td><strong><span id="totalComiteSolidaritat_completades"></span></strong></td>
                        <td><strong><span id="totalComiteSolidaritat"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>i) Detinguts Camps de treball</td>
                        <td><strong><span id="totalCampsTreball_completades"></span></strong></td>
                        <td><strong><span id="totalCampsTreball"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>j) Detinguts Batallons disciplinaris de presos</td>
                        <td><strong><span id="totalBatallonsPresos_completades"></span></strong></td>
                        <td><strong><span id="totalBatallonsPresos"></span></strong></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>k) Represaliats pendents classificació </td>
                        <td><strong><span id="totalRepresaliatsPendents_completades"></span></strong></td>
                        <td><strong><span id="totalRepresaliatsPendents"></span></strong></td>
                    </tr>

                    <tr>
                        <td id="grup2"><strong>2) Exiliats i deportats:</strong></td>
                        <td id="email-2"></td>
                        <td><strong><span id="totalExiliatsDeportatsTotal_completades"></span></strong></td>
                        <td><strong><span id="totalExiliatsDeportatsTotal"></span></strong></td>
                    </tr>

                    <tr>
                        <td id="grup2"></td>
                        <td id="email-2">Exiliats</td>
                        <td><strong><span id="totalExiliatsTotal_completades"></span></strong></td>
                        <td><strong><span id="totalExiliatsTotal"></span></strong></td>
                    </tr>

                    <tr>
                        <td id="grup2"></td>
                        <td id="email-2">Deportats (total)</td>
                        <td><strong><span id="totalDeportatsTotal_completades"></span></strong></td>
                        <td><strong><span id="totalDeportatsTotal"></span></strong></td>
                    </tr>

                    <tr>
                        <td id="grup2"></td>
                        <td id="email-2">Deportats (morts)</td>
                        <td><strong><span id="totalDeportatsMorts_completades"></span></strong></td>
                        <td><strong><span id="totalDeportatsMorts"></span></strong></td>
                    </tr>

                    <tr>
                        <td id="grup2"></td>
                        <td id="email-2">Deportats (alliberats)</td>
                        <td><strong><span id="totalDeportatsAlliberats_completades"></span></strong></td>
                        <td><strong><span id="totalDeportatsAlliberats"></span></strong></td>
                    </tr>

                    <tr>
                        <td id="grup3"><strong>4) Cost humà guerra civil:</strong></td>
                        <td id="email-3"></td>
                        <td><strong><span id="totalCostHuma_completades"></span></strong></td>
                        <td><strong><span id="totalCostHuma"></span></strong></td>
                    </tr>

                    <tr>
                        <td id="grup3"><strong></strong></td>
                        <td id="email-3">Milicians i combatents de l'exèrcit de la República</td>
                        <td><strong><span id="totalCombatentsRepublica_completades"></span></strong></td>
                        <td><strong><span id="totalCombatentsRepublica"></span></strong></td>
                    </tr>

                    <tr>
                        <td id="grup3"><strong></strong></td>
                        <td id="email-3">Combatents de l'exèrcit sollevat</td>
                        <td><strong><span id="totalCombatentsSollevats_completades"></span></strong></td>
                        <td><strong><span id="totalCombatentsSollevats"></span></strong></td>
                    </tr>

                    <tr>
                        <td id="grup3"><strong></strong></td>
                        <td id="email-3">Sense definir bàndol</td>
                        <td><strong><span id="totalCombatentsSenseDefinir_completades"></span></strong></td>
                        <td><strong><span id="totalCombatentsSenseDefinir"></span></strong></td>
                    </tr>

                    <tr>
                        <td id="grup3"><strong></strong></td>
                        <td id="email-3">Víctimes civils de bombardeigos, accidents i altres causes desconegudes derivades de la guerra</td>
                        <td><strong><span id="totalCivilsBombardeigs_completades"></span></strong></td>
                        <td><strong><span id="totalCivilsBombardeigs"></span></strong></td>
                    </tr>

                    <tr>
                        <td id="grup3"><strong></strong></td>
                        <td id="email-3">Acció incontrolats - violència revolucionària - repressió rereguarda</td>
                        <td><strong><span id="totalCivilsRepresaliaRepublicana_completades"></span></strong></td>
                        <td><strong><span id="totalCivilsRepresaliaRepublicana"></span></strong></td>
                    </tr>

                    <tr>
                        <td><strong>Total repressió 1939-1979:</strong></td>
                        <td></td>
                        <td><strong><span id="totalGeneral_completades"></span></strong></td>
                        <td><strong><span id="totalGeneral"></span></strong></td>
                    </tr>

                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p><strong>L'accés a aquesta pàgina està restringit només a usuaris administratius.</strong></p>
    <?php endif; ?>
</div>