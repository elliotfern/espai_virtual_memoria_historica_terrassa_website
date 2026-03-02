<?php

require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <div id="titolForm"></div>

            <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                <div id="okText"></div>
            </div>

            <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                <div id="errText"></div>
            </div>

            <form id="HoresForm">
                <!-- ID del registre (si edició) -->
                <input type="hidden" name="id" id="id" value="">
                <input type="hidden" name="user_uuid" id="user_uuid" value="">

                <!-- Dia -->
                <div class="col-md-12 espai-superior">
                    <label for="dia" class="form-label negreta">Dia:</label>
                    <input type="date" class="form-control" name="dia" id="dia" required>
                </div>

                <!-- Hores (enteres) -->
                <div class="col-md-12 espai-superior">
                    <label for="hores" class="form-label negreta">Hores treballades:</label>
                    <input type="number" class="form-control" name="hores" id="hores" min="0" max="8" step="1" required>
                    <div class="form-text">Hores en números enters (1, 2, 3...).</div>
                </div>

                <!-- Tipus de tasca -->
                <div class="col-md-12 espai-superior">
                    <label for="tipusId" class="form-label negreta">Tipus de tasca:</label>
                    <select class="form-select" name="tipusId" id="tipusId" required>
                        <option value="">-- Selecciona tipus --</option>
                        <!-- Options via JS (aux_tipus_tasca) -->
                    </select>
                </div>

                <!-- Descripció -->
                <div class="col-md-12 espai-superior">
                    <label for="descripcio" class="form-label negreta">Descripció:</label>
                    <textarea class="form-control" name="descripcio" id="descripcio" rows="4" maxlength="500" placeholder="Breu descripció del que s&apos;ha fet (opcional)"></textarea>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                        <!-- espai lliure (si vols posar un botó d'esborrar en edició, etc.) -->
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <button class="btn btn-primary" id="btnHores" type="submit">Desar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>