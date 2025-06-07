/// <reference types="vite/client" />

interface ImportMetaEnv {
  VITE_API_BASE_URL: string; // Agrega las variables que usas
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
