export function generarLinks(selectedValues: string[], categorias: { [key: number]: string }, userId: number): void {
  const container = document.getElementById('btnRepressio');
  if (!container) return;

  container.innerHTML = '';

  selectedValues.forEach((id) => {
    const categoriaId = parseInt(id.trim());
    const titulo = categorias[categoriaId];

    if (titulo) {
      const link = document.createElement('a');
      const devDirectory = `https://${window.location.hostname}`;
      link.href = `${devDirectory}/gestio/base-dades/modifica-repressio/${categoriaId}/${userId}`;
      link.className = 'btn btn-success m-2';
      link.textContent = titulo; // Aquí se pone el nombre visible en el botón
      link.target = '_blank';

      container.appendChild(link);
    }
  });
}
