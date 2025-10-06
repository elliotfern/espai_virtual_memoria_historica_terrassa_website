import { initBuscador } from '../../../components/cercadorHomepage/cercadorPaginaInici';
import { llistatMembresEquip } from './membresEquip';

export async function homePage(lang: string): Promise<void> {
  initBuscador();
  llistatMembresEquip(lang);
}
