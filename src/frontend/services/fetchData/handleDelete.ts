export function initDeleteHandlers(reloadTableCallback?: () => void) {
  document.addEventListener('click', async (event: Event) => {
    const target = event.target as HTMLElement;

    if (target.classList.contains('delete-button')) {
      event.preventDefault();

      const id = target.dataset.id;
      const url = target.dataset.url;

      if (!id || !url) return;

      const confirmed = confirm('Segur que vols eliminar aquest registre?');

      if (!confirmed) return;

      try {
        const response = await fetch(url, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
          },
        });

        const data = await response.json();

        if (response.ok && data.status === 'success') {
          alert('Registre eliminat correctament.');

          if (reloadTableCallback) {
            reloadTableCallback(); // Recargar tabla si se pasó un callback
          } else {
            // Alternativa: eliminar la fila directamente
            const rowElement = target.closest('tr');
            if (rowElement) rowElement.remove();
          }
        } else {
          alert(data.message || 'Error en eliminar el registre.');
        }
      } catch (error) {
        console.error('Error al eliminar:', error);
        alert('Error de xarxa en eliminar el registre.');
      }
    }
  });
}
