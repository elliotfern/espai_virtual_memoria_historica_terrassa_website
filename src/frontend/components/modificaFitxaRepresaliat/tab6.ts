import { Fitxa } from '../../types/types';
import { carregarBiografies } from './carregarBiografies';

export function tab6(fitxa: Fitxa) {
  carregarBiografies(fitxa.id);
}
