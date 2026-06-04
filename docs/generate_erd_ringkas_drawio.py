#!/usr/bin/env python3
"""Generate docs/ERD-konseptual-ringkas.drawio — clean layout, fewer crossing lines."""

import html
from pathlib import Path


def xml_attr(s: str) -> str:
    return html.escape(s, quote=True)


def entity_block(eid, x, y, w, h, title, lines, fill, stroke):
    """Single oval: entity name + attributes (PK first, underlined)."""
    parts = [f"&lt;b&gt;{xml_attr(title)}&lt;/b&gt;", "────────"]
    for i, line in enumerate(lines):
        if i == 0:
            parts.append(f"&lt;u&gt;{xml_attr(line)}&lt;/u&gt;")
        else:
            parts.append(xml_attr(line))
    val = "&#xa;".join(parts)
    style = (
        f"ellipse;whiteSpace=wrap;html=1;align=center;verticalAlign=middle;"
        f"fillColor={fill};strokeColor={stroke};spacingTop=4;spacingBottom=4;"
    )
    return f"""<mxCell id="{eid}" value="{val}" style="{style}" vertex="1" parent="1">
  <mxGeometry x="{x}" y="{y}" width="{w}" height="{h}" as="geometry"/>
</mxCell>"""


def rhombus(rid, x, y, label):
    return f"""<mxCell id="{rid}" value="{xml_attr(label)}" style="rhombus;whiteSpace=wrap;html=1;fillColor=#FFF2CC;strokeColor=#D6B656;" vertex="1" parent="1">
  <mxGeometry x="{x}" y="{y}" width="88" height="48" as="geometry"/>
</mxCell>"""


def connect(eid, src, tgt):
    return f"""<mxCell id="{eid}" style="endArrow=none;html=1;rounded=0;strokeColor=#333333;" edge="1" parent="1" source="{src}" target="{tgt}">
  <mxGeometry relative="1" as="geometry"/>
</mxCell>"""


def card(cid, x, y, text):
    return f"""<mxCell id="{cid}" value="{xml_attr(text)}" style="text;html=1;strokeColor=none;fillColor=none;fontStyle=1;fontSize=13;" vertex="1" parent="1">
  <mxGeometry x="{x}" y="{y}" width="20" height="18" as="geometry"/>
</mxCell>"""


def zone(zid, x, y, w, h, label, fill):
    return f"""<mxCell id="{zid}" value="{xml_attr(label)}" style="rounded=1;whiteSpace=wrap;html=1;fillColor={fill};strokeColor=#999999;opacity=20;verticalAlign=top;fontStyle=1;fontSize=12;align=left;spacingLeft=8;spacingTop=6;" vertex="1" parent="1">
  <mxGeometry x="{x}" y="{y}" width="{w}" height="{h}" as="geometry"/>
</mxCell>"""


def build():
    c = []
    c.append(zone("z1", 30, 70, 280, 520, "Karyawan", "#FFE6CC"))
    c.append(zone("z2", 330, 70, 400, 520, "Produksi &amp; Bahan", "#D5E8D4"))
    c.append(zone("z3", 750, 70, 280, 520, "Admin", "#E1D5E7"))

    c.append(entity_block("karyawan", 70, 120, 200, 100, "Karyawan", [
        "id  (PK)", "nama", "username", "role = karyawan",
    ], "#FFE6CC", "#D79B00"))
    c.append(entity_block("jual", 70, 380, 200, 110, "Transaksi Penjualan", [
        "id  (PK)", "tanggal", "total", "metode",
    ], "#F8CECC", "#B85450"))

    c.append(entity_block("produksi", 380, 120, 220, 120, "Data Produksi", [
        "id  (PK)", "tanggal", "nama_produk", "jumlah", "status",
    ], "#D5E8D4", "#82B366"))
    c.append(entity_block("produk", 620, 120, 180, 90, "Produk", [
        "id  (PK)", "nama", "stok", "harga",
    ], "#D5E8D4", "#82B366"))
    c.append(entity_block("detail", 380, 320, 200, 90, "Detail Pemakaian Bahan", [
        "id  (PK)", "jumlah", "total",
    ], "#FFF2CC", "#D6B656"))
    c.append(entity_block("bahan", 620, 320, 180, 100, "Bahan Baku", [
        "id  (PK)", "nama", "stok", "satuan",
    ], "#FFF2CC", "#D6B656"))

    c.append(entity_block("admin", 790, 120, 200, 100, "Admin", [
        "id  (PK)", "nama", "username", "role = admin",
    ], "#E1D5E7", "#9673A6"))
    c.append(entity_block("ops", 790, 280, 200, 110, "Biaya Operasional", [
        "id  (PK)", "tanggal", "kategori", "nominal",
    ], "#E1D5E7", "#9673A6"))
    c.append(entity_block("akun", 790, 440, 200, 90, "Akun (COA)", [
        "kode  (PK)", "nama", "grup",
    ], "#E1D5E7", "#9673A6"))

    c.append(entity_block("catatan", 380, 480, 300, 70, "Catatan", [
        "Karyawan &amp; Admin = satu tabel users",
        "Laporan = virtual (tidak ada tabel)",
    ], "#F5F5F5", "#666666"))

    # Diamonds
    c.append(rhombus("r1", 290, 155, "Menginput"))
    c.append(rhombus("r2", 290, 420, "Menginput"))
    c.append(rhombus("r3", 540, 145, "Menghasilkan"))
    c.append(rhombus("r4", 480, 260, "Membutuhkan"))
    c.append(rhombus("r5", 540, 340, "Menggunakan"))
    c.append(rhombus("r6", 720, 155, "Mengelola"))
    c.append(rhombus("r7", 720, 310, "Mencatat"))
    c.append(rhombus("r8", 720, 460, "Mengelola"))

    edges = [
        ("e1", "karyawan", "r1"), ("e2", "r1", "produksi"),
        ("e3", "karyawan", "r2"), ("e4", "r2", "jual"),
        ("e5", "produksi", "r3"), ("e6", "r3", "produk"),
        ("e7", "produksi", "r4"), ("e8", "r4", "detail"),
        ("e9", "detail", "r5"), ("e10", "r5", "bahan"),
        ("e11", "admin", "r6"), ("e12", "r6", "produk"),
        ("e13", "admin", "r7"), ("e14", "r7", "ops"),
        ("e15", "admin", "r8"), ("e16", "r8", "akun"),
    ]
    for eid, a, b in edges:
        c.append(connect(eid, a, b))

    cards = [
        ("c1", 255, 140, "1"), ("c2", 325, 140, "N"),
        ("c3", 255, 405, "1"), ("c4", 325, 405, "N"),
        ("c5", 505, 125, "1"), ("c6", 575, 125, "1"),
        ("c7", 445, 245, "1"), ("c8", 515, 245, "N"),
        ("c9", 505, 330, "N"), ("c10", 575, 330, "1"),
        ("c11", 755, 140, "1"), ("c12", 685, 140, "N"),
        ("c13", 755, 295, "1"), ("c14", 685, 295, "N"),
        ("c15", 755, 445, "1"), ("c16", 685, 445, "N"),
    ]
    for cid, x, y, t in cards:
        c.append(card(cid, x, y, t))

    c.append(f"""<mxCell id="title" value="{xml_attr('ERD Konseptual — versi ringkas (edit bebas di draw.io)')}" style="text;html=1;strokeColor=none;fillColor=none;fontStyle=1;fontSize=15;" vertex="1" parent="1">
  <mxGeometry x="30" y="20" width="600" height="30" as="geometry"/>
</mxCell>""")

    return c


def main():
    out = Path(__file__).parent / "ERD-konseptual-ringkas.drawio"
    body = "\n        ".join(["<mxCell id=\"0\"/>", "<mxCell id=\"1\" parent=\"0\"/>", *build()])
    xml = f"""<?xml version="1.0" encoding="UTF-8"?>
<mxfile host="app.diagrams.net" agent="TandisBakery" version="22.1.0">
  <diagram id="ringkas" name="ERD Ringkas">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" pageWidth="1100" pageHeight="650">
      <root>
        {body}
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
"""
    out.write_text(xml, encoding="utf-8")
    print(f"Written {out}")


if __name__ == "__main__":
    main()
