
#!/usr/bin/env python3

import argparse
import re
import uuid
from pathlib import Path


PATRON = re.compile(
    r"^(\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2}-\d{3})"
    r"\.(jpg|jpeg|png|tif|tiff|webp)$",
    re.IGNORECASE,
)

EXTENSIONES = {".jpg", ".jpeg", ".png", ".tif", ".tiff", ".webp"}


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Renombra imágenes por fecha y hora del nombre original."
    )
    parser.add_argument("carpeta", type=Path)
    parser.add_argument(
        "--aplicar",
        action="store_true",
        help="Realiza el renombrado; por defecto solo simula.",
    )
    args = parser.parse_args()

    carpeta = args.carpeta.expanduser().resolve()

    if not carpeta.is_dir():
        parser.error(f"No existe el directorio: {carpeta}")

    
    def clave_orden(p: Path) -> tuple[bool, str]:
        coincidencia = PATRON.fullmatch(p.name)

        if coincidencia is not None:
            return (False, coincidencia.group(1))

        return (True, p.name.casefold())

    imagenes = sorted(
        (
            p for p in carpeta.iterdir()
            if p.is_file() and p.suffix.lower() in EXTENSIONES
        ),
        key=clave_orden,
    )


    if not imagenes:
        print("No se han encontrado imágenes.")
        return 0

    # Las imágenes con fecha en el nombre van primero en orden cronológico.
    # Los demás archivos quedan después, ordenados por nombre original.
    ancho = max(4, len(str(len(imagenes))))
    cambios = []

    for numero, origen in enumerate(imagenes, start=1):
        destino = carpeta / f"{numero:0{ancho}d}{origen.suffix.lower()}"
        cambios.append((origen, destino))

    print(f"Carpeta: {carpeta}")
    print(f"Imágenes: {len(cambios)}\n")

    for origen, destino in cambios:
        print(f"{origen.name} -> {destino.name}")

    if not args.aplicar:
        print("\nVista previa. No se ha modificado ningún archivo.")
        print("Añade --aplicar para realizar el renombrado.")
        return 0

    # Comprobar que no existan destinos ajenos a las imágenes que renombramos.
    nombres_origen = {origen.name for origen, _ in cambios}

    for _, destino in cambios:
        if destino.exists() and destino.name not in nombres_origen:
            print(f"ERROR: el destino ya existe: {destino}")
            return 1

    # Primero usamos nombres temporales para evitar colisiones.
    temporales = []

    try:
        for origen, destino in cambios:
            temporal = carpeta / f".tmp_{uuid.uuid4().hex}{origen.suffix.lower()}"
            origen.rename(temporal)
            temporales.append((temporal, destino, origen))

        for temporal, destino, origen in temporales:
            temporal.rename(destino)

    except OSError as error:
        print(f"ERROR durante el renombrado: {error}")
        print("Puede que queden archivos con nombres temporales.")
        print("Revisa el directorio antes de volver a ejecutar el script.")
        return 1

    print(f"\nRenombrado completado: {len(cambios)} archivos.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())