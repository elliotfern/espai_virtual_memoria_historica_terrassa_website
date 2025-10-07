// src/pages/fitxaRepresaliat/tabs/tab9.ts
import type { Fitxa } from '../../../types/types';
// Leaflet (mapa base)
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import { LABELS_MAP } from '../../../services/i18n/labels-tab9';
import { t } from '../../../services/i18n/i18n';

function crearIconoPersona() {
  // Icono SVG inline para no depender de assets
  return L.icon({
    iconUrl:
      'data:image/svg+xml;utf8,' +
      encodeURIComponent(
        `<svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34">
          <circle cx="17" cy="17" r="14" fill="#2e7d32"/>
          <circle cx="17" cy="13" r="5" fill="white" opacity="0.9"/>
          <path d="M8,26c2.5-4.5,7-6,9-6s6.5,1.5,9,6" fill="white" opacity="0.9"/>
        </svg>`
      ),
    iconSize: [34, 34],
    iconAnchor: [17, 17],
    popupAnchor: [0, -18],
  });
}

export function renderTab9(fitxa: Fitxa, label: string, lang: string): void {
  const divInfo = document.getElementById('fitxa');
  if (!divInfo) return;

  // Construcción del HTML (todo dinámico)
  const mapId = 'map-tab9';
  divInfo.innerHTML = `
  <h3 class="titolSeccio">${label}</h3>

  <div style="margin-top:20px;margin-bottom:5px">
    <p><span class='blau1'>${t(LABELS_MAP, 'mapIntro', lang)}</span></p>
  </div>

  <div id="${mapId}" style="width:100%;height:60vh;border-radius:10px;overflow:hidden;margin-top:30px"></div>
  <div id="${mapId}-msg" style="margin-top:8px;font-size:.9rem;color:#555;"></div>
`;

  const msg = document.getElementById(`${mapId}-msg`);

  // Validaciones mínimas
  const lat = Number(fitxa.lat);
  const lng = Number(fitxa.lng);
  const adreca = fitxa.adreca;
  const tipus_ca = fitxa.tipus_ca;
  const num = fitxa.adreca_num;
  const municipi = fitxa.ciutat_residencia;
  const tieneCoords = Number.isFinite(lat) && Number.isFinite(lng);

  if (!tieneCoords) {
    if (msg) msg.textContent = 'No hi ha coordenades disponibles per aquesta persona.';
    return;
  }

  const nombre = [fitxa.nom, fitxa.cognom1, fitxa.cognom2].filter(Boolean).join(' ') || 'Sense nom';

  const slug = fitxa.slug;
  const urlFicha = slug ? `https://memoriaterrassa.cat/fitxa/${encodeURIComponent(slug)}` : '';

  // Crear mapa
  const container = document.getElementById(mapId)!;
  const map = L.map(container, {
    center: [lat, lng],
    zoom: 16,
    preferCanvas: true,
  });

  // Capa base: OSM (ideal para desarrollo/uso ligero)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
  }).addTo(map);

  // Marcador único
  const icono = crearIconoPersona();
  const marker = L.marker([lat, lng], { icon: icono }).addTo(map);

  const popupHtml = `
    <div style="min-width:200px">
      <strong>${nombre}</strong><br/>
       ${adreca ? `<div style="margin:6px 0 2px 0; line-height:1.35">${tipus_ca?.trim() || ''} ${adreca}, ${num || ''} - ${municipi}</div>` : ''}
      ${urlFicha ? `<a href="${urlFicha}" rel="noopener">Veure fitxa</a>` : ''}
    </div>
  `.trim();

  marker.bindPopup(popupHtml);

  // Asegura cálculo correcto del tamaño y luego vuela al zoom deseado
  setTimeout(() => {
    map.invalidateSize();
    map.flyTo([lat, lng], 16, { duration: 0.6 }); // zoom calle
  }, 0);
}
