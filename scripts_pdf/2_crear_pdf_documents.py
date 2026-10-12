#!/usr/bin/env python3
import argparse
import re
import sys
import tempfile
from pathlib import Path
from PIL import Image, ImageOps
from reportlab.lib.pagesizes import A4
from reportlab.lib.units import mm
from reportlab.pdfgen import canvas

# ============================================================

# CONFIGURACIÓN

# ============================================================
CABECERA = "Archivo del Tribunal Militar Territorial Tercero de Barcelona"
PIE = "Espai Virtual de la Memòria Històrica de Terrassa - EVMHT"
EXTENSIONES_IMAGEN = {
    ".jpg",
    ".jpeg",
    ".png",
    ".tif",
    ".tiff",
    ".bmp",
    ".webp",
}

# ============================================================

# ORDEN DE LAS IMÁGENES

# ============================================================

def clave_orden(ruta: Path) -> tuple[str, str]:
    """
    Ordena primero las imágenes cuyo nombre empieza con:
    YYYY-MM-DD-HH-MM-SS-mmm
    El resto se ordena alfabéticamente después.
    Funciona también con nombres secuenciales como 0001.jpg.
    """
    coincidencia = re.match(
        r"^(\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2}-\d{3})",
        ruta.name,
    )
    if coincidencia:
        return ("0", coincidencia.group(1))
    return ("1", ruta.name.casefold())

def obtener_imagenes(
    carpeta: Path,
    recursivo: bool = False,
) -> list[Path]:
    """Obtiene las imágenes admitidas y las ordena."""
    if recursivo:
        archivos = (
            ruta
            for ruta in carpeta.rglob("*")
            if ruta.is_file()
            and ruta.suffix.lower() in EXTENSIONES_IMAGEN
        )
    else:
        archivos = (
            ruta
            for ruta in carpeta.iterdir()
            if ruta.is_file()
            and ruta.suffix.lower() in EXTENSIONES_IMAGEN
        )
    return sorted(archivos, key=clave_orden)

# ============================================================

# PREPARACIÓN Y OPTIMIZACIÓN DE IMÁGENES

# ============================================================

def preparar_imagen(
    ruta: Path,
    ppp: int,
    calidad: int,
    escala_grises: bool,
    directorio_temporal: Path,
) -> Path:
    """
    Abre una imagen, aplica la orientación EXIF, convierte a
    escala de grises si procede y reduce su tamaño sin ampliar
    imágenes pequeñas. Genera un JPEG temporal.
    """
    with Image.open(ruta) as original:
        # Respeta la orientación almacenada en los metadatos EXIF.
        imagen = ImageOps.exif_transpose(original)
        imagen.load()
    try:
        # Aplanar transparencias sobre fondo blanco.
        if imagen.mode in ("RGBA", "LA") or (
            imagen.mode == "P" and "transparency" in imagen.info
        ):
            rgba = imagen.convert("RGBA")
            fondo = Image.new("RGBA", rgba.size, "white")
            fondo.alpha_composite(rgba)
            imagen = fondo.convert("RGB")
        else:
            imagen = imagen.convert("RGB")
        # Convertir a escala de grises por defecto.
        if escala_grises:
            imagen = ImageOps.grayscale(imagen)
            imagen = imagen.convert("RGB")
        # Reducir a la resolución objetivo, sin ampliar.
        ancho_maximo = round((210 / 25.4) * ppp)
        alto_maximo = round((297 / 25.4) * ppp)
        imagen.thumbnail(
            (ancho_maximo, alto_maximo),
            Image.Resampling.LANCZOS,
        )
        destino_temporal = (
            directorio_temporal / f"{ruta.stem}_optimizada.jpg"
        )
        imagen.save(
            destino_temporal,
            format="JPEG",
            quality=calidad,
            optimize=True,
            progressive=True,
            subsampling=0,
        )
        return destino_temporal
    finally:
        imagen.close()

# ============================================================

# CREACIÓN DEL PDF

# ============================================================

# ============================================================
# PORTADA
# ============================================================

def dibujar_portada(
    pdf: canvas.Canvas,
    ancho_pagina: float,
    alto_pagina: float,
    margen: float,
    nombre_procesado: str,
    numero_causa: str,
) -> None:
    """Dibuja una portada con marco negro y área central blanca."""
    pdf.setFillColorRGB(0, 0, 0)
    pdf.rect(0, 0, ancho_pagina, alto_pagina, fill=1, stroke=0)
    pdf.setFillColorRGB(1, 1, 1)
    pdf.rect(
        margen,
        margen,
        ancho_pagina - 2 * margen,
        alto_pagina - 2 * margen,
        fill=1,
        stroke=0,
    )
    centro_x = ancho_pagina / 2
    alto_area = alto_pagina - 2 * margen
    arriba_area = alto_pagina - margen
    pdf.setFillColorRGB(0, 0, 0)
    pdf.setFont("Helvetica-Bold", 14)
    pdf.drawCentredString(
        centro_x,
        arriba_area - alto_area * 0.20,
        CABECERA,
    )
    pdf.setFont("Helvetica", 12)
    pdf.drawCentredString(
        centro_x,
        arriba_area - alto_area * 0.42,
        f"Nom del processat: {nombre_procesado}",
    )
    pdf.drawCentredString(
        centro_x,
        arriba_area - alto_area * 0.47,
        f"Número de causa: {numero_causa}",
    )
    pdf.setFont("Helvetica", 10)
    pdf.drawCentredString(
        centro_x,
        arriba_area - alto_area * 0.70,
        "Digitalització realitzada per l'Espai Virtual de la Memòria Històrica de Terrassa - EVMHT",
    )
    pdf.drawCentredString(
        centro_x,
        arriba_area - alto_area * 0.75,
        "https://memoriaterrassa.cat",
    )
    pdf.drawCentredString(
        centro_x,
        arriba_area - alto_area * 0.80,
        "Any 2026",
    )

def crear_pdf(
    carpeta: Path,
    salida: Path,
    nombre_procesado: str,
    numero_causa: str,
    ppp: int = 180,
    calidad: int = 85,
    margen_mm: float = 12,
    recursivo: bool = False,
    color: bool = False,
) -> None:
    """Genera un PDF A4 con una imagen por página."""
    if not carpeta.is_dir():
        raise ValueError(
            f"La carpeta de origen no existe: {carpeta}"
        )
    if ppp < 72 or ppp > 600:
        raise ValueError(
            "El valor de PPP debe estar entre 72 y 600."
        )
    if not 40 <= calidad <= 100:
        raise ValueError(
            "La calidad JPEG debe estar entre 40 y 100."
        )
    if margen_mm < 0 or margen_mm > 40:
        raise ValueError(
            "El margen debe estar entre 0 y 40 mm."
        )
    carpeta = carpeta.resolve()
    salida = salida.resolve()
    if salida.suffix.lower() != ".pdf":
        raise ValueError(
            "El archivo de salida debe tener extensión .pdf."
        )
    if salida.exists() and salida.is_dir():
        raise ValueError(
            f"La ruta de salida corresponde a un directorio: {salida}"
        )
    if salida.exists() and salida in carpeta.parents:
        raise ValueError(
            "El PDF de salida no puede sobrescribir un archivo "
            "dentro de la carpeta de imágenes."
        )
    imagenes = obtener_imagenes(carpeta, recursivo)
    if not imagenes:
        raise ValueError(
            f"No se encontraron imágenes en: {carpeta}"
        )
    salida.parent.mkdir(parents=True, exist_ok=True)
    ancho_pagina, alto_pagina = A4
    margen = margen_mm * mm
    alto_cabecera = 20 * mm
    alto_pie = 20 * mm
    ancho_util = ancho_pagina - (2 * margen)
    alto_util = (
        alto_pagina
        - alto_cabecera
        - alto_pie
        - (2 * margen)
    )
    if ancho_util <= 0 or alto_util <= 0:
        raise ValueError(
            "El margen deja un área útil insuficiente."
        )
    print(f"Carpeta: {carpeta}")
    print(f"Imágenes encontradas: {len(imagenes)}")
    print(f"PDF de salida: {salida}")
    print(f"Resolución objetivo: {ppp} PPP")
    print(f"Escala de grises: {'No' if color else 'Sí'}")
    print()
    pdf = canvas.Canvas(
        str(salida),
        pagesize=A4,
        pageCompression=1,
    )
    pdf.setTitle(salida.stem)
    pdf.setAuthor(PIE)
    # Primera página: portada personalizada, sin cabecera ni pie habituales.
    dibujar_portada(
        pdf=pdf,
        ancho_pagina=ancho_pagina,
        alto_pagina=alto_pagina,
        margen=margen,
        nombre_procesado=nombre_procesado,
        numero_causa=numero_causa,
    )
    pdf.showPage()
    procesadas = 0
    errores: list[str] = []
    try:
        with tempfile.TemporaryDirectory(
            prefix="crear_pdf_documentos_"
        ) as temporal:
            directorio_temporal = Path(temporal)
            for indice, ruta in enumerate(imagenes, start=1):
                print(f"[{indice}/{len(imagenes)}] {ruta.name}")
                try:
                    imagen_preparada = preparar_imagen(
                        ruta=ruta,
                        ppp=ppp,
                        calidad=calidad,
                        escala_grises=not color,
                        directorio_temporal=directorio_temporal,
                    )
                    with Image.open(imagen_preparada) as imagen:
                        ancho_imagen, alto_imagen = imagen.size
                    # Ajustar la imagen sin recortar ni deformar.
                    escala = min(
                        ancho_util / ancho_imagen,
                        alto_util / alto_imagen,
                    )
                    ancho_final = ancho_imagen * escala
                    alto_final = alto_imagen * escala
                    x = (ancho_pagina - ancho_final) / 2
                    y = (
                        alto_pie
                        + margen
                        + (alto_util - alto_final) / 2
                    )
                    # Fondo negro de página completa.
                    pdf.setFillColorRGB(0, 0, 0)
                    pdf.rect(
                        0,
                        0,
                        ancho_pagina,
                        alto_pagina,
                        fill=1,
                        stroke=0,
                    )
                    # Cabecera blanca en negrita.
                    pdf.setFillColorRGB(1, 1, 1)
                    pdf.setFont("Helvetica-Bold", 12)
                    pdf.drawCentredString(
                        ancho_pagina / 2,
                        alto_pagina - 13 * mm,
                        CABECERA,
                    )
                    # Pie blanco en negrita.
                    pdf.setFont("Helvetica-Bold", 10)
                    pdf.drawCentredString(
                        ancho_pagina / 2,
                        10 * mm,
                        PIE,
                    )
                    # Número de página debajo del pie.
                    pdf.setFont("Helvetica", 9)
                    pdf.drawCentredString(
                        ancho_pagina / 2,
                        4 * mm,
                        f"Pàgina {indice + 1} de {len(imagenes) + 1}",
                    )
                    # Imagen centrada y proporcional.
                    pdf.drawImage(
                        str(imagen_preparada),
                        x,
                        y,
                        width=ancho_final,
                        height=alto_final,
                        preserveAspectRatio=True,
                        mask="auto",
                    )
                    pdf.showPage()
                    procesadas += 1
                except Exception as error:
                    mensaje = f"{ruta.name}: {error}"
                    errores.append(mensaje)
                    print(f"  [ERROR] {mensaje}")
        if procesadas == 0:
            raise RuntimeError(
                "No se ha podido procesar ninguna imagen. "
                "No se ha generado un PDF válido."
            )
        pdf.save()
    except Exception:
        if salida.exists():
            salida.unlink()
        raise
    print()
    print(f"PDF generado: {salida}")
    print(f"Páginas totales del PDF: {procesadas + 1}")
    print(f"Imágenes procesadas: {procesadas}")
    if errores:
        print(f"Imágenes con errores: {len(errores)}")
        for error in errores:
            print(f"  - {error}")

# ============================================================

# ARGUMENTOS DE CONSOLA

# ============================================================

def main() -> None:
    parser = argparse.ArgumentParser(
        description=(
            "Crea un PDF A4 de documentos históricos "
            "con portada personalizada y fondo negro, sin corrección automática por OCR."
        )
    )
    parser.add_argument(
        "carpeta",
        type=Path,
        help="Carpeta que contiene las imágenes.",
    )
    parser.add_argument(
        "-o",
        "--salida",
        type=Path,
        default=Path("documentos.pdf"),
        help="Ruta del PDF de salida.",
    )
    parser.add_argument(
        "--ppp",
        type=int,
        default=180,
        help="Resolución objetivo en PPP (por defecto: 180).",
    )
    parser.add_argument(
        "--calidad",
        type=int,
        default=85,
        help="Calidad JPEG entre 40 y 100 (por defecto: 85).",
    )
    parser.add_argument(
        "--margen",
        type=float,
        default=12,
        help="Margen en milímetros (por defecto: 12).",
    )
    parser.add_argument(
        "--recursivo",
        action="store_true",
        help="Buscar imágenes también en subcarpetas.",
    )
    parser.add_argument(
        "--color",
        action="store_true",
        help="Conservar el color en lugar de usar escala de grises.",
    )
    args = parser.parse_args()
    try:
        nombre_procesado = input("Nom del processat: ").strip()
        numero_causa = input("Número de causa: ").strip()
        if not nombre_procesado:
            raise ValueError("El nombre del procesado no puede estar vacío.")
        if not numero_causa:
            raise ValueError("El número de causa no puede estar vacío.")
        crear_pdf(
            carpeta=args.carpeta,
            salida=args.salida,
            nombre_procesado=nombre_procesado,
            numero_causa=numero_causa,
            ppp=args.ppp,
            calidad=args.calidad,
            margen_mm=args.margen,
            recursivo=args.recursivo,
            color=args.color,
        )
    except (ValueError, RuntimeError, OSError) as error:
        print(f"\nError: {error}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    main()
