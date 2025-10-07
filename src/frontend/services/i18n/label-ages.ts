// labels-age.ts
import { makeDict } from './i18n';

type AgeKeys = 'year_one' | 'year_other';

export const LABELS_AGE = makeDict<AgeKeys>({
  ca: { year_one: 'any', year_other: 'anys' },
  es: { year_one: 'año', year_other: 'años' },
  en: { year_one: 'year', year_other: 'years' },
  fr: { year_one: 'an', year_other: 'ans' },
  it: { year_one: 'anno', year_other: 'anni' },
  pt: { year_one: 'ano', year_other: 'anos' },
});
