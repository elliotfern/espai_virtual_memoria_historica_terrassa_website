import { cargarTabla } from './taulaDades';

// Función para crear los botones
export function botonsEstat(pag: string) {
  const divBotones = document.getElementById('botonsFiltres');

  if (divBotones) {
    divBotones.innerHTML = ''; // Limpia botones anteriores
    // Crear botón "Mostrar Todos"
    const botonMostrarTodos = document.createElement('button');
    botonMostrarTodos.innerText = 'Tots';
    botonMostrarTodos.classList.add('btn', 'btn-secondary'); // Clases de Bootstrap
    botonMostrarTodos.onclick = function () {
      cargarTabla(pag, 2, 3); // Mostrar todos (sin filtrar)
    };

    // Crear botón "Completado"
    const botonCompletado = document.createElement('button');
    botonCompletado.innerText = 'Completats (visibles al web)';
    botonCompletado.classList.add('btn', 'btn-success', 'mr-2'); // Clases de Bootstrap
    botonCompletado.onclick = function () {
      cargarTabla(pag, 2, 2); // Filtrar por completado (2)
    };

    // Crear botón "cal revisio"
    const botonRevisio = document.createElement('button');
    botonRevisio.innerText = 'Cal revisió';
    botonRevisio.classList.add('btn', 'btn-secondarys', 'mr-2'); // Clases de Bootstrap
    botonRevisio.onclick = function () {
      cargarTabla(pag, 2, 4); // Filtrar por completado (2)
    };

    // Crear botón "No Completado"
    const botonNoCompletado = document.createElement('button');
    botonNoCompletado.innerText = 'Pendents';
    botonNoCompletado.classList.add('btn', 'btn-primary', 'mr-2'); // Clases de Bootstrap
    botonNoCompletado.onclick = function () {
      cargarTabla(pag, 2, 1); // Filtrar por no completado (1)
    };

    // Agregar los botones al contenedor
    divBotones.appendChild(botonMostrarTodos);
    divBotones.appendChild(botonCompletado);
    divBotones.appendChild(botonRevisio);
    divBotones.appendChild(botonNoCompletado);
  }
}
