export function avatarUsuari() {
  document.getElementById('usuariForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const nomImatge = (document.getElementById('nomImatge') as HTMLInputElement).value;
    const tipus = (document.getElementById('tipus') as HTMLSelectElement).value;
    const fileInput = document.getElementById('fileToUpload') as HTMLInputElement;
    const file = fileInput.files?.[0];

    if (!file) {
      alert('Has de seleccionar un fitxer!');
      return;
    }

    if (!nomImatge) {
      alert("Has d'escriure un nom d'imatge!");
      return;
    }

    const formData = new FormData();
    formData.append('fileToUpload', file);
    formData.append('nomImatge', nomImatge);
    formData.append('tipus', tipus);

    try {
      const response = await fetch('/api/auxiliars/post/usuariAvatar', {
        method: 'POST',
        body: formData,
      });

      const result = await response.json();
      const missatgeOk = document.getElementById('okMessage');
      const missatgeErr = document.getElementById('errMessage');

      if (result.status === 'success') {
        if (missatgeOk && missatgeErr) {
          missatgeOk.style.display = 'block';
          missatgeErr.style.display = 'none';
          missatgeOk.textContent = "L'operació s'ha realizat correctament a la base de dades.";
        } else {
          if (missatgeOk && missatgeErr) {
            missatgeErr.style.display = 'block';
            missatgeOk.style.display = 'none';
            missatgeErr.textContent = "L'operació no s'ha pogut realizar correctament a la base de dades.";
          }
        }
      }
    } catch (error) {
      console.error('Error al pujar la imatge:', error);
    }
  });
}
