export function parseUserAgent(ua: string): { browser: string; os: string } {
  let browser = 'Desconocido';
  let os = 'Desconocido';

  // Detectar navegador
  if (/Chrome\/(\d+)/.test(ua) && !/Edg\//.test(ua) && !/OPR\//.test(ua)) {
    browser = `Chrome ${ua.match(/Chrome\/(\d+)/)?.[1] || ''}`;
  } else if (/Edg\/(\d+)/.test(ua)) {
    browser = `Microsoft Edge ${ua.match(/Edg\/(\d+)/)?.[1] || ''}`;
  } else if (/Firefox\/(\d+)/.test(ua)) {
    browser = `Firefox ${ua.match(/Firefox\/(\d+)/)?.[1] || ''}`;
  } else if (/Safari\/(\d+)/.test(ua) && /Version\/(\d+)/.test(ua)) {
    browser = `Safari ${ua.match(/Version\/(\d+)/)?.[1] || ''}`;
  }

  // Detectar sistema operativo
  if (/Windows NT 10\.0/.test(ua)) os = 'Windows 10';
  else if (/Windows NT 11\.0/.test(ua)) os = 'Windows 11';
  else if (/Windows NT 6\.3/.test(ua)) os = 'Windows 8.1';
  else if (/Windows NT 6\.2/.test(ua)) os = 'Windows 8';
  else if (/Windows NT 6\.1/.test(ua)) os = 'Windows 7';
  else if (/Mac OS X/.test(ua)) os = 'macOS';
  else if (/Android/.test(ua)) os = 'Android';
  else if (/iPhone|iPad/.test(ua)) os = 'iOS';
  else if (/Linux/.test(ua)) os = 'Linux';

  return { browser, os };
}
