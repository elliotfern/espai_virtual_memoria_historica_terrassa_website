import { Fitxa } from '../../types/types';
import { taulaEdicions } from './taulaRegistreEdicions';

export function tab9(fitxa?: Fitxa) {
  if (fitxa) {
    taulaEdicions(fitxa?.id);
  }
}
