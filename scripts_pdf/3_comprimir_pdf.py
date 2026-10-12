
#!/usr/bin/env python3

import argparse
import shutil
import subprocess
import sys
from pathlib import Path


# Perfil equilibrado: resolución objetivo de 150 PPP
# Perfil estricto: resolución objetivo de 100 PPP
RESOLUCIO = 150


def format_mida(mida: int) -> str:
    """Converteix bytes a una mida llegible."""
    if mida >= 1024 * 1024:
        return f"{mida / (1024 * 1024):.2f} MB"
    return f"{mida / 1024:.2f} KB"


def comprimir_pdf(entrada: Path, sortida: Path) -> bool:
    """Comprimeix un PDF sense modificar el fitxer original."""

    if not entrada.is_file():
        print(f"Error: no existeix el fitxer: {entrada}", file=sys.stderr)
        return False

    if entrada.suffix.lower() != ".pdf":
        print("Error: el fitxer d'entrada ha de ser un PDF.", file=sys.stderr)
        return False

    if entrada.resolve() == sortida.resolve():
        print("Error: l'entrada i la sortida no poden ser el mateix fitxer.",
              file=sys.stderr)
        return False

    if sortida.exists():
        print(f"Error: el fitxer de sortida ja existeix: {sortida}",
              file=sys.stderr)
        print("Tria un altre nom o mou el fitxer existent.")
        return False

    gs = shutil.which("gs")
    if gs is None:
        print("Error: Ghostscript no està instal·lat o no és al PATH.",
              file=sys.stderr)
        return False

    sortida.parent.mkdir(parents=True, exist_ok=True)

    ordre = [
        gs,
        "-sDEVICE=pdfwrite",
        "-dCompatibilityLevel=1.7",
        "-dNOPAUSE",
        "-dBATCH",
        "-dQUIET",
        "-dDetectDuplicateImages=true",
        "-dCompressFonts=true",
        "-dSubsetFonts=true",
        "-dAutoRotatePages=/None",
        "-dDownsampleColorImages=true",
        "-dColorImageDownsampleType=/Bicubic",
        f"-dColorImageResolution={RESOLUCIO}",
        "-dDownsampleGrayImages=true",
        "-dGrayImageDownsampleType=/Bicubic",
        f"-dGrayImageResolution={RESOLUCIO}",
        "-dDownsampleMonoImages=true",
        "-dMonoImageDownsampleType=/Subsample",
        "-dMonoImageResolution=300",
        "-dPassThroughJPEGImages=false",
        "-dJPEGQ=65",
        f"-sOutputFile={sortida}",
        str(entrada),
    ]

    try:
        resultat = subprocess.run(
            ordre,
            check=False,
            capture_output=True,
            text=True,
        )
    except OSError as error:
        print(f"Error en executar Ghostscript: {error}", file=sys.stderr)
        return False

    if resultat.returncode != 0:
        print("Error durant la compressió.", file=sys.stderr)
        if resultat.stderr:
            print(resultat.stderr.strip(), file=sys.stderr)
        if sortida.exists():
            sortida.unlink()
        return False

    if not sortida.is_file() or sortida.stat().st_size == 0:
        print("Error: no s'ha generat un PDF vàlid.", file=sys.stderr)
        if sortida.exists():
            sortida.unlink()
        return False

    mida_original = entrada.stat().st_size
    mida_final = sortida.stat().st_size
    diferencia = mida_original - mida_final

    print("\nCompressió completada")
    print("---------------------")
    print(f"Original:     {entrada}")
    print(f"Comprès:      {sortida}")
    print(f"Mida original: {format_mida(mida_original)}")
    print(f"Mida final:    {format_mida(mida_final)}")

    if diferencia > 0:
        percentatge = diferencia / mida_original * 100
        print(f"Estalvi:       {format_mida(diferencia)} ({percentatge:.1f} %)")
    elif diferencia < 0:
        increment = abs(diferencia)
        percentatge = increment / mida_original * 100
        print(
            f"Increment:     {format_mida(increment)} "
            f"({percentatge:.1f} %)"
        )
        print("El PDF comprimit és més gran que l'original.")
    else:
        print("La mida del fitxer no ha canviat.")

    print("\nRevisa algunes pàgines per comprovar-ne la llegibilitat.")
    return True


def main() -> int:
    parser = argparse.ArgumentParser(
        description=(
            "Comprimeix un PDF amb un perfil equilibrat "
            "sense modificar l'original."
        )
    )
    parser.add_argument(
        "entrada",
        type=Path,
        help="Ruta del PDF original",
    )
    parser.add_argument(
        "-o",
        "--sortida",
        type=Path,
        help="Ruta del PDF comprimit (opcional)",
    )

    args = parser.parse_args()
    entrada = args.entrada.expanduser().resolve()

    if args.sortida:
        sortida = args.sortida.expanduser().resolve()
    else:
        sortida = entrada.with_name(
            f"{entrada.stem}_comprimido.pdf"
        )

    return 0 if comprimir_pdf(entrada, sortida) else 1


if __name__ == "__main__":
    raise SystemExit(main())