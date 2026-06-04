# Panduan bikin ERD sendiri di draw.io (Tandi's Bakery)

Diagram auto-generated (`ERD-konseptual.drawio`) sengaja **penuh** supaya lengkap — untuk skripsi/laporan biasanya **lebih rapi kalau Anda gambar sendiri** dengan cakupan lebih sedikit.

Ikuti urutan ini; jangan taruh semua entitas + semua atribut sekaligus.

---

## 1. Tentukan versi mana

| Versi | Isi | Cocok untuk |
|--------|-----|-------------|
| **Ringkas (disarankan)** | 8 entitas, atribut utama saja | Laporan, presentasi, skripsi bab desain |
| **Lengkap** | + bahan dasar, batch, jurnal detail, dll. | Lampiran teknis |

Mulai dari **ringkas**. Tambah entitas hanya kalau dosen/pembimbing minta.

---

## 2. Entitas ringkas (sesuai kode Anda)

Gambar **oval** untuk nama entitas. Di bawahnya (masih dalam oval yang sama, atau oval kecil terpisah) tulis atribut. **PK = garis bawah** (`Ctrl+U` di draw.io).

| No | Nama di ERD (Indonesia) | Tabel di database | PK (garis bawah) |
|----|------------------------|-------------------|------------------|
| 1 | Karyawan | `users` (role = karyawan) | id |
| 2 | Admin | `users` (role = admin) | id |
| 3 | Data Produksi | `production_records` | id |
| 4 | Produk | `products` | id |
| 5 | Bahan Baku | `raw_materials` | id |
| 6 | Transaksi Penjualan | `sales_transactions` | id |
| 7 | Biaya Operasional | `operational_costs` | id |
| 8 | Akun | `accounts` | kode |

**Catatan:** Karyawan dan Admin **bukan** dua tabel — satu tabel `users`, dibedakan `role`. Di ERD konseptual boleh dua oval (seperti contoh dosen) + catatan kecil: *“satu tabel users”*.

**Opsional** (halaman 2): Jurnal (`journal_transactions`), Detail Pemakaian Bahan (`production_material_usages`).

**Jangan** gambar tabel `Laporan` — di aplikasi laporan **virtual** (tidak disimpan).

---

## 3. Relasi + kardinalitas (yang benar menurut aplikasi)

Gunakan **belah ketupat** untuk nama relasi. Di ujung garis tulis **1** atau **N**.

### Yang dilakukan Karyawan

| Dari | Relasi (◇) | Ke | Kardinalitas |
|------|------------|-----|--------------|
| Karyawan | Menginput | Data Produksi | 1 : N |
| Karyawan | Menginput | Transaksi Penjualan | 1 : N |

Karyawan **tidak** langsung menginput Bahan Baku (itu akses Admin).

### Produksi & produk

| Dari | Relasi | Ke | Kardinalitas |
|------|--------|-----|--------------|
| Data Produksi | Menghasilkan | Produk | 1 : 1 |
| Data Produksi | Membutuhkan | Bahan Baku | N : M |

Untuk N : M, tambah entitas asosiasi **Detail Pemakaian Bahan** di tengah:

`Data Produksi` —1:N— `Detail Pemakaian` —N:1— `Bahan Baku`

### Yang dilakukan Admin

| Dari | Relasi | Ke | Kardinalitas |
|------|--------|-----|--------------|
| Admin | Mengelola | Produk | 1 : N |
| Admin | Mengelola | Bahan Baku | 1 : N |
| Admin | Mencatat | Biaya Operasional | 1 : N |
| Admin | Mengelola | Akun | 1 : N |
| Admin | Melihat | Laporan | — (garis putus-putus, tanpa tabel) |

### Akuntansi (opsional halaman 2)

| Dari | Relasi | Ke | Kardinalitas |
|------|--------|-----|--------------|
| Transaksi Penjualan | Mencatat ke | Jurnal | N : 1 |
| Biaya Operasional | Mencatat ke | Jurnal | N : 1 |
| Jurnal | Memuat | Akun | N : M (via baris jurnal) |

---

## 4. Tata letak biar tidak berantakan

Bagi kanvas jadi **3 kolom** (gunakan guide View → Guides):

```
┌─────────────┬──────────────────┬─────────────┐
│   KARYAWAN  │     PRODUKSI     │    ADMIN    │
│             │                  │             │
│  Karyawan   │  Data Produksi   │   Admin     │
│      ◇      │       ◇          │      ◇      │
│  Transaksi  │  Produk          │  Produk     │
│  Penjualan  │  Detail◇Bahan    │  Bahan Baku │
│             │  Bahan Baku      │  Biaya Ops  │
│             │                  │  Akun       │
└─────────────┴──────────────────┴─────────────┘
```

**Aturan:**

1. Taruh **semua oval entitas** dulu — belum ada garis.
2. Jarak antar entitas ± 3 kotak grid.
3. Baru tambah **belah ketupat** di tengah garis (bukan di atas entitas).
4. Atribut: cukup **3–5 per entitas**; sisanya tulis di laporan.
5. Satu halaman = satu modul; jurnal/laporan → halaman 2.

---

## 5. Langkah di draw.io (klik demi klik)

1. Buka https://app.diagrams.net → **Create New Diagram** → kosong.
2. Kiri: pencarian shape → ketik **ellipse** (oval).
3. **Entitas:** oval besar, ketik nama (mis. `Data Produksi`).
4. **PK:** buat oval kecil di bawah, ketik `id` → blok teks → **Ctrl+U** (underline).  
   Atau satu oval saja: baris pertama `id` dengan underline.
5. **Relasi:** pencarian **rhombus** → letakkan di antara dua entitas.
6. **Garis:** drag dari entitas ke belah ketupat, belah ketupat ke entitas.  
   Style garis: tanpa panah (Entity Relation style).
7. **Kardinalitas:** double-click garis → tambah label `1` atau `N` di dekat ujung.
8. Warna konsisten (mis. hijau = produksi, merah = penjualan) — maksimal 4 warna.

**Template draw.io:** menu **+** → **Templates** → cari **Entity Diagram** atau **Chen ERD** (jika ada).

---

## 6. File bantu di repo

| File | Gunanya |
|------|---------|
| `ERD-konseptual-ringkas.drawio` | Kerangka sudah rapi, tinggal geser/edit |
| `ERD.drawio` | Referensi nama tabel & kolom lengkap |
| `ERD.md` | Penjelasan teks + Mermaid |

Regenerasi ringkas:

```text
D:\Laragon\laragon\bin\python\python-3.13\python.exe docs\generate_erd_ringkas_drawio.py
```

---

## 7. Checklist sebelum dikumpulkan

- [ ] Setiap entitas punya PK dengan **garis bawah**
- [ ] Setiap relasi punya **belah ketupat** + nama (Menginput, Mengelola, …)
- [ ] Setiap relasi punya **1** dan/atau **N** di kedua ujung
- [ ] Tidak ada garis menumpuk tanpa label
- [ ] Ada catatan: Karyawan/Admin = `users.role`
- [ ] Laporan tidak digambar sebagai tabel

---

## 8. Kalau masih bingung — urutan 30 menit

1. **Menit 0–10:** Gambar 8 oval entitas (tabel di atas), susun 3 kolom.
2. **Menit 10–20:** Tambah 6 belah ketupat + garis Karyawan & Admin saja.
3. **Menit 20–25:** Tambah relasi Produksi → Produk → Detail → Bahan Baku.
4. **Menit 25–30:** Tulis 1/N, underline PK, export PNG (File → Export as → PNG).

Selesai dulu versi minimal; baru tambah Jurnal di halaman kedua jika perlu.
