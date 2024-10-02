import { login } from "./services/auth/auth.js";
import { logout } from "./services/cookies/cookiesUtils.js";
import { nameUser } from "./components/userName/userName.js";
import { initButtons } from "./components/fitxaRepressaliat/fitxaRepresaliat.js";
document.addEventListener("DOMContentLoaded", () => {
    const btnLogin = document.querySelector("#btnLogin");
    btnLogin === null || btnLogin === void 0 ? void 0 : btnLogin.addEventListener("click", (event) => {
        var _a, _b;
        event.preventDefault();
        const userName = (_a = document.querySelector("#username")) === null || _a === void 0 ? void 0 : _a.value;
        const password = (_b = document.querySelector("#password")) === null || _b === void 0 ? void 0 : _b.value;
        if (userName && password) {
            login(userName, password);
        }
        else {
            console.log("Faltan datos para iniciar sesión.");
        }
    });
    const userIdFromStorage = localStorage.getItem('user_id');
    if (userIdFromStorage) {
        nameUser(userIdFromStorage).catch(error => {
            console.error('Error al llamar a nameUser desde localStorage:', error);
        });
    }
    const btnLogout = document.querySelector("#btnSortir");
    btnLogout === null || btnLogout === void 0 ? void 0 : btnLogout.addEventListener("click", () => {
        logout();
    });
    // Verificar si estamos en la página de ficha de represaliat
    const pathArray = window.location.pathname.split('/');
    const isFichaRepresaliat = pathArray[pathArray.length - 2] === 'fitxa';
    if (isFichaRepresaliat) {
        const id = pathArray[pathArray.length - 1];
        // Llama a initButtons cuando la página se haya cargado
        initButtons(id); // Pasar el id
    }
});
