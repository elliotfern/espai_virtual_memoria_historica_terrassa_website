export async function fetchData<T>(url: string, init?: RequestInit & { noCache?: boolean }): Promise<T> {
  const withBuster = init?.noCache ? `${url}${url.includes('?') ? '&' : '?'}_=${Date.now()}` : url;

  const resp = await fetch(withBuster, {
    ...init,
    cache: init?.noCache ? 'no-store' : init?.cache,
    headers: {
      method: 'GET',
      ...(init?.headers || {}),
      ...(init?.noCache
        ? {
            'Cache-Control': 'no-store, no-cache, must-revalidate',
            Pragma: 'no-cache',
          }
        : {}),
    },
  });
  if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
  return resp.json() as Promise<T>;
}
