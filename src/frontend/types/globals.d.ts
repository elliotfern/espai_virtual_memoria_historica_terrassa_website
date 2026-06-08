declare global {
  interface Window {
    dataLayer: unknown[];
    gtag: GtagFunction;
  }

  type GtagFunction = {
    (command: 'js', date: Date): void;
    (command: 'config', targetId: string, params?: Record<string, unknown>): void;
    (command: 'consent', action: string, params: Record<string, unknown>): void;
    (command: 'event', eventName: string, params?: Record<string, unknown>): void;
    (command: string, ...args: unknown[]): void; // fallback genérico
  };
}
export {};
