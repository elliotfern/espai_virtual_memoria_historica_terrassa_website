var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import { devDirectory } from '../../config.js';
export function nameUser(idUser) {
    return __awaiter(this, void 0, void 0, function* () {
        const urlAjax = `${devDirectory}/api/auth/get/?type=user&id=${idUser}`;
        const token = localStorage.getItem('token');
        try {
            const response = yield fetch(urlAjax, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = yield response.json(); // Devuelve la respuesta como JSON
            // Modifica el contenido de un div con el resultado de la API
            const welcomeMessage = data.nom ? `Hola, ${data.nom}` : 'Usuari desconegut';
            const userDiv = document.getElementById('userDiv');
            if (userDiv) {
                userDiv.innerHTML = welcomeMessage; // Muestra el mensaje en tu página
            }
        }
        catch (error) {
            console.error('Error:', error);
        }
    });
}
