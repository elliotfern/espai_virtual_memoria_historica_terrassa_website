import { getIsAdmin } from '../../services/auth/getIsAdmin';
import { getIsAutor } from '../../services/auth/getIsAutor';

export async function mostrarBotonsNomesAdmin() {
  const isAdmin = await getIsAdmin();

  const buttonContainer = document.getElementById('isAdminButton');

  if (isAdmin) {
    // Si es admin, mostramos el botón
    if (buttonContainer) {
      buttonContainer.style.display = 'block';
    }
  } else {
    // Si no es admin, comprobamos si es autor
    const isAutor = await getIsAutor();

    if (isAutor) {
      if (buttonContainer) {
        buttonContainer.style.display = 'block';
      }
    } else {
      // Si no es admin ni autor, eliminamos el botón del DOM
      if (buttonContainer) {
        buttonContainer.remove();
      }
    }
  }
}
