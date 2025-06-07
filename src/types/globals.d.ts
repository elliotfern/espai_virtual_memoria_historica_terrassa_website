declare global {
  interface Window {
    dataLayer: unknown[];
    gtag: GtagFunction;
  }
  // Definimos el tipo de GtagFunction
  type GtagFunction = (command: string, eventName: string, eventParams: Record<string, unknown>) => void;
}
export {};
