export async function nameUser(idUser: string): Promise<void> {
    const devDirectory =  `https://${window.location.hostname}`;
    const urlAjax = `${devDirectory}/api/auth/get/?type=user&id=${idUser}`;
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(urlAjax, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data: { nom?: string; id: string } = await response.json(); // Devuelve la respuesta como JSON
        
        // Modifica el contenido de un div con el resultado de la API
        const welcomeMessage = data.nom ? `Hola, ${data.nom}` : 'Usuari desconegut';
        const userDiv = document.getElementById('userDiv');
        
        if (userDiv) {
            userDiv.innerHTML = welcomeMessage; // Muestra el mensaje en tu página
        }

    } catch (error) {
        console.error('Error:', error);
    }
}
