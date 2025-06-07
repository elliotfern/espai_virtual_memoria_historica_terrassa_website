import { getIsAdmin } from '../../services/auth/getIsAdmin';
import { getIsAutor } from '../../services/auth/getIsAutor';
import { getIsLogged } from '../../services/auth/getIsLogged';

export async function mostrarBotonsNomesAdmin() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();

  const buttonContainer = document.getElementById('isAdminButton');

  if (isAdmin || isAutor) {
    if (buttonContainer) {
      buttonContainer.style.display = 'block';
    }
  } else if (isLogged) {
    // Si no es admin ni autor, eliminamos el botón del DOM
    if (buttonContainer) {
      buttonContainer.remove();
    }
  }
}
