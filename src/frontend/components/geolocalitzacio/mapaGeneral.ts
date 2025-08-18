// src/pages/mapaGeolocalitzacio/index.ts
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import L from 'leaflet';
import 'leaflet.markercluster';

import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

interface PersonGeo {
  id: number;
  slug: string;
  nom: string;
  cognom1: string;
  cognom2: string;
  lat: number;
  lng: number;
}

const API_URL = 'https://memoriaterrassa.cat/api/dades_personals/get/?type=geolocalitzacio';

function createPersonIcon(): L.Icon {
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

function isFiniteNumber(x: unknown): x is number {
  return typeof x === 'number' && Number.isFinite(x);
}
function toNumber(x: unknown): number | null {
  const n = typeof x === 'string' ? Number(x) : (x as number);
  return Number.isFinite(n) ? n : null;
}

function toPersonGeo(obj: unknown): PersonGeo | null {
  if (typeof obj !== 'object' || obj === null) return null;
  const r = obj as Record<string, unknown>;

  const idNum = toNumber(r.id);
  const latNum = toNumber(r.lat);
  const lngNum = toNumber(r.lng);

  const slug = typeof r.slug === 'string' ? r.slug : '';
  const nom = typeof r.nom === 'string' ? r.nom : '';
  const cognom1 = typeof r.cognom1 === 'string' ? r.cognom1 : '';
  const cognom2 = typeof r.cognom2 === 'string' ? r.cognom2 : '';

  if (idNum === null || latNum === null || lngNum === null) return null;

  return { id: idNum, slug, nom, cognom1, cognom2, lat: latNum, lng: lngNum };
}

async function fetchAllPeople(): Promise<PersonGeo[]> {
  const res = await fetch(API_URL, { headers: { 'Content-Type': 'application/json' } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  const data: unknown = await res.json();

  const arr = Array.isArray((data as Record<string, unknown>)?.['items']) ? ((data as Record<string, unknown>)['items'] as unknown[]) : Array.isArray(data) ? (data as unknown[]) : [];

  const ok = arr.map(toPersonGeo).filter((p): p is PersonGeo => p !== null);
  return ok.filter((p) => isFiniteNumber(p.lat) && isFiniteNumber(p.lng) && p.lat >= -90 && p.lat <= 90 && p.lng >= -180 && p.lng <= 180);
}

function fullName(p: PersonGeo): string {
  return [p.nom, p.cognom1, p.cognom2].filter(Boolean).join(' ') || 'Sense nom';
}

export async function renderMapaGeolocalitzacio(): Promise<void> {
  const root = document.getElementById('geolocalitzacio');
  if (!root) return;

  // Estructura interna del contenedor
  const mapId = 'map-all-persons';
  const msgId = `${mapId}-msg`;

  root.innerHTML = `
    <div id="${mapId}" style="width:100%;height:70vh;border-radius:10px;overflow:hidden;"></div>
    <div id="${msgId}" class="text-muted mt-2"></div>
  `;

  const mapEl = document.getElementById(mapId)!;
  const msgEl = document.getElementById(msgId)!;

  // Mapa base
  const map = L.map(mapEl, {
    center: [41.6, 1.8], // centro aprox. de Catalunya; se ajustará con fitBounds
    zoom: 7,
    preferCanvas: true,
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
  }).addTo(map);

  // Datos
  let people: PersonGeo[] = [];
  try {
    people = await fetchAllPeople();
  } catch (e) {
    console.error(e);
    msgEl.textContent = 'No s’han pogut carregar les dades de geolocalització.';
    return;
  }

  if (people.length === 0) {
    msgEl.textContent = 'No hi ha persones amb coordenades per mostrar.';
    return;
  }

  const icon = createPersonIcon();
  const cluster = L.markerClusterGroup({
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: false,
    disableClusteringAtZoom: 16, // a partir de 16, marcadores individuales
  });

  for (const p of people) {
    const name = fullName(p);
    const url = p.slug ? `https://memoriaterrassa.cat/fitxa/${encodeURIComponent(p.slug)}` : '';

    const popupHtml = `
      <div style="min-width:220px">
        <strong>${name}</strong><br/>
        ${url ? `<a href="${url}" target="_blank" rel="noopener">Ver fitxa</a>` : ''}
      </div>
    `.trim();

    const m = L.marker([p.lat, p.lng], { icon }).bindPopup(popupHtml);
    cluster.addLayer(m);
  }

  map.addLayer(cluster);

  // Encajar a todos los puntos
  const bounds = cluster.getBounds();
  if (bounds.isValid()) {
    map.fitBounds(bounds.pad(0.2));
  }

  // Corregir tamaño si el contenedor se pintó oculto
  setTimeout(() => map.invalidateSize(), 0);

  // Info
  msgEl.textContent = `S’estan mostrant ${people.length} persona(es) al mapa.`;

  // Opcional: mantener el mapa correcto en resizes
  const ro = new ResizeObserver(() => map.invalidateSize());
  ro.observe(mapEl);
}
