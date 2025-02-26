<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Cronologia</h2>
            <div class="col-md-4" style="margin-top:20px;margin-bottom:20px">
                <a href="https://memoriaterrassa.cat/gestio/cronologia/afegir-esdeveniment" class="btn btn-success">Afegir esdeveniment</a>
            </div>

            <!-- Botones de áreas -->
            <div id="filtroArea">
                <button class="boton activo" data-area="tots">Tots</button>
                <button class="boton" data-area="1">Terrassa</button>
                <button class="boton" data-area="2">Catalunya</button>
                <button class="boton" data-area="3">Espanya</button>
                <button class="boton" data-area="4">Món</button>

            </div>

            <!-- Botones de temas (ocultos al inicio) -->
            <div id="filtroTema">
                <button class="boton activo" data-tema="tots">Tots</button>
                <button class="boton" data-tema="1">Econòmic</button>
                <button class="boton" data-tema="2">Polític</button>
                <button class="boton" data-tema="3">Moviment Social</button>

            </div>

            <!-- Botones de años (se generan dinámicamente) -->
            <div id="filtroAny"></div>


            <!-- Contenedor de la lista de eventos -->
            <div id="eventos" class="eventos-container"></div>

        </div>
    </div>
</div>

<style>
    .boton {
        margin: 5px;
        padding: 10px;
        cursor: pointer;
        display: inline-block;
        background-color: lightgray;
        border: 1px solid black;
    }

    .activo {
        background-color: darkgray;
    }

    .oculto {
        display: none;
    }

    /* Contenedor de eventos */
    .eventos-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: flex-start;
        /* Cambia 'center' por 'flex-start' */
        margin-top: 20px;
    }

    /* Estilo para cada tarjeta de evento */
    .evento {
        background: white;
        border-left: 5px solid #007BFF;
        border-radius: 8px;
        padding: 15px;
        width: 800px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }

    .evento:hover {
        transform: scale(1.05);
    }

    /* Año y fecha en grande */
    .evento-fecha {
        font-size: 14px;
        font-weight: bold;
        color: #007BFF;
        margin-bottom: 5px;
    }

    /* Texto del evento */
    .evento-texto {
        font-size: 16px;
        color: #333;
    }

    .evento-anyo {
        font-size: 24px;
        font-weight: bold;
        margin-top: 20px;
        color: #333;
        width: 100%;
        text-align: left;
    }

    .evento-mes {
        font-size: 20px;
        font-weight: bold;
        margin-top: 10px;
        color: #555;
        width: 100%;
        text-align: left;
    }


    /* Responsivo */
    @media (max-width: 768px) {
        .evento {
            width: 90%;
        }
    }

    .pagination-container {
        margin-top: 25px;
    }

    .paginacion-boton {
        background-color: #fff;
        color: #007bff;
        border: 1px solid #dee2e6;
        padding: 8px 12px;
        margin: 0 5px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        font-size: 14px;
    }

    .paginacion-boton:hover {
        background-color: #007bff;
        color: white;
    }

    .paginacion-boton.activo {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .paginacion-boton:disabled,
    .paginacion-boton[disabled] {
        color: #6c757d;
        background-color: #e9ecef;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
</style>




<script>
    document.addEventListener("DOMContentLoaded", function() {
        const eventosContainer = document.getElementById("eventos");
        const paginacionContainer = document.createElement("div");
        eventosContainer.after(paginacionContainer);
        let selectedArea = "tots";
        let selectedTema = "tots";
        let selectedAny = "tots";
        let paginaActual = 1;

        // Asignar clase "activo" al inicio
        document.querySelector('[data-area="tots"]').classList.add("activo");
        document.querySelector('[data-tema="tots"]').classList.add("activo");

        filtroTema.classList.remove("oculto");

        // Manejo de botones de áreas
        filtroArea.addEventListener("click", function(e) {
            if (e.target.classList.contains("boton")) {
                selectedArea = e.target.getAttribute("data-area");
                selectedTema = "tots";
                selectedAny = "tots";
                paginaActual = 1; // Reiniciar la paginación

                document.querySelectorAll("#filtroArea .boton").forEach(btn => btn.classList.remove("activo"));
                e.target.classList.add("activo");

                cargarEventos();
            }
        });

        // Manejo de botones de temas
        filtroTema.addEventListener("click", function(e) {
            if (e.target.classList.contains("boton")) {
                selectedTema = e.target.getAttribute("data-tema");
                paginaActual = 1; // Reiniciar la paginación

                document.querySelectorAll("#filtroTema .boton").forEach(btn => btn.classList.remove("activo"));
                e.target.classList.add("activo");

                cargarEventos();
            }
        });

        // Manejo de botones de años
        filtroAny.addEventListener("click", function(e) {
            if (e.target.classList.contains("boton")) {
                selectedAny = e.target.getAttribute("data-any");
                paginaActual = 1; // Reiniciar la paginación

                document.querySelectorAll("#filtroAny .boton").forEach(btn => btn.classList.remove("activo"));
                e.target.classList.add("activo");

                cargarEventos();
            }
        });

        function cargarEventos() {
            fetch(`/api/cronologia/get/?area=${selectedArea}&tema=${selectedTema}&any=${selectedAny}&pagina=${paginaActual}`)
                .then(response => response.json())
                .then(data => {
                    mostrarEventos(data.eventos);

                    // Verificar si hay eventos filtrados antes de mostrar la paginación
                    if (data.totalPaginas > 1) {
                        crearPaginacion(data.totalPaginas);
                    } else {
                        paginacionContainer.innerHTML = ""; // Oculta la paginación si no es necesaria
                    }
                })
                .catch(error => console.error("Error al cargar eventos:", error));
        }


        function crearPaginacion(totalPaginas) {
            paginacionContainer.innerHTML = ""; // Limpia la paginación previa

            if (totalPaginas <= 1) return; // No genera paginación si no es necesaria

            const contenedorBotones = document.createElement("div");
            contenedorBotones.classList.add("pagination-container");

            for (let i = 1; i <= totalPaginas; i++) {
                const boton = document.createElement("button");
                boton.textContent = i;
                boton.classList.add("paginacion-boton");

                if (i === paginaActual) boton.classList.add("activo");

                boton.addEventListener("click", () => {
                    document.querySelectorAll(".paginacion-boton").forEach(btn => btn.classList.remove("activo"));
                    boton.classList.add("activo");

                    paginaActual = i;
                    cargarEventos();

                    setTimeout(() => {
                        const eventosContainer = document.getElementById("eventos");
                        if (eventosContainer) {
                            const yOffset = eventosContainer.getBoundingClientRect().top + window.scrollY - 20;
                            window.scrollTo({
                                top: yOffset,
                                behavior: "smooth"
                            });
                        }
                    }, 200);
                });

                contenedorBotones.appendChild(boton);
            }

            paginacionContainer.appendChild(contenedorBotones);
        }

        function cargarBotonesAños() {
            fetch("/api/cronologia/get/anys")
                .then(response => response.json())
                .then(anyos => {
                    filtroAny.innerHTML = "";

                    // Agregar botón "Tots" al inicio
                    const botonTots = document.createElement("button");
                    botonTots.classList.add("boton");
                    botonTots.setAttribute("data-any", "tots");
                    botonTots.textContent = "Tots";
                    botonTots.classList.add("activo");
                    filtroAny.appendChild(botonTots);

                    anyos.forEach(obj => {
                        const any = obj.any; // Asegurarse de acceder correctamente al valor del año
                        if (any) {
                            const boton = document.createElement("button");
                            boton.classList.add("boton");
                            boton.setAttribute("data-any", any);
                            boton.textContent = any;
                            filtroAny.appendChild(boton);
                        }
                    });
                })
                .catch(error => console.error("Error al cargar los años:", error));
        }

        function mostrarEventos(eventos) {
            eventosContainer.innerHTML = "";

            if (selectedAny !== "tots") {
                eventos = eventos.filter(evento => evento.any == selectedAny);
            }

            eventos.sort((a, b) => a.any - b.any || a.mesOrdre - b.mesOrdre || a.diaInici - b.diaInici);

            let ultimoAnyo = null;
            let ultimoMes = null;

            eventos.forEach(evento => {
                const {
                    any,
                    mesOrdre,
                    diaInici,
                    textCa
                } = evento;

                if (any !== ultimoAnyo) {
                    const anyoDiv = document.createElement("div");
                    anyoDiv.classList.add("evento-anyo");
                    anyoDiv.textContent = any;
                    eventosContainer.appendChild(anyoDiv);
                    ultimoAnyo = any;
                    ultimoMes = null;
                }

                if (mesOrdre !== ultimoMes) {
                    const mesDiv = document.createElement("div");
                    mesDiv.classList.add("evento-mes");
                    mesDiv.textContent = obtenerNombreMes(mesOrdre);
                    eventosContainer.appendChild(mesDiv);
                    ultimoMes = mesOrdre;
                }

                const eventoDiv = document.createElement("div");
                eventoDiv.classList.add("evento");
                const nombreMes = obtenerNombreMes(mesOrdre);
                const preposicion = obtenerPreposicion(nombreMes);
                eventoDiv.innerHTML = `
                    <div class="evento-fecha">${diaInici} ${preposicion} ${nombreMes.toLowerCase()}</div>
                    <div class="evento-texto">${textCa}</div>
                `;
                eventosContainer.appendChild(eventoDiv);
            });
        }

        function obtenerNombreMes(mesOrdre) {
            const nombresMeses = ["Gener", "Febrer", "Març", "Abril", "Maig", "Juny", "Juliol", "Agost", "Setembre", "Octubre", "Novembre", "Desembre"];
            return nombresMeses[mesOrdre - 1] || "";
        }

        function obtenerPreposicion(nombreMes) {
            return ["Abril", "Agost", "Octubre"].includes(nombreMes) ? "d'" : "de";
        }

        // Cargar los botones de años al inicio
        cargarBotonesAños();
        cargarEventos();
    });
</script>