export async function transmissioDadesDB(event: Event, tipus: string, formId: string, urlAjax: string): Promise<void> {
  event.preventDefault();

  const form = document.getElementById(formId) as HTMLFormElement;
  if (!form) {
    console.error(`Form with id ${formId} not found`);
    return;
  }

  // Crear un objeto para almacenar los datos del formulario
  const formData: { [key: string]: FormDataEntryValue } = {};

  Array.from(form.elements).forEach((element) => {
    if (element instanceof HTMLInputElement || element instanceof HTMLTextAreaElement || element instanceof HTMLSelectElement) {
      if (element.name) {
        formData[element.name] = element.value;
      }
    }
  });

  const jsonData = JSON.stringify(formData);

  try {
    const response = await fetch(urlAjax, {
      method: tipus,
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: jsonData,
    });

    if (!response.ok) {
      console.error('Error en la respuesta de la API:', response);
      throw new Error('Error en la sol·licitud AJAX');
    }

    const data = await response.json();

    const missatgeOk = document.getElementById('okMessage');
    const missatgeErr = document.getElementById('errMessage');

    if (data.status === 'success') {
      if (missatgeOk && missatgeErr) {
        missatgeOk.style.display = 'block';
        missatgeErr.style.display = 'none';
        missatgeOk.textContent = "L'operació s'ha realizat correctament a la base de dades.";

        limpiarFormulario(formId);

        setTimeout(() => {
          missatgeOk.style.display = 'none';
        }, 5000);
      }
    } else {
      if (missatgeOk && missatgeErr) {
        missatgeErr.style.display = 'block';
        missatgeOk.style.display = 'none';
        missatgeErr.textContent = "L'operació no s'ha pogut realizar correctament a la base de dades.";
      }
    }
  } catch (error) {
    const missatgeOk = document.getElementById('okMessage');
    const missatgeErr = document.getElementById('errMessage');
    if (missatgeOk && missatgeErr) {
      console.error('Error:', error);
      missatgeErr.style.display = 'block';
      missatgeOk.style.display = 'none';
      missatgeErr.textContent = 'Error: ';
    }
  }
}

function limpiarFormulario(formId: string) {
  const formulario = document.getElementById(formId) as HTMLFormElement;
  const inputs = formulario.querySelectorAll('input, textarea, select, trix-editor');

  inputs.forEach((input) => {
    if (input instanceof HTMLInputElement || input instanceof HTMLTextAreaElement) {
      input.value = ''; // Limpiar el valor del campo
    }
    if (input instanceof HTMLSelectElement) {
      input.selectedIndex = 0; // Limpiar el select (poner el primer valor por defecto)
    }
    if (input instanceof HTMLElement && input.tagName === 'TRIX-EDITOR') {
      // Limpiar el editor Trix (Type Assertion)
      const trixEditor = input as HTMLTrixEditorElement;
      trixEditor.editor.loadHTML(''); // Limpiar el contenido del editor Trix
    }
  });
}

// Declara el tipo extendido para TrixEditor
interface HTMLTrixEditorElement extends HTMLElement {
  editor: {
    loadHTML: (html: string) => void;
  };
}
