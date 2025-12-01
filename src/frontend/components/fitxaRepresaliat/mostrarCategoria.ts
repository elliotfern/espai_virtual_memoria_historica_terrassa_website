import { DOMAIN_WEB } from '../../config/constants';
import { getApiArray } from '../../services/api/http';
import { FitxaJudicial } from '../../types/types';
import { fitxaTipusRepressio } from './tab_tipus_repressio';

const categoriaCache: { [key: string]: FitxaJudicial | FitxaJudicial[] } = {};

export async function mostrarCategoria(categoriaNumerica: string, idPersona: number, lang: string): Promise<void> {
  const divInfo = document.getElementById('fitxa-categoria');
  if (!divInfo) return;

  divInfo.innerHTML = '';
  divInfo.dataset.categoria = categoriaNumerica;

  const devDirectory = DOMAIN_WEB;
  const urlAjax2 = getApiUrlForCategoria(categoriaNumerica, idPersona, devDirectory);
  if (urlAjax2 === null) {
    console.error('Categoria no válida:', categoriaNumerica);
    return;
  }

  if (!categoriaCache[categoriaNumerica]) {
    divInfo.innerHTML = `<div id="fitxa-view">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Carregant dades...</span>
      </div>
    </div>`;

    try {
      const fitxes: FitxaJudicial[] = await getApiArray<FitxaJudicial>(urlAjax2);

      if (fitxes.length === 0) {
        divInfo.innerHTML = `<p class="text-danger">⚠️ No s'han trobat dades.</p>`;
        categoriaCache[categoriaNumerica] = [];
        return;
      }

      divInfo.innerHTML = '';
      categoriaCache[categoriaNumerica] = fitxes;

      fitxaTipusRepressio(categoriaNumerica, fitxes, lang);
    } catch (error) {
      console.error('Error al obtenir la informació de la categoria:', error);
      divInfo.innerHTML = '<p class="text-danger">⚠️ Error al carregar les dades. Torna-ho a intentar més tard.</p>';
    }
  } else {
    const fitxa2 = categoriaCache[categoriaNumerica];
    if (fitxa2) {
      fitxaTipusRepressio(categoriaNumerica, fitxa2, lang);
    }
  }
}

function getApiUrlForCategoria(categoriaNumerica: string, idPersona: number, baseUrl: string): string | null {
  switch (parseInt(categoriaNumerica)) {
    case 1:
      return `${baseUrl}/api/afusellats/get/fitxaId?id=${idPersona}`;
    case 2:
      return `${baseUrl}/api/deportats/get/fitxaId?id=${idPersona}`;
    case 3:
      return `${baseUrl}/api/cost_huma_front/get/fitxaId?id=${idPersona}`;
    case 4:
    case 5:
      return `${baseUrl}/api/cost_huma_civils/get/fitxaId?id=${idPersona}`;
    case 6:
      return `${baseUrl}/api/processats/get/fitxaId?id=${idPersona}`;
    case 7:
      return `${baseUrl}/api/depurats/get/fitxaId?id=${idPersona}`;
    case 8:
      return `${baseUrl}/api/dones/get/fitxaId?id=${idPersona}`;
    case 10:
      return `${baseUrl}/api/exiliats/get/fitxaId?id=${idPersona}`;
    case 11:
      return null;
    case 12:
      return `${baseUrl}/api/preso_model/get/fitxaId?id=${idPersona}`;
    case 13:
      return null;
    case 16:
      return `${baseUrl}/api/detinguts_guardia_urbana/get/fitxaId?id=${idPersona}`;
    case 14:
      return `${baseUrl}/api/comite_solidaritat/get/fitxaId?id=${idPersona}`;
    case 15:
      return `${baseUrl}/api/responsabilitats_politiques/get/fitxaId?id=${idPersona}`;
    case 17:
      return `${baseUrl}/api/top/get/fitxaId?id=${idPersona}`;
    case 18:
      return `${baseUrl}/api/comite_relacions_solidaritat/get/fitxaId?id=${idPersona}`;
    default:
    case 22:
      return `${baseUrl}/api/cost_huma_front/get/fitxaId?id=${idPersona}`;
  }
}
