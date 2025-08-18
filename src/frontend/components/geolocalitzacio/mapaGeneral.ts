// src/pages/mapaGeolocalitzacio/index.ts
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import L from 'leaflet';
import 'leaflet.markercluster';

interface ApiResponseData {
  status: string;
  message: string;
  errors: unknown[];
  data: unknown[];
}

interface PersonGeo {
  id: number;
  slug: string;
  nom: string;
  cognom1: string;
  cognom2: string;
  lat: number;
  lng: number;
  adreca?: string;
  ciutat?: string;
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
  if (typeof x === 'number' && Number.isFinite(x)) return x;
  if (typeof x === 'string') {
    const n = Number(x);
    return Number.isFinite(n) ? n : null;
  }
  return null;
}

function toPersonGeo(obj: unknown): PersonGeo | null {
  if (typeof obj !== 'object' || obj === null) return null;
  const r = obj as Record<string, unknown>;

  const idNum = toNumber(r['id']);
  const latNum = toNumber(r['lat']);
  const lngNum = toNumber(r['lng']);
  if (idNum === null || latNum === null || lngNum === null) return null;

  const slug = typeof r['slug'] === 'string' ? r['slug'] : '';
  const nom = typeof r['nom'] === 'string' ? r['nom'] : '';
  const cognom1 = typeof r['cognom1'] === 'string' ? r['cognom1'] : '';
  const cognom2 = typeof r['cognom2'] === 'string' ? r['cognom2'] : '';
  const adreca = typeof r['adreca'] === 'string' ? r['adreca'] : undefined;
  const ciutat = typeof r['ciutat'] === 'string' ? r['ciutat'] : undefined;

  return { id: idNum, slug, nom, cognom1, cognom2, lat: latNum, lng: lngNum, adreca, ciutat };
}

async function fetchAllPeople(): Promise<PersonGeo[]> {
  const res = await fetch(API_URL, { headers: { 'Content-Type': 'application/json' } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);

  const raw: unknown = await res.json();

  let arrRaw: unknown[] = [];
  if (typeof raw === 'object' && raw !== null && Array.isArray((raw as ApiResponseData).data)) {
    arrRaw = (raw as ApiResponseData).data;
  } else if (Array.isArray(raw)) {
    arrRaw = raw as unknown[];
  }

  const parsed = arrRaw.map(toPersonGeo).filter((p): p is PersonGeo => p !== null);

  return parsed.filter((p) => isFiniteNumber(p.lat) && isFiniteNumber(p.lng) && p.lat >= -90 && p.lat <= 90 && p.lng >= -180 && p.lng <= 180);
}

function fullName(p: PersonGeo): string {
  return [p.nom, p.cognom1, p.cognom2].filter(Boolean).join(' ') || 'Sense nom';
}

function buildPopupHtml(p: PersonGeo): string {
  const name = fullName(p);
  const url = p.slug ? `https://memoriaterrassa.cat/fitxa/${encodeURIComponent(p.slug)}` : '';
  const addrLine = p.adreca && p.ciutat ? `${p.adreca}, ${p.ciutat}` : p.adreca ? p.adreca : p.ciutat ? p.ciutat : '';

  return `
    <div style="min-width:220px">
      <strong>${name}</strong><br/>
      ${addrLine ? `<div style="margin:6px 0 2px; line-height:1.35">${addrLine}</div>` : ''}
      ${url ? `<a href="${url}" target="_blank" rel="noopener">Ver fitxa</a>` : ''}
    </div>
  `.trim();
}

export async function renderMapaGeolocalitzacio(): Promise<void> {
  const root = document.getElementById('geolocalitzacio');
  if (!root) return;

  const mapId = 'map-all-persons';
  const msgId = `${mapId}-msg`;

  root.innerHTML = `
    <div id="${mapId}" style="width:100%;height:70vh;border-radius:10px;overflow:hidden;"></div>
    <div id="${msgId}" class="text-muted mt-2"></div>
  `;

  const mapEl = document.getElementById(mapId)!;
  const msgEl = document.getElementById(msgId)!;

  const map = L.map(mapEl, {
    center: [41.6, 1.8], // centro aprox. de Catalunya; se ajustará con fitBounds
    zoom: 7,
    preferCanvas: true,
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
  }).addTo(map);

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
    disableClusteringAtZoom: 16,
  });

  for (const p of people) {
    const m = L.marker([p.lat, p.lng], { icon }).bindPopup(buildPopupHtml(p));
    cluster.addLayer(m);
  }

  map.addLayer(cluster);

  const bounds = cluster.getBounds();
  if (bounds.isValid()) {
    map.fitBounds(bounds.pad(0.2));
  }

  setTimeout(() => map.invalidateSize(), 0);

  msgEl.textContent = `S’estan mostrant ${people.length} persona(es) al mapa.`;

  const ro = new ResizeObserver(() => map.invalidateSize());
  ro.observe(mapEl);
}
