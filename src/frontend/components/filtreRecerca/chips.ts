// src/buscador/chips.ts
import Choices from 'choices.js';
import { SelectionState } from './store';
import { OpcionesFiltros } from './types';
import { FilterSpec } from './filters/types';

function labelFor(stateKey: string, value: string, opciones: OpcionesFiltros): string {
  switch (stateKey) {
    case 'provincies':
      return `Província: ${value}`;

    case 'municipis_naixement': {
      const m = opciones.municipis.find((x) => String(x.id) === value);
      return m ? `Naixement: ${m.ciutat}` : `Naixement: #${value}`;
    }

    case 'municipis_defuncio': {
      const m = opciones.municipis.find((x) => String(x.id) === value);
      return m ? `Defunció: ${m.ciutat}` : `Defunció: #${value}`;
    }

    case 'anys_naixement':
      return `Any naixement: ${value}`;

    case 'anys_defuncio':
      return `Any defunció: ${value}`;

    case 'estats': {
      const e = opciones.estats_civils.find((x) => String(x.id) === value);
      return e ? `Estat civil: ${e.estat_cat}` : `Estat civil: #${value}`;
    }

    case 'estudis': {
      const e = opciones.estudis.find((x) => String(x.id) === value);
      return e ? `Estudis: ${e.estudi_cat}` : `Estudis: #${value}`;
    }

    case 'oficis': {
      const o = opciones.oficis.find((x) => String(x.id) === value);
      return o ? `Ofici: ${o.ofici_cat}` : `Ofici: #${value}`;
    }

    case 'sexes':
      return `Sexe: ${value === '1' ? 'Home' : 'Dona'}`;

    case 'partits': {
      const p = opciones.partits.find((x) => String(x.id) === value);
      return p ? `Partit: ${p.partit_politic}${p.sigles ? ` (${p.sigles})` : ''}` : `Partit: #${value}`;
    }

    case 'sindicats': {
      const s = opciones.sindicats.find((x) => String(x.id) === value);
      return s ? `Sindicat: ${s.sindicat}` : `Sindicat: #${value}`;
    }

    case 'causes': {
      const c = opciones.causes.find((x) => String(x.id) === value);
      return c ? `Causa: ${c.causa_defuncio_ca}` : `Causa: #${value}`;
    }

    // Categoría repressió
    case 'categories': {
      const c = opciones.categories?.find((x) => String(x.id) === value);
      const name = c?.name ?? c?.categoria_ca ?? c?.categoria ?? `#${value}`;
      return `Categoria: ${name}`;
    }

    // Filtros específicos de exili
    case 'anys_exili':
      return `Any exili: ${value}`;

    case 'primer_desti_exili': {
      const m = opciones.municipis.find((x) => String(x.id) === value);
      return m ? `Primer destí: ${m.ciutat}` : `Primer destí: #${value}`;
    }

    case 'deportat':
      return `Deportat: ${value === '1' ? 'Sí' : 'No'}`;

    case 'resistencia_fr':
      return `Resistència FR: ${value === '1' ? 'Sí' : 'No'}`;

    default:
      // fallback genérico
      return `${stateKey}: ${value}`;
  }
}

/**
 * Pinta chips de filtros activos y permite “quitar” cada chip
 * (quitando el valor en Choices) y llama a `onChanged()` después.
 *
 * @param selection   Estado actual de selección (Record<string, string[]>)
 * @param opciones    Catálogo para resolver etiquetas
 * @param filters     Registro de filtros (para mapear stateKey -> id del select)
 * @param choicesMap  Mapa idSelect -> instancia de Choices
 * @param onChanged   Callback a ejecutar tras quitar un chip (normalmente onCriteriaChange)
 */
export function renderActiveChips(selection: SelectionState, opciones: OpcionesFiltros, filters: FilterSpec[], choicesMap: Map<string, Choices>, onChanged: () => void): void {
  // Asegura contenedor
  let chips = document.getElementById('filtres-actius');
  if (!chips) {
    chips = document.createElement('div');
    chips.id = 'filtres-actius';
    chips.style.margin = '8px 0';
    const resultados = document.getElementById('resultados');
    resultados?.insertBefore(chips, resultados.firstChild ?? null);
  }

  // Mapa rápido stateKey -> idSelect (a partir de registry)
  const keyToId = new Map<string, string>();
  filters.forEach((f) => keyToId.set(f.stateKey, f.id));

  // Genera HTML chips
  const parts: string[] = [];
  Object.keys(selection).forEach((stateKey) => {
    const vals = selection[stateKey] ?? [];
    vals.forEach((value) => {
      const text = labelFor(stateKey, value, opciones);
      parts.push(
        `<span class="chip" data-key="${stateKey}" data-value="${value}" style="display:inline-flex;align-items:center;margin:4px; padding:2px 8px; border:1px solid #ddd; border-radius:999px; font-size:12px;">
          ${text}
          <button type="button" aria-label="Treu filtre" style="margin-left:6px;border:none;background:transparent;cursor:pointer;font-weight:bold;">×</button>
        </span>`
      );
    });
  });

  chips.innerHTML = parts.length ? parts.join('') : `<span style="color:#666;font-size:12px;">Sense filtres actius</span>`;

  // Listeners para quitar chip
  chips.querySelectorAll<HTMLButtonElement>('.chip button').forEach((btn) => {
    btn.addEventListener('click', (ev) => {
      const wrap = (ev.currentTarget as HTMLElement).closest('.chip') as HTMLElement | null;
      if (!wrap) return;

      const key = wrap.getAttribute('data-key') ?? '';
      const val = wrap.getAttribute('data-value') ?? '';
      const selectId = keyToId.get(key);
      if (!selectId) return;

      const ch = choicesMap.get(selectId);
      if (!ch) return;

      // Quita el valor en Choices y dispara recálculo externo
      ch.removeActiveItemsByValue(val);
      onChanged();
    });
  });
}
