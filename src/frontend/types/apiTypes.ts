export type ApiUrls = {
  GET: {
    CATEGORIES_REPRESSIO: (lang: string) => string;
    MUNICIPI_ID: (id: number) => string;
  };
  POST: {
    MUNICIPI: string;
    MUNICIPI_ESTAT: string;
    MUNICIPI_COMUNITAT: string;
    MUNICIPI_COMARCA: string;
    CAUSA_MORT: string;
    CATEGORIA_REPRESSIO: string;
    SUB_SECTOR_ECONOMIC: string;
    OFICI: string;
    CARREC_EMPRESA: string;
  };
  PUT: {
    MUNICIPI: string;
    MUNICIPI_ESTAT: string;
    MUNICIPI_COMUNITAT: string;
    MUNICIPI_COMARCA: string;
    CAUSA_MORT: string;
    CATEGORIA_REPRESSIO: string;
    SUB_SECTOR_ECONOMIC: string;
    OFICI: string;
    CARREC_EMPRESA: string;
  };
  DELETE: {
    MUNICIPI: string;
    MUNICIPI_ESTAT: string;
    MUNICIPI_COMUNITAT: string;
    MUNICIPI_COMARCA: string;
    CAUSA_MORT: string;
    CATEGORIA_REPRESSIO: string;
    SUB_SECTOR_ECONOMIC: string;
    OFICI: string;
    CARREC_EMPRESA: string;
  };
};
