/// <reference lib="webworker" />
// src/buscador/filters.worker.ts

export {}; // convierte el fichero en módulo y evita globals de TS

// Mensajes estrictos
type InitMsg = { type: 'init'; index: Array<{ i: number; s: string }> };
type FilterMsg = { type: 'filter'; id: number; texto: string };
type InMsg = InitMsg | FilterMsg;

type OutReady = { type: 'ready' };
type OutFiltered = { type: 'filtered'; id: number; idx: number[] };

// Índice compacto para búsqueda
let INDEX: Array<{ i: number; s: string }> = [];

self.onmessage = (e: MessageEvent<InMsg>): void => {
  const msg = e.data;

  if (msg.type === 'init') {
    INDEX = msg.index ?? [];
    const ready: OutReady = { type: 'ready' };
    self.postMessage(ready);
    return;
  }

  if (msg.type === 'filter') {
    const t = (msg.texto || '').trim().toLowerCase();

    if (t.length === 0) {
      const allIdx = INDEX.map((x) => x.i);
      const outAll: OutFiltered = { type: 'filtered', id: msg.id, idx: allIdx };
      self.postMessage(outAll);
      return;
    }

    const idx: number[] = [];
    for (let k = 0; k < INDEX.length; k += 1) {
      if (INDEX[k].s.includes(t)) idx.push(INDEX[k].i);
    }
    const out: OutFiltered = { type: 'filtered', id: msg.id, idx };
    self.postMessage(out);
  }
};
