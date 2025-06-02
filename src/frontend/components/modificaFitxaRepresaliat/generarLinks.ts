export function generarLinks(selectedValues: string[], categorias: { [key: number]: string }, userId: number): void {
  const containerGeneral = document.getElementById('quadreGrupsRepressio');
  if (!containerGeneral) return;
  containerGeneral.style.display = 'block';
  containerGeneral.style.marginBottom = '50px';
  containerGeneral.style.border = '1px solid gray';
  containerGeneral.style.borderRadius = '10px';
  containerGeneral.style.padding = '25px';
  containerGeneral.style.backgroundColor = '#eaeaea';

  const container = document.getElementById('btnRepressio');
  if (!container) return;

  container.innerHTML = '';

  selectedValues.forEach((id) => {
    const categoriaId = parseInt(id.trim());

    // ❌ Omitir si es categoria 5 (Represàlia republicana)
    if (categoriaId === 5) return;

    const titulo = categorias[categoriaId];

    if (titulo) {
      const link = document.createElement('a');
      const devDirectory = `https://${window.location.hostname}`;
      link.href = `${devDirectory}/gestio/base-dades/modifica-repressio/${categoriaId}/${userId}`;
      link.className = 'btn btn-success m-2';
      link.textContent = titulo;
      link.target = '_blank';

      container.appendChild(link);
    }
  });
}
