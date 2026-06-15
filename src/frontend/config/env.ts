export const ENV = {
  apiBaseUrl: process.env.API_BASE_URL as string,
  appEnv: process.env.APP_ENV as string,
  domainImg: process.env.DOMAIN_IMG as string,
  domainWeb: process.env.DOMAIN_WEB as string,

  // helpers útiles
  isLocal: process.env.APP_ENV === 'local',
  isProd: process.env.APP_ENV === 'production',
  isStaging: process.env.APP_ENV === 'staging',
} as const;
