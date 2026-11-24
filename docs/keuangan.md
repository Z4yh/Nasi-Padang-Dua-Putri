# Dokumentasi Fitur Keuangan — RM Padang Tiga Putri

Modul **Keuangan (Laporan Arus Kas & Pencatatan Pengeluaran)** menyediakan rekapitulasi keuangan otomatis terintegrasi dari Kasir POS dan Pesanan Katering, serta pencatatan pengeluaran operasional warung secara cepat (mobile-first) dan ekspor berkas pembukuan.

---

## 1. Struktur Arsitektur & File Utama

### Database & Model
- **Tabel `expenses`**: Menyimpan pengeluaran kas warung dengan audit trail (`created_by`, `updated_at`, `deleted_at` soft deletes).
- **Model `App\Models\Expense`**: Dilengkapi validasi model level, konstanta kategori (`CATEGORIES`), dan sumber dana (`FUND_SOURCES`).

### Logika Bisnis & Service
- **`App\Services\Keuangan\CashFlowCalculator`**: Kelas murni perhitungan rumus laba, margin %, persentase masuk/keluar, dan pemetaan kategori.
- **`App\Services\Keuangan\CashFlowPeriodHelper`**: Kelas pembantu rentang waktu (Hari Ini, Minggu Ini, Bulan Ini, Tahun Ini) berbasis zona waktu `Asia/Jakarta` (WIB).
- **`App\Services\Keuangan\CashFlowFormatter`**: Format mata uang Rupiah (`Rp X.XXX.XXX`), format ringkas (`k`), dan persentase.
- **`App\Services\Keuangan\CashFlowService`**: Service agregator yang menggabungkan transaksi Kasir POS, Pesanan Katering, dan Pengeluaran Warung.

### Controller & Rute
- **Web**: `GET /keuangan` (`App\Http\Controllers\KeuanganController@index`)
- **API**:
  - `GET /api/finance/summary` (`FinanceController@summary`)
  - `GET /api/finance/entries` (`FinanceController@entries`)
  - `POST /api/finance/expenses` (`FinanceController@storeExpense`)
  - `PUT /api/finance/expenses/{id}` (`FinanceController@updateExpense`)
  - `DELETE /api/finance/expenses/{id}` (`FinanceController@destroyExpense`)
  - `GET /api/finance/export` (`FinanceController@export`)

### Antarmuka Pengguna (Blade & Alpine.js)
- `resources/views/keuangan/index.blade.php`: Halaman utama laporan keuangan dengan container `max-w-7xl` (responsif seragam dengan Katering).
- `resources/views/keuangan/partials/`:
  - `period-filter.blade.php`: Tab pemilih periode (Hari Ini, Minggu Ini, Bulan Ini, Tahun Ini).
  - `summary-card.blade.php`: Kartu Laba Bersih Usaha, margin laba, dan progress bar komparasi Masuk vs Keluar.
  - `in-out-cards.blade.php`: Kartu Total Masuk dan Total Keluar dengan breakdown.
  - `source-cards.blade.php`: Kartu Rincian Kasir POS (Tunai/QRIS) dan Pesanan Katering.
  - `entry-list.blade.php`: Riwayat transaksi arus kas (dengan tombol ubah pengeluaran).
  - `export-card.blade.php`: Tombol ekspor PDF dan Excel CSV.
  - `expense-sheet.blade.php`: Bottom sheet "Catat Pengeluaran" dan "Ubah Pengeluaran" lengkap dengan validasi inline, modal konfirmasi buang perubahan, dan konfirmasi hapus.
- `resources/views/keuangan/pdf.blade.php`: Template dokumen cetak A4 berstandar cetak printer / Save as PDF.

---

## 2. Rumus Pembukuan & Aturan Arus Kas

1. **Kas Masuk (+)**:
   - Kasir POS: Total transaksi selesai pada periode terkait (Tunai + QRIS).
   - Katering: DP pesanan (dicatat saat tanggal pembayaran DP) + Pelunasan sisa tagihan (dicatat saat pesanan berstatus selesai).
2. **Kas Keluar (-)**:
   - Belanja Pasar Subuh & Daging (`bahan_pasar`).
   - Operasional Harian: Gas LPG & Galon (`gas_galon`), Upah/Uang Makan (`upah_makan`), Listrik & Air (`listrik_air`), Lain-lain (`lain_lain`).
3. **Laba Bersih**:
   $$\text{Laba Bersih} = \text{Total Kas Masuk} - \text{Total Kas Keluar}$$
4. **Margin Laba**:
   $$\text{Margin Laba (\%)} = \begin{cases} \left(\frac{\text{Laba Bersih}}{\text{Total Kas Masuk}}\right) \times 100\%, & \text{jika Total Masuk} > 0 \\ 0\%, & \text{jika Total Masuk} = 0 \end{cases}$$

---

## 3. Fitur Keamanan & Validasi

- **Akses Kontrol (RBAC)**: Hanya pengguna dengan peran `super_admin` / `admin` yang diizinkan mengakses halaman `/keuangan` dan API `/api/finance/*`. Pengguna lain mendapatkan status `403 Tidak punya akses`.
- **CSV Formula Injection**: Sel yang berawalan karakter berbahaya (`=`, `+`, `-`, `@`) secara otomatis diawali tanda petik tunggal (`'`).
- **Excel BOM**: Menggunakan prefix `\xEF\xBB\xBF` agar teks beraksen dan format UTF-8 terbuka rapi di Microsoft Excel.
- **Pencegahan Tanggal Masa Depan**: Pengeluaran divalidasi tidak boleh tanggal masa depan (`before_or_equal:now`).

---

## 4. Cara Menjalankan Automated Test

```bash
cd c:\Users\Hype\uas\Nasi-Padang-Dua-Putri

# Menjalankan seluruh test unit & feature Keuangan
php -d extension=pdo_sqlite vendor/phpunit/phpunit/phpunit tests/Unit/Keuangan tests/Feature/Keuangan
```
