#!/usr/bin/env python3
"""Buat logo PNG transparan dari file sumber (gambar WhatsApp Anda).

Simpan file asli sebagai: docs/logo-source.png
Jalankan:
  D:\\Laragon\\laragon\\bin\\python\\python-3.13\\python.exe docs/process_logo.py
"""

from pathlib import Path

from PIL import Image

ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "docs" / "logo-source.png"
OUTPUT = ROOT / "public" / "images" / "tandis-logo.png"


def remove_background(img: Image.Image) -> Image.Image:
    img = img.convert("RGBA")
    px = img.load()
    w, h = img.size
    for y in range(h):
        for x in range(w):
            r, g, b, a = px[x, y]
            if r > 235 and g > 235 and b > 235:
                px[x, y] = (255, 255, 255, 0)
            elif r < 40 and g < 40 and b < 40:
                px[x, y] = (r, g, b, 0)
    return img


def main() -> None:
    if not SOURCE.exists():
        raise SystemExit(f"Letakkan gambar logo di: {SOURCE}")

    img = remove_background(Image.open(SOURCE))
    img.thumbnail((560, 200), Image.Resampling.LANCZOS)
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    img.save(OUTPUT, "PNG", optimize=True)
    print(f"Logo tersimpan: {OUTPUT} ({img.size[0]}x{img.size[1]})")


if __name__ == "__main__":
    main()
