#!/usr/bin/env python3
"""Generate docs/ERD-konseptual.drawio — Chen-style: oval entities & attributes, diamond relationships."""

import html
from pathlib import Path


def xml_attr(s: str) -> str:
    return html.escape(s, quote=True)


def oval(oid, x, y, w, h, text, fill="#FFFFFF", stroke="#000000", bold=False, pk=False):
    text = text.replace("\n", "&#xa;")
    if pk:
        val = f"&lt;u&gt;{xml_attr(text)}&lt;/u&gt;"
    elif bold:
        val = f"&lt;b&gt;{xml_attr(text)}&lt;/b&gt;"
    else:
        val = xml_attr(text)
    fw = "fontStyle=1;" if bold else ""
    style = (
        f"ellipse;whiteSpace=wrap;html=1;align=center;verticalAlign=middle;"
        f"fillColor={fill};strokeColor={stroke};{fw}"
    )
    return f"""<mxCell id="{oid}" value="{val}" style="{style}" vertex="1" parent="1">
  <mxGeometry x="{x}" y="{y}" width="{w}" height="{h}" as="geometry"/>
</mxCell>"""


def rhombus(rid, x, y, w, h, label, fill="#FFF2CC", stroke="#D6B656"):
    style = (
        f"rhombus;whiteSpace=wrap;html=1;align=center;verticalAlign=middle;"
        f"fillColor={fill};strokeColor={stroke};"
    )
    return f"""<mxCell id="{rid}" value="{xml_attr(label)}" style="{style}" vertex="1" parent="1">
  <mxGeometry x="{x}" y="{y}" width="{w}" height="{h}" as="geometry"/>
</mxCell>"""


def line(eid, src, tgt, dashed=False):
    dash = "dashed=1;" if dashed else ""
    style = f"endArrow=none;html=1;rounded=0;{dash}strokeColor=#000000;"
    return f"""<mxCell id="{eid}" style="{style}" edge="1" parent="1" source="{src}" target="{tgt}">
  <mxGeometry relative="1" as="geometry"/>
</mxCell>"""


def rel_line(eid, src, tgt):
    style = "endArrow=none;html=1;rounded=0;strokeColor=#000000;strokeWidth=1;"
    return f"""<mxCell id="{eid}" style="{style}" edge="1" parent="1" source="{src}" target="{tgt}">
  <mxGeometry relative="1" as="geometry"/>
</mxCell>"""


def cardinality(cid, x, y, text):
    return f"""<mxCell id="{cid}" value="{xml_attr(text)}" style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontStyle=1;fontSize=12;" vertex="1" parent="1">
  <mxGeometry x="{x}" y="{y}" width="24" height="20" as="geometry"/>
</mxCell>"""


def entity_group(gid, cx, cy, title, attrs, pk_set, ent_fill="#DAE8FC", ent_stroke="#6C8EBF", attr_fill="#FFFFFF"):
    """Entity name oval + attribute ovals + connecting lines."""
    cells = []
    ew, eh = 120, 44
    cells.append(oval(f"{gid}_e", cx - ew // 2, cy, ew, eh, title, ent_fill, ent_stroke, bold=True))

    aw, ah = 100, 32
    start_y = cy + eh + 24
    cols = 2
    for i, attr in enumerate(attrs):
        col = i % cols
        row = i // cols
        ax = cx - aw - 12 + col * (aw + 24)
        ay = start_y + row * (ah + 10)
        aid = f"{gid}_a{i}"
        cells.append(oval(aid, ax, ay, aw, ah, attr, attr_fill, "#666666", pk=(attr in pk_set)))
        cells.append(line(f"{gid}_l{i}", f"{gid}_e", aid))
    return cells


def build_diagram():
    cells = []

    # Legend
    cells.append(f"""<mxCell id="legend" value="{xml_attr('ERD Konseptual — Tandi\'s Bakery (sesuai implementasi)')}" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontStyle=1;fontSize=16;" vertex="1" parent="1">
  <mxGeometry x="40" y="20" width="500" height="30" as="geometry"/>
</mxCell>""")
    cells.append(f"""<mxCell id="legend2" value="{xml_attr('Oval = entitas/atribut | Belah ketupat = relasi | PK = garis bawah | Tabel di () = nama di database')}" style="text;html=1;strokeColor=none;fillColor=none;align=left;fontSize=11;fontColor=#666666;" vertex="1" parent="1">
  <mxGeometry x="40" y="48" width="700" height="24" as="geometry"/>
</mxCell>""")

    # --- Karyawan cluster (users role=karyawan) ---
    cells += entity_group(
        "karyawan", 140, 100, "Karyawan\n(users)",
        ["id", "nama", "username", "password", "role"],
        {"id"},
        ent_fill="#FFE6CC",
        ent_stroke="#D79B00",
    )

    # --- Admin cluster ---
    cells += entity_group(
        "admin", 1040, 100, "Admin\n(users)",
        ["id", "nama", "username", "password", "role"],
        {"id"},
        ent_fill="#E1D5E7",
        ent_stroke="#9673A6",
    )

    # --- Produksi ---
    cells += entity_group(
        "produksi", 420, 100, "Produksi\n(production_records)",
        ["id", "tanggal", "nama_produk", "jumlah", "status", "total_biaya"],
        {"id"},
        ent_fill="#D5E8D4",
        ent_stroke="#82B366",
    )

    # --- Produk ---
    cells += entity_group(
        "produk", 700, 100, "Produk\n(products)",
        ["id", "nama", "stok", "harga"],
        {"id"},
        ent_fill="#D5E8D4",
        ent_stroke="#82B366",
    )

    # --- Detail pemakaian bahan (associative) ---
    cells += entity_group(
        "detail", 420, 380, "Detail Pemakaian\n(production_material_usages)",
        ["id", "jumlah", "satuan", "total"],
        {"id"},
        ent_fill="#FFF2CC",
        ent_stroke="#D6B656",
    )

    # --- Bahan baku ---
    cells += entity_group(
        "bahan", 140, 380, "Bahan Baku\n(raw_materials)",
        ["id", "nama", "stok", "satuan", "harga"],
        {"id"},
        ent_fill="#FFF2CC",
        ent_stroke="#D6B656",
    )

    # --- Transaksi penjualan ---
    cells += entity_group(
        "jual", 140, 640, "Transaksi Penjualan\n(sales_transactions)",
        ["id", "tanggal", "total", "metode", "jumlah_trx"],
        {"id"},
        ent_fill="#F8CECC",
        ent_stroke="#B85450",
    )

    # --- Biaya operasional ---
    cells += entity_group(
        "ops", 1040, 380, "Biaya Operasional\n(operational_costs)",
        ["id", "tanggal", "kategori", "deskripsi", "nominal"],
        {"id"},
        ent_fill="#E1D5E7",
        ent_stroke="#9673A6",
    )

    # --- Jurnal (accounting hub) ---
    cells += entity_group(
        "jurnal", 700, 640, "Jurnal\n(journal_transactions)",
        ["id", "tanggal", "deskripsi", "ref"],
        {"id"},
        ent_fill="#E1D5E7",
        ent_stroke="#9673A6",
    )

    # --- Akun COA ---
    cells += entity_group(
        "akun", 1040, 640, "Akun\n(accounts)",
        ["kode", "nama", "posisi", "grup"],
        {"kode"},
        ent_fill="#E1D5E7",
        ent_stroke="#9673A6",
    )

    # --- Laporan virtual ---
    cells.append(oval("laporan_e", 700, 380, 130, 50, "Laporan\n(virtual)", "#F5F5F5", "#666666", bold=True))

    # Relationships (diamonds)
    rels = [
        ("r1", 270, 200, "Menginput"),
        ("r2", 270, 520, "Menginput"),
        ("r3", 560, 155, "Menghasilkan"),
        ("r4", 420, 280, "Membutuhkan"),
        ("r5", 280, 430, "Menggunakan"),
        ("r6", 900, 200, "Mengelola"),
        ("r7", 900, 430, "Mengelola"),
        ("r8", 900, 520, "Mencatat"),
        ("r9", 270, 700, "Mencatat"),
        ("r10", 560, 700, "Mencatat ke"),
        ("r11", 900, 700, "Mengelola"),
    ]
    for rid, rx, ry, lbl in rels:
        cells.append(rhombus(rid, rx, ry, 90, 50, lbl))

    # Rel edges + cardinality labels (approximate positions)
    pairs = [
        # (edge_id, src, diamond, tgt, card_near_src, card_near_tgt, pos1, pos2)
        ("re1a", "karyawan_e", "r1", None),
        ("re1b", "r1", "produksi_e", None),
        ("re2a", "karyawan_e", "r2", None),
        ("re2b", "r2", "jual_e", None),
        ("re3a", "produksi_e", "r3", None),
        ("re3b", "r3", "produk_e", None),
        ("re4a", "produksi_e", "r4", None),
        ("re4b", "r4", "detail_e", None),
        ("re5a", "bahan_e", "r5", None),
        ("re5b", "r5", "detail_e", None),
        ("re6a", "admin_e", "r6", None),
        ("re6b", "r6", "produk_e", None),
        ("re7a", "admin_e", "r7", None),
        ("re7b", "r7", "bahan_e", None),
        ("re8a", "admin_e", "r8", None),
        ("re8b", "r8", "ops_e", None),
        ("re9a", "karyawan_e", "r9", None),
        ("re9b", "r9", "jual_e", None),
        ("re10a", "jual_e", "r10", None),
        ("re10b", "r10", "jurnal_e", None),
        ("re11a", "admin_e", "r11", None),
        ("re11b", "r11", "akun_e", None),
    ]
    for eid, src, mid, _ in pairs:
        if _ is None:
            cells.append(rel_line(eid, src, mid))

    # Admin -> Laporan (mengelola)
    cells.append(rhombus("r12", 900, 380, 90, 50, "Mengelola"))
    cells.append(rel_line("re12a", "admin_e", "r12"))
    cells.append(rel_line("re12b", "r12", "laporan_e"))

    # Cardinality
    cards = [
        ("c1", 228, 175, "1"), ("c2", 318, 175, "N"),
        ("c3", 228, 495, "1"), ("c4", 318, 555, "N"),
        ("c5", 518, 130, "1"), ("c6", 608, 130, "1"),
        ("c7", 388, 250, "1"), ("c8", 448, 330, "N"),
        ("c9", 248, 405, "1"), ("c10", 358, 405, "N"),
        ("c11", 958, 175, "1"), ("c12", 848, 175, "N"),
        ("c13", 958, 405, "1"), ("c14", 848, 405, "N"),
        ("c15", 958, 505, "1"), ("c16", 848, 555, "N"),
        ("c17", 228, 655, "1"), ("c18", 318, 655, "N"),
        ("c19", 518, 655, "N"), ("c20", 608, 655, "1"),
        ("c21", 958, 655, "1"), ("c22", 848, 655, "N"),
        ("c23", 958, 380, "1"), ("c24", 848, 380, "N"),
    ]
    for cid, x, y, t in cards:
        cells.append(cardinality(cid, x, y, t))

    return cells


def main():
    out = Path(__file__).parent / "ERD-konseptual.drawio"
    body = "\n        ".join(["<mxCell id=\"0\"/>", "<mxCell id=\"1\" parent=\"0\"/>", *build_diagram()])
    xml = f"""<?xml version="1.0" encoding="UTF-8"?>
<mxfile host="app.diagrams.net" agent="TandisBakery-ERD-Konseptual" version="22.1.0" type="device">
  <diagram id="erd-konseptual" name="ERD Konseptual (Chen)">
    <mxGraphModel dx="1400" dy="900" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1280" pageHeight="900" math="0" shadow="0">
      <root>
        {body}
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
"""
    out.write_text(xml, encoding="utf-8")
    print(f"Written {out} ({len(xml)} bytes)")


if __name__ == "__main__":
    main()
