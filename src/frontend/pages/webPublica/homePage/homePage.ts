import { initBuscador } from '../../../components/cercadorHomepage/cercadorPaginaInici';
import { llistatMembresEquip } from './membresEquip';
import { traduccionsHomePagei18n } from './traduccionsHomePage';

export async function homePage(lang: string): Promise<void> {
  initBuscador();
  llistatMembresEquip(lang);
  traduccionsHomePagei18n(lang);
}
