<!-- index.html -->

<style>
    #filtros-panel {
        position: sticky;
        top: 100px;
        max-height: calc(100vh - 120px);
        overflow: auto;
    }

    #tabla-resultados .fila-persona {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    #tabla-resultados .nombre {
        font-weight: 600;
    }

    @media (max-width: 900px) {
        /* En mobile, vuelve a flujo normal (sin sticky ni scroll) */

        #filtros-panel {
            position: relative;
            width: 100%;
            max-height: none;
            height: auto;
            overflow: visible;
        }


        #resultados {
            margin-left: 0;
        }
    }

    /* Cada bloque de filtro en columna */
    #filtros .filtro-grupo {
        margin-bottom: 12px;
    }

    /* Etiquetas arriba del select */
    #filtros .filtro-grupo label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        font-size: 13px;
    }

    /* Choices ocupa todo el ancho del contenedor */
    #filtros .choices,
    #filtros .choices__inner {
        width: 100%;
    }

    #filtros .choices {
        display: block;
    }
</style>


<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">

    <div class="container px-4">
        <span class="negreta gran italic-text cap">Base de dades<br>cost humà de la guerra civil</span>

    </div>
</div>


<div class="container d-flex flex-column card-body" style="padding-top: 50px;padding-bottom:10px;">
    <span class="titol italic-text gran lora">Explora les històries, documents i testimonis</span>

    <p><span class="text1 mitja raleway" style="margin-top:20px">
            El llistat del Cost Humà de la Guerra Civil està format per totes les persones terrassenques (residents o nascudes a Terrassa) mortes, durant la Guerra Civil: milicians, combatents o soldats, al front de guerra i també les víctimes civils de la rereguarda.</span></p>

    <span class="more-text d-none text1 mitja raleway">

        <h2>El cost humà de la guerra civil a Terrassa</h2>

        <p>El divendres 17 de juliol de 1936 <strong>Terrassa</strong>, una població de 47.690 habitants, rep rumors d'un aixecament militar feixista contra la República. Un grup de militars colpistes s'aixecaven en contra d'una temuda revolució i el que veritablement va passar és que van provocar una guerra i una revolució. El fracàs parcial del cop d'Estat militar provoca una guerra. Les tropes que manava Franco a les Canàries es declaren rebels al règim republicà. S'inicia així tot un pla per a exterminar la República que no abandonarà als franquistes amb la seva victòria militar, es perllongarà més enllà del 1939.</p>
        <p>Precisament tal dia com avui, un 26 de gener de fa 82 anys, al migdia el front arriba a Terrassa i la ciutat és ocupada per les tropes franquistes; començava una llarga negra nit on una justícia arbitrària escamparà molta por entre la ciutadania. El passat novembre, el company historiador i amic Manel Márquez, juntament amb Joan Soler director de l'AHT, presentaven ací mateix una recerca sobre les víctimes de la dictadura franquista. Tot un exercici de dignitat i memòria.</p>
        <p>La dignitat i la memòria que mereixen totes les víctimes del passat no implica pels historiadors la indiferenciació de les causes, mecanismes i dimensió del procés repressiu que hi ha darrera de les seves morts. On triomfa el cop militar els rectors de la Rebel·lió apliquen allò que havien programat explícitament: fer-se amb el poder i eliminació de tot allò que expressi vida republicana. El treball que els presentem avui, correspon a una tasca que, sota el títol <strong>EL COST HUMÀ</strong> de la Guerra Civil a Terrassa, ha tingut per objectiu elaborar un llistat que està conformat per quatre vessants d'informació:</p>
        <ul>
            <li>En primer lloc fa referència als milicians i combatents morts en defensa de la República.</li>
            <li>En segon lloc els combatents de l'Exercit Nacional franquista.</li>
            <li>Un tercer bloc d'informació fa referència a les víctimes de bombardejos i accidents derivats de la guerra.</li>
            <li>Una quarta vessant del llistat integra les persones que van morir víctimes de la repressió de la rereguarda, de la violència revolucionària o assassinats per grups d'acció.</li>
        </ul>

        <h2>1. Fonts</h2>
        <p>Es fa difícil fer un llistat exacte, fidedigne, per raons obvies. L'any 1939 no va acabar la guerra, va continuar amb els consells de guerra, la repressió, el silenci, això fa que, després de molts anys, es fa difícil disposar d'una nòmina exacta, per exemple, de combatents republicans. El treball que presentem no és un treball definitiu, tot el contrari, resta a rectificacions de possibles errors en algunes dades, ampliacions de nous registres, etc... És un treball que recull totes aquelles dades que teníem disponibles de recerques anteriors més les que hem pogut cercar mitjançant la consulta de fonts primàries diverses.</p>
        <p>Cal subratllar que parlar de la Guerra Civil avui no és nou, cal recordar la tasca d'historiadors locals que a finals dels anys 80 i principis dels noranta estudien aquest esdeveniment significatiu del segle XX. Investigadors com ara X. Navarro o Xavier Marcet, per exemple, fan referència a les víctimes de la repressió a la rereguarda entre 1936 i 1939. L'any 1993 en el treball <em>Segle XX</em> (editat pel diari de Terrassa), es recull, a partir d'un treball previ de Francesc Escudé i Marian Trenchs, un llistat de 215 morts per la repressió revolucionària.</p>
        <p>En aquesta llista d'aquestes 215 víctimes en la Terrassa republicana s'hauria de sumar l'assassinat de 3 membres de la FAI i de 13 terrassencs que no resideixen a la ciutat (Manresa, Barcelona, Collbató, Tortosa. Diverses professions i on hi ha un membre de la CNT). En total 231 persones víctimes de la repressió a la rereguarda. Avui aquesta xifra s'ha ampliat, com els comentaré després. Beatificacions, Causa general i/o martirologi. Les víctimes de la violència revolucionària mai van ser oblidades. Per les altres víctimes republicanes no va existir un registre civil, ni làpides, ni martirologis ni causa General.</p>
        <p>Haurem d'esperar l'any 2003 perquè l'investigador Jordi Serra faci una recerca sobre els combatents terrassencs morts en acció de guerra, un llistat nominal que arriba als 528 de morts o desapareguts durant la Guerra Civil. Altres investigadors com ara Jordi Oliva i Juan Antonio Olivares han contribuït a enriquir el coneixement del cost humà de la GC. a Terrassa.</p>

        <h2>2. Dades numèriques</h2>
        <table border="1">
            <tr>
                <th>Categoria</th>
                <th>Número</th>
                <th>Percentatge</th>
            </tr>
            <tr>
                <td>MILICIANS I COMBATENTS DE L'EXERCIT DE LA REPÚBLICA</td>
                <td>842</td>
                <td>71,53%</td>
            </tr>
            <tr>
                <td>COMBATENTS DE L'EXERCIT NACIONAL</td>
                <td>27</td>
                <td>2,30%</td>
            </tr>
            <tr>
                <td>SENSE DEFINIR BÀNDOL</td>
                <td>2</td>
                <td>0,17%</td>
            </tr>
            <tr>
                <td>VÍCTIMES CIVILS DE BOMBARDEIGS I ACCIDENTS DERIVATS DE LA GUERRA</td>
                <td>34</td>
                <td>2,88%</td>
            </tr>
            <tr>
                <td>ACCIÓ INCONTROLATS-VIOLÈNCIA REVOLUCIONARIA-REPRESSIÓ REREGUARDA</td>
                <td>260</td>
                <td>22,10%</td>
            </tr>
            <tr>
                <td>ACCIÓ D'EXERCIT REPUBLICÀ</td>
                <td>4</td>
                <td>0,33%</td>
            </tr>
            <tr>
                <td>VÍCTIMES CIVILS (SENSE DETERMINAR CAUSA)</td>
                <td>8</td>
                <td>0,68%</td>
            </tr>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td><strong>1177</strong></td>
                <td></td>
            </tr>
        </table>
        <p>1177 registres de morts: 871 milicians/combatents (un 74% del total del cost humà) – 306 civils. Un 97% del total de morts en combat són milicians o militars republicans.</p>
        <p>Dels Combatents de l'Exèrcit Rebel (27) hi ha 22 casos on consta el lloc de la mort al front. Dels que formaven part del Tercio de requetès de Nuestra señora de Montserrat, 6 van morir a la batalla de l'Ebre i 4 en la defensa de CODO (Saragossa, 1937). En tan sols 2 casos no tenim informació per determinar si defensaven la República o eren combatents en l'Exèrcit feixista dirigit per Franco. Dels 871 registres en total de milicians/combatents tenim 162 casos que no sabem on van morir i/o quin lloc estan enterrats; dels quals 58 correspon a joves menors de 22 anys. La recerca roman oberta...</p>

        <h2>3. Context del cost humà</h2>
        <p>Malgrat que el registre de defunció no sempre és precís, perquè pot existir una desviació entre la data de mort registrada i el lloc on es produeix exactament la mort, es pot contextualitzar el nombre de baixes respecte el lloc on es van produir batalles i ofensives.</p>
        <p>Dues fases de la guerra:</p>
        <ul>
            <li>Juliol-Desembre 1936: Viscuda com a prolongació de l'esclat revolucionari i resposta de les MILICIES (Voluntaris, columnes de milicians de Partits polítics i Sindicats) davant del cop militar.</li>
            <li>Marcada per la militarització (Exèrcit Popular de la República) i la incorporació de lleves. L'abril de 1938 es mobilitzen les lleves del 1927, 1928 i 1941 (la denominada lleva del biberó amb 17 anys) i també reservistes durant el mes d'octubre, dels anys 1923 i 1926.</li>
        </ul>
        <p>Dels 871 morts de milicians i/o combatents:</p>
        <ul>
            <li>1936: 60 morts (6,8%)</li>
            <li>1937: 112 morts (12,8%)</li>
            <li>1938: 514 morts (59%)</li>
            <li>1939: 97 (11,1%)</li>
            <li>Sense determinar data: 88 casos (10%)</li>
        </ul>
        <p>La localització de les baixes: només queda reflectida en el 70% dels registres. 264 no consta lloc de la mort.</p>
        <p>19,6% de morts milicians/combatents (Juliol 1936 - agost 1937). Entre un 70 i un 80% (setembre 1937-març 1939).</p>
        <p>Els combatents terrassencs participen en els fronts d'Aragó (Belchite i Terol) de Lleida (Segre, Balaguer i Serós) i de l'Ebre (Juliol-Novembre 1938), i durant l'ofensiva final sobre Catalunya (desembre 1938-Febrer 1939).</p>

        <h2>4. La distribució de les baixes del bàndol republicà</h2>
        <p>Un 60% de les baixes republicanes es dona en els fronts d'Aragó i de Catalunya (355-360 milicians/combatents republicans).</p>
        <p>L'edat mitjana dels combatents morts? 26/27 anys.</p>
        <p>232 joves menors de 22 anys morts/desapareguts, el que significa un 26% del total; 60 de la lleva del biberó: 17 i 18 anys; un cas de 14 anys, el més jove en el front d'Aragó el 5 d'octubre 1936.</p>
        <p>256 combatents, entre 22 i 30 anys (29%).</p>
        <p>167, més de 30 anys. (19%)</p>
        <p>215 no consta (24%)</p>
        <p>La primera víctima militar és del 5 d'agost 1936 en el front de Madrid: Miquel Íñiguez Llopis de 22 anys (milicià voluntari en una columna del PSUC).</p>
        <p>26 agost 1936, un altre milicià de 25 anys en el Front d'Aragó: Feliu Vázquez Taveria.</p>
        <p>28 agost en el Front del Centre (Toledo) 17 anys un altre milicià: Esteve Bernat Bartomeus.</p>

        <h2>5. Registre del cost civil</h2>
        <p>A Terrassa i Catalunya l'aixecament militar feixista fracassa i provoca una revolució social. La ciutat no viu de prop la guerra, però durant els primers mesos patrulles armades duen a terme una tasca sistemàtica de detencions i assassinats de persones considerades contràries a la revolució. La repressió no té un criteri uniforme, depenia de la dinàmica, els interessos o les fòbies dels que componien les patrulles – els anomenats grups d'incontrolats-, va permetre l'aflorament d'odis creuats que s'havien anat acumulant amb el pas dels anys, un fil de memòria que no s'havia trencat amb el temps i que arrossega comptes pendents de conflictes socials anteriors; i es va desfermar una forta repressió contra molts empresaris, directius d'empreses i gent conservadora en general. La repressió cau sobre tot un ventall de persones sospitoses de poder donar suport als insurgents (molts vinculats al salisme) o simplement víctimes d'una denúncia. Una fragmentació de poders s'instal·la a la ciutat: milicians que patrullen armats d'una banda i de l'altra un Ajuntament dirigit per l'Alcalde Samuel Morera que condemna actes de violència. Una repressió al marge de les institucions republicanes i algun dissident de la CNT qualificarà com «l'esgarrifos devessall de sang», denunciant «secrets i crims que quedaven sota el vel de l'ombra i la impunitat».</p>
        <p>Dels 306 Civils registrats: 260 morts corresponen als assassinats comesos per grups d'acció, patrulles armades, víctimes de la violència política i la revolució social: 207 morts entre juliol i desembre de 1936 (78%). El 19 de juliol es produeix la primera mort: Josep Domingo Montserrat (27 anys, treballador Caixa Estalvis de Terrassa). 34 morts civils corresponen a víctimes de bombardejos fora de la ciutat i accidents derivats de la guerra.</p>
        <p>Dins del context de l'arribada el dia 26 de gener de 1939 de les tropes Nacionals i la retirada dels combatents republicans de pas per la ciutat es registren 4 víctimes i sense que es pugui determinar a hores d’ara la circumstància de la mort en tenim 8 víctimes.</p>
        <p>Sense estendre'ns més, vull agrair a l'Ajuntament el seu programa Terrassa Memòria impulsat amb altres entitats com ara el CEHT, i agrair també la intervenció de l'Arxiu Històric de la ciutat per la gestió i tractament de les dades, gràcies per col·laborar en obrir portes al coneixement històric, possibilitant que la memòria obri expedients que la història moltes vegades dona per arxivats.</p>
        <p>Josep Lluís Lacueva</p>
        <p>Aquest text correspon a la presentació de l'estudi sobre el Cost Humà a Terrassa - Ajuntament de Terrassa, 26 de gener de 2021</p>
    </span>


    <button class="btn-toggle btn btn-primary btn-custom-2 w-auto align-self-start" style="margin-top:25px">
        llegir més
    </button>
</div>

<div class="container mt-4 text-center">
    <span class="titol italic-text gran lora">Selecciona una categoria per començar la teva recerca:</span>
    <div class="row g-3 justify-content-center" style="padding-top: 50px;padding-bottom:10px;">
        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/general/#filtre">
                <span class="lora mitja">General</span>
            </a>
        </div>

        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div active" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/cost-huma/#filtre">
                <span class="lora mitja">Cost humà <br>de la Guerra Civil</span>
            </a>
        </div>

        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/exiliats-deportats/#filtre">
                <span class="lora mitja">Exiliats<br>i deportats</span>
            </a>
        </div>

        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/represaliats/#filtre">
                <span class="lora mitja">Represaliats <br>de la dictadura</span>
            </a>
        </div>
    </div>
</div>

<span id="filtre"></span>
<div class="container my-5" id="filtre">
    <div class="row g-4">
        <!-- Columna filtros -->
        <div class="col-lg-3">
            <div id="filtros-panel" class="bg-white p-3 border rounded">
                <input
                    type="text"
                    id="buscador-nom"
                    placeholder="Cerca per nom o cognoms"
                    class="form-control mb-3" />
                <h3 class="h6">Filtres</h3>
                <div id="filtros"></div>
                <div class="mt-3">
                    <button id="btn-reset" type="button" class="btn btn-outline-secondary w-100">
                        Restableix filtres
                    </button>
                </div>
            </div>
        </div>

        <!-- Columna resultados -->
        <div class="col-lg-9">
            <div id="resultados">
                <h3 class="h6">Resultats</h3>
                <div id="tabla-resultados" aria-live="polite"></div>
                <div id="contador-resultados" class="text-muted mt-3"></div>
                <div id="paginacion" class="d-flex gap-2 align-items-center mt-3">
                    <button id="prevPage" class="btn btn-outline-primary btn-sm" aria-label="Anterior">
                        Anterior
                    </button>
                    <span id="pageInfo" style="min-width:120px;text-align:center"></span>
                    <button id="nextPage" class="btn btn-outline-primary btn-sm" aria-label="Següent">
                        Següent
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .active {
        background-color: #C2AF96 !important;
        color: #133B7C !important;
    }

    .btn-div {
        width: 70%;
        padding: 20px;
        background-color: #133B7C;
        color: #C2AF96;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: background 0.3s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        text-decoration: none !important;
    }

    .btn-div:hover {
        background-color: #C2AF96;
        color: #133B7C;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".btn-toggle").forEach(button => {
            button.addEventListener("click", function() {
                const cardBody = this.closest(".card-body");
                const moreText = cardBody.querySelector(".more-text");

                if (moreText.classList.contains("d-none")) {
                    moreText.classList.remove("d-none");
                    this.textContent = "veure menys";
                } else {
                    moreText.classList.add("d-none");
                    this.textContent = "veure més";
                }
            });
        });
    });
</script>