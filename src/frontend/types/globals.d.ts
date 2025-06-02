declare global {
  interface Window {
    dataLayer: unknown[];
    gtag: GtagFunction;
  }
}
export {};
