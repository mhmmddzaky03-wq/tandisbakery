#!/usr/bin/env python3
"""Generate docs/ERD.drawio from the current database schema."""

import html
from pathlib import Path


def xml_attr(s: str) -> str:
    """Escape string for use inside an XML attribute (draw.io value=\"...\")."""
    return html.escape(s, quote=True)


def entity(eid, x, y, w, h, title, cols, fill="#dae8fc", stroke="#6c8ebf"):
    separator = "─" * max(len(title), 12)
    lines = [title, separator, *cols]
    val = "&#xa;".join(xml_attr(line) for line in lines)
    style = (
        "rounded=0;whiteSpace=wrap;html=1;align=left;verticalAlign=top;"
        f"fillColor={fill};strokeColor={stroke};fontStyle=1;spacingLeft=6;spacingTop=4;"
    )
    return f"""<mxCell id="{eid}" value="{val}" style="{style}" vertex="1" parent="1">
  <mxGeometry x="{x}" y="{y}" width="{w}" height="{h}" as="geometry"/>
</mxCell>"""


def edge(eid, src, tgt, label=""):
    style = (
        "edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;"
        "html=1;endArrow=ERmany;startArrow=ERone;endFill=0;startFill=0;"
    )
    lbl = f' value="{xml_attr(label)}"' if label else ""
    return f"""<mxCell id="{eid}" style="{style}" edge="1" parent="1" source="{src}" target="{tgt}"{lbl}>
  <mxGeometry relative="1" as="geometry"/>
</mxCell>"""


def group_label(gid, x, y, w, text):
    return f"""<mxCell id="{gid}" value="{xml_attr(text)}" style="text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontStyle=1;fontSize=14;" vertex="1" parent="1">
  <mxGeometry x="{x}" y="{y}" width="{w}" height="30" as="geometry"/>
</mxCell>"""


def bg(bid, x, y, w, h, fill):
    return f"""<mxCell id="{bid}" value="" style="rounded=1;whiteSpace=wrap;html=1;fillColor={fill};strokeColor=#999999;opacity=30;dashed=1;" vertex="1" parent="1">
  <mxGeometry x="{x}" y="{y}" width="{w}" height="{h}" as="geometry"/>
</mxCell>"""


def build_page1():
    cells = [
        bg("bg_auth", 20, 50, 280, 480, "#dae8fc"),
        bg("bg_prod", 320, 50, 300, 850, "#d5e8d4"),
        bg("bg_inv", 640, 50, 320, 1080, "#fff2cc"),
        bg("bg_sales", 980, 50, 280, 320, "#f8cecc"),
        group_label("lbl_auth", 30, 55, 220, "Autentikasi & Audit"),
        group_label("lbl_prod", 330, 55, 220, "Produksi"),
        group_label("lbl_inv", 650, 55, 240, "Persediaan & Bahan Dasar"),
        group_label("lbl_sales", 990, 55, 220, "Penjualan"),
        entity("users", 40, 90, 220, 130, "users", [
            "id : bigint PK",
            "name : string",
            "username : string UK",
            "email : string UK (nullable)",
            "password : string",
            "role : admin | karyawan",
        ]),
        entity("activity_logs", 40, 250, 220, 150, "activity_logs", [
            "id : bigint PK",
            "user_id : bigint FK → users",
            "user_name : string",
            "user_role : string",
            "action, object, menu : string",
        ]),
        entity("sessions", 40, 430, 220, 90, "sessions (Laravel)", [
            "id : string PK",
            "user_id : bigint FK → users",
            "payload, last_activity",
        ]),
        entity("laporan_virtual", 40, 550, 250, 110, "Laporan (virtual)", [
            "Tidak ada tabel di database",
            "Laba rugi, neraca, laporan penjualan",
            "GL, TB, PDF — runtime / export",
        ], fill="#f5f5f5", stroke="#666666"),
        entity("production_records", 340, 90, 260, 200, "production_records", [
            "id : string PK (PRD001)",
            "tanggal : date",
            "product_name : string",
            "jumlah : int, satuan : string",
            "status : Berhasil | Gagal",
            "notes : string (nullable)",
            "total_material_cost : bigint",
            "journal_transaction_id FK (nullable)",
        ], fill="#d5e8d4", stroke="#82b366"),
        entity("products", 340, 320, 260, 140, "products", [
            "id : string PK (P001)",
            "production_record_id : string FK UK",
            "nama, satuan : string",
            "jumlah : int (stok produk)",
            "harga : int",
        ], fill="#d5e8d4", stroke="#82b366"),
        entity("production_material_usages", 340, 490, 260, 170, "production_material_usages", [
            "id : bigint PK",
            "production_record_id : string FK",
            "raw_material_id : string FK",
            "raw_material_restock_id FK (nullable)",
            "jumlah, satuan, harga_satuan, total",
        ], fill="#d5e8d4", stroke="#82b366"),
        entity("pemakaian_bahan_dasar_produksi", 340, 690, 260, 160, "pemakaian_bahan_dasar_produksi", [
            "id : bigint PK",
            "production_record_id : string FK",
            "batch_bahan_dasar_id : bigint FK",
            "bahan_dasar_id : string FK",
            "jumlah, satuan, harga_satuan, total",
        ], fill="#d5e8d4", stroke="#82b366"),
        entity("raw_materials", 660, 90, 280, 150, "raw_materials", [
            "id : string PK (SBB001)",
            "nama : string",
            "jumlah : decimal (stok agregat)",
            "satuan, min : decimal/string",
            "kategori : string",
            "harga : int",
        ], fill="#fff2cc", stroke="#d6b656"),
        entity("raw_material_restocks", 660, 270, 280, 170, "raw_material_restocks", [
            "id : bigint PK",
            "raw_material_id : string FK",
            "tanggal : date",
            "kode_produksi, expired (nullable)",
            "jumlah, sisa : decimal",
            "harga, total : int/bigint",
            "journal_transaction_id FK (nullable)",
        ], fill="#fff2cc", stroke="#d6b656"),
        entity("bahan_dasar", 660, 470, 280, 120, "bahan_dasar", [
            "id : string PK",
            "nama, satuan : string",
            "jumlah, min : decimal",
            "harga : bigint",
        ], fill="#fff2cc", stroke="#d6b656"),
        entity("batch_bahan_dasar", 660, 620, 280, 150, "batch_bahan_dasar", [
            "id : bigint PK",
            "bahan_dasar_id : string FK",
            "tanggal : date",
            "jumlah, sisa : decimal",
            "total_biaya : bigint",
            "journal_transaction_id FK (nullable)",
        ], fill="#fff2cc", stroke="#d6b656"),
        entity("pemakaian_bahan_baku_adonan", 660, 800, 280, 160, "pemakaian_bahan_baku_adonan", [
            "id : bigint PK",
            "batch_bahan_dasar_id : bigint FK",
            "raw_material_id : string FK",
            "raw_material_restock_id FK (nullable)",
            "jumlah, satuan, harga_satuan, total",
        ], fill="#fff2cc", stroke="#d6b656"),
        entity("units", 660, 990, 200, 80, "units", [
            "id : bigint PK",
            "nama : string UK",
        ], fill="#fff2cc", stroke="#d6b656"),
        entity("sales_transactions", 1000, 90, 240, 150, "sales_transactions", [
            "id : string PK (TRS001)",
            "tanggal : date",
            "total : int (rupiah)",
            "metode : Cash | Transfer | Mix",
            "jumlah : int (hitungan transaksi)",
            "journal_transaction_id FK (nullable)",
        ], fill="#f8cecc", stroke="#b85450"),
        entity("journal_ref_p1", 1000, 270, 240, 60, "journal_transactions", [
            "(detail di halaman 2 - Akuntansi)",
        ], fill="#e1d5e7", stroke="#9673a6"),
    ]
    edges = [
        ("e1", "activity_logs", "users", "user_id"),
        ("e2", "sessions", "users", "user_id"),
        ("e3", "products", "production_records", "production_record_id"),
        ("e4", "production_material_usages", "production_records", ""),
        ("e5", "production_material_usages", "raw_materials", ""),
        ("e6", "production_material_usages", "raw_material_restocks", "batch FEFO"),
        ("e7", "pemakaian_bahan_dasar_produksi", "production_records", ""),
        ("e8", "pemakaian_bahan_dasar_produksi", "batch_bahan_dasar", ""),
        ("e9", "pemakaian_bahan_dasar_produksi", "bahan_dasar", ""),
        ("e10", "raw_material_restocks", "raw_materials", ""),
        ("e11", "batch_bahan_dasar", "bahan_dasar", ""),
        ("e12", "pemakaian_bahan_baku_adonan", "batch_bahan_dasar", ""),
        ("e13", "pemakaian_bahan_baku_adonan", "raw_materials", ""),
        ("e14", "pemakaian_bahan_baku_adonan", "raw_material_restocks", "nullable"),
        ("e15", "sales_transactions", "journal_ref_p1", "nullable FK"),
        ("e16", "production_records", "journal_ref_p1", "nullable FK"),
        ("e17", "raw_material_restocks", "journal_ref_p1", "nullable FK"),
        ("e18", "batch_bahan_dasar", "journal_ref_p1", "nullable FK"),
    ]
    for eid, src, tgt, lbl in edges:
        cells.append(edge(eid, src, tgt, lbl))
    return cells


def build_page2():
    cells = [
        bg("bg2_acc", 20, 50, 540, 500, "#e1d5e7"),
        bg("bg2_ops", 580, 50, 300, 280, "#fff2cc"),
        bg("bg2_jref", 900, 50, 300, 420, "#f5f5f5"),
        group_label("lbl2_acc", 30, 55, 320, "Akuntansi (double-entry)"),
        group_label("lbl2_ops", 590, 55, 280, "Biaya Operasional"),
        group_label("lbl2_jref", 910, 55, 280, "Referensi jurnal dari modul lain"),
        entity("accounts", 40, 90, 240, 140, "accounts", [
            "kode : string PK",
            "nama : string",
            "posisi : Debit | Credit",
            "grup, sub_grup : string",
        ], fill="#e1d5e7", stroke="#9673a6"),
        entity("journal_transactions", 40, 260, 240, 100, "journal_transactions", [
            "id : bigint PK",
            "tanggal : date",
            "deskripsi : string",
            "ref : string (nullable)",
        ], fill="#e1d5e7", stroke="#9673a6"),
        entity("journal_entries", 40, 390, 240, 120, "journal_entries", [
            "id : bigint PK",
            "journal_transaction_id : bigint FK",
            "account_kode : string FK → accounts",
            "debit : int, credit : int",
        ], fill="#e1d5e7", stroke="#9673a6"),
        entity("expense_categories", 300, 90, 240, 130, "expense_categories", [
            "id : bigint PK",
            "nama : string",
            "jenis : Fixed | Variable",
            "account_kode : string FK → accounts",
            "sort_order, is_active",
        ], fill="#e1d5e7", stroke="#9673a6"),
        entity("operational_costs", 590, 90, 270, 170, "operational_costs", [
            "id : string PK (BO001)",
            "expense_category_id : bigint FK (nullable)",
            "tanggal : date",
            "kat, desk : string",
            "jumlah : int",
            "jenis : Fixed | Variable",
            "journal_transaction_id FK (nullable)",
        ], fill="#fff2cc", stroke="#d6b656"),
        entity("role_note", 40, 540, 500, 90, "Pembagian akses (aplikasi)", [
            "karyawan: data produksi + transaksi penjualan (lihat & input)",
            "admin: semua modul, laporan, akuntansi",
            "users.role — bukan tabel Admin/Karyawan terpisah",
        ], fill="#f5f5f5", stroke="#666666"),
        entity("sales_transactions_p2", 920, 90, 260, 90, "sales_transactions", [
            "journal_transaction_id → journal_transactions",
        ], fill="#f8cecc", stroke="#b85450"),
        entity("production_records_p2", 920, 200, 260, 90, "production_records", [
            "journal_transaction_id → journal_transactions",
        ], fill="#d5e8d4", stroke="#82b366"),
        entity("raw_material_restocks_p2", 920, 310, 260, 90, "raw_material_restocks", [
            "journal_transaction_id → journal_transactions",
        ], fill="#fff2cc", stroke="#d6b656"),
        entity("batch_bahan_dasar_p2", 920, 420, 260, 90, "batch_bahan_dasar", [
            "journal_transaction_id → journal_transactions",
        ], fill="#fff2cc", stroke="#d6b656"),
    ]
    edges = [
        ("e2_1", "journal_entries", "journal_transactions", ""),
        ("e2_2", "journal_entries", "accounts", "account_kode"),
        ("e2_3", "expense_categories", "accounts", "account_kode"),
        ("e2_4", "operational_costs", "expense_categories", ""),
        ("e2_5", "operational_costs", "journal_transactions", "jurnal auto"),
        ("e2_6", "sales_transactions_p2", "journal_transactions", ""),
        ("e2_7", "production_records_p2", "journal_transactions", ""),
        ("e2_8", "raw_material_restocks_p2", "journal_transactions", ""),
        ("e2_9", "batch_bahan_dasar_p2", "journal_transactions", ""),
    ]
    for eid, src, tgt, lbl in edges:
        cells.append(edge(eid, src, tgt, lbl))
    return cells


def page(name, diagram_id, cell_list, width, height):
    root = "\n        ".join(["<mxCell id=\"0\"/>", "<mxCell id=\"1\" parent=\"0\"/>", *cell_list])
    return f"""  <diagram id="{diagram_id}" name="{xml_attr(name)}">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="{width}" pageHeight="{height}" math="0" shadow="0">
      <root>
        {root}
      </root>
    </mxGraphModel>
  </diagram>"""


def main():
    out = Path(__file__).parent / "ERD.drawio"
    xml = f"""<?xml version="1.0" encoding="UTF-8"?>
<mxfile host="app.diagrams.net" agent="TandisBakery-ERD" version="22.1.0" type="device">
{page("1. Bisnis - Produksi & Penjualan", "erd-bisnis", build_page1(), 1280, 1150)}
{page("2. Akuntansi & Operasional", "erd-akuntansi", build_page2(), 1240, 680)}
</mxfile>
"""
    out.write_text(xml, encoding="utf-8")
    print(f"Written {out} ({len(xml)} bytes)")


if __name__ == "__main__":
    main()
