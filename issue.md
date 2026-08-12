# Issue & Specification Document: Sistem Pendukung Keputusan (SPK) Toko Jitanichi Go

**Tujuan Dokumen:**
Dokumen ini berfungsi sebagai perencanaan dan cetak biru (*blueprint*) teknis lengkap untuk diimplementasikan oleh Junior Programmer atau AI Coding Model. Dokumen mencakup instruksi setup environment, skema database (migrasi), struktur Eloquent Model dan relasinya, spesifikasi Controller, rumusan algoritma AHP & SAW, serta tahapan implementasi langkah demi langkah.

---

## 1. Ikhtisar Sistem & Metode SPK

Sistem ini adalah **Sistem Pendukung Keputusan (SPK)** berbasis web yang dirancang untuk membantu pemilik **Toko Jitanichi Go** menentukan produk mana yang paling layak diprioritaskan untuk dipromosikan atau diberikan diskon.

Metode komputasi yang digunakan adalah penggabungan 2 algoritma:
1. **Analytical Hierarchy Process (AHP)**: Digunakan untuk menentukan **pembobotan kriteria** ($W_j$) secara objektif berdasarkan perbandingan berpasangan (*pairwise comparison*) skala Saaty (1–9). Sistem juga memverifikasi tingkat konsistensi pembobotan ($CR \le 0.10$).
2. **Simple Additive Weighting (SAW)**: Digunakan untuk **pemeringkatan alternatif (produk)** dengan melakukan normalisasi matriks keputusan (kriteria *benefit* atau *cost*) dan mengalikan nilai normalisasi dengan bobot AHP untuk memperoleh nilai preferensi ($V_i$).

---

## 2. Stack Teknologi & Setup Awal

### Stack Teknologi
- **Framework Backend**: Laravel 11.x (PHP 8.2+)
- **Frontend / Styling**: Tailwind CSS via Vite
- **Autentikasi & Starter Kit**: Laravel Breeze (Blade Views)
- **Database**: MySQL / MariaDB

### Instruksi Setup Command-Line (Untuk Implementer)

 Jalankan perintah berikut di terminal root proyek (`e:\laragon\www\jitanichigo`):

```bash
# 1. Install Laravel Breeze untuk Autentikasi
composer require laravel/breeze --dev
php artisan breeze:install blade --no-interaction

# 2. Generate Application Key
php artisan key:generate

# 3. Install NPM Dependencies & Build Assets (Tailwind CSS via Vite)
npm install
npm run build
```

Konfigurasikan file `.env` untuk koneksi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jitanichigo_spk
DB_USERNAME=root
DB_PASSWORD=
```

---

## 3. Spesifikasi Skema Database & Migrasi (11 Tabel)

Setiap tabel di bawah harus dibuat menggunakan Migration File Laravel (`php artisan make:migration create_<table_name>_table`).

### 1. Tabel `roles`
*Tabel master untuk peran pengguna sistem.*
- `role_id` (`INT`, PK, Auto Increment)
- `role_name` (`VARCHAR(20)`, Unique, Not Null) — Nilai: `'owner'` atau `'admin'`
- `created_at`, `updated_at` (`TIMESTAMP`)

### 2. Tabel `users`
*Tabel pengguna sistem terautentikasi.*
- `user_id` (`INT`, PK, Auto Increment)
- `username` (`VARCHAR(50)`, Unique, Not Null)
- `password` (`VARCHAR(255)`, Not Null, Hashed)
- `role_id` (`INT`, FK -> `roles.role_id`, Not Null)
- `full_name` (`VARCHAR(100)`, Not Null)
- `status` (`ENUM('aktif', 'nonaktif')`, Default: `'aktif'`)
- `created_at`, `updated_at` (`TIMESTAMP`)

### 3. Tabel `criteria`
*Tabel kriteria penilaian produk.*
- `criterion_id` (`INT`, PK, Auto Increment)
- `criterion_code` (`VARCHAR(5)`, Unique, Not Null) — Contoh: `C1`, `C2`, `C3`, `C4`, `C5`
- `criterion_name` (`VARCHAR(50)`, Not Null) — Contoh: "Jumlah Terjual", "Sisa Stok", "Margin Keuntungan", "Lama Mengendap"
- `type` (`ENUM('benefit', 'cost')`, Not Null)
- `status` (`ENUM('aktif', 'nonaktif')`, Default: `'aktif'`)
- `created_at`, `updated_at` (`TIMESTAMP`)

### 4. Tabel `products`
*Tabel data produk Toko Jitanichi Go.*
- `product_id` (`INT`, PK, Auto Increment)
- `product_code` (`VARCHAR(20)`, Unique, Not Null)
- `product_name` (`VARCHAR(100)`, Not Null)
- `category` (`VARCHAR(50)`, Not Null)
- `status` (`ENUM('aktif', 'nonaktif')`, Default: `'aktif'`)
- `created_at`, `updated_at` (`TIMESTAMP`)

### 5. Tabel `assessment_periods`
*Tabel periode evaluasi/penilaian.*
- `period_id` (`INT`, PK, Auto Increment)
- `period_name` (`VARCHAR(50)`, Not Null) — Contoh: "Periode Agustus 2026", "Promo Q3 2026"
- `start_date` (`DATE`, Not Null)
- `end_date` (`DATE`, Not Null)
- `status` (`ENUM('aktif', 'selesai')`, Default: `'aktif'`)
- `created_at`, `updated_at` (`TIMESTAMP`)

### 6. Tabel `pairwise_comparisons`
*Tabel input perbandingan berpasangan AHP skala Saaty (1-9).*
- `comparison_id` (`INT`, PK, Auto Increment)
- `period_id` (`INT`, FK -> `assessment_periods.period_id`, Not Null)
- `criterion_a_id` (`INT`, FK -> `criteria.criterion_id`, Not Null)
- `criterion_b_id` (`INT`, FK -> `criteria.criterion_id`, Not Null)
- `value` (`DECIMAL(5,4)`, Not Null) — Nilai Saaty (misal: 1, 3, 5, 0.3333)
- `created_by` (`INT`, FK -> `users.user_id`, Not Null) — Merujuk pada Owner
- `created_at`, `updated_at` (`TIMESTAMP`)

### 7. Tabel `ahp_results`
*Tabel penyimpanan bobot kriteria AHP dan indeks konsistensi.*
- `ahp_result_id` (`INT`, PK, Auto Increment)
- `period_id` (`INT`, FK -> `assessment_periods.period_id`, Not Null)
- `criterion_id` (`INT`, FK -> `criteria.criterion_id`, Not Null)
- `weight` (`DECIMAL(6,4)`, Not Null) — Bobot Prioritas ($W_j$)
- `lambda_max` (`DECIMAL(8,4)`, Not Null) — Nilai $\lambda_{max}$
- `ci` (`DECIMAL(8,4)`, Not Null) — Consistency Index
- `cr` (`DECIMAL(8,4)`, Not Null) — Consistency Ratio
- `is_valid` (`BOOLEAN`, Not Null) — `1` jika $CR \le 0.10$, `0` jika konsistensi gagal
- `created_at`, `updated_at` (`TIMESTAMP`)

### 8. Tabel `product_assessments`
*Tabel input data transaksi/stok produk pada periode tertentu.*
- `assessment_id` (`INT`, PK, Auto Increment)
- `product_id` (`INT`, FK -> `products.product_id`, Not Null)
- `period_id` (`INT`, FK -> `assessment_periods.period_id`, Not Null)
- `initial_stock` (`INT`, Not Null)
- `final_stock` (`INT`, Not Null)
- `units_sold` (`INT`, Not Null)
- `selling_price` (`DECIMAL(12,2)`, Not Null)
- `cost_price` (`DECIMAL(12,2)`, Not Null)
- `entry_date` (`DATE`, Not Null)
- `data_status` (`ENUM('layak', 'belum memadai')`, Not Null)
- `created_at`, `updated_at` (`TIMESTAMP`)

### 9. Tabel `normalization_results`
*Tabel hasil normalisasi matriks keputusan SAW.*
- `norm_id` (`INT`, PK, Auto Increment)
- `assessment_id` (`INT`, FK -> `product_assessments.assessment_id`, Not Null)
- `criterion_id` (`INT`, FK -> `criteria.criterion_id`, Not Null)
- `raw_value` (`DECIMAL(12,4)`, Not Null) — Nilai mentah kriteria
- `normalized_value` (`DECIMAL(8,4)`, Not Null) — Nilai ter-normalisasi ($R_{ij}$)
- `created_at`, `updated_at` (`TIMESTAMP`)

### 10. Tabel `ranking_results`
*Tabel hasil nilai preferensi ($V_i$) dan urutan peringkat produk.*
- `ranking_id` (`INT`, PK, Auto Increment)
- `assessment_id` (`INT`, FK -> `product_assessments.assessment_id`, Not Null)
- `period_id` (`INT`, FK -> `assessment_periods.period_id`, Not Null)
- `preference_value` (`DECIMAL(8,4)`, Not Null) — Nilai $V_i$
- `rank` (`INT`, Not Null) — Peringkat (1, 2, 3, dst.)
- `created_at`, `updated_at` (`TIMESTAMP`)

### 11. Tabel `promotion_decisions` dan `reports`

#### Tabel `promotion_decisions`
*Tabel keputusan penetapan diskon/promosi oleh Owner.*
- `decision_id` (`INT`, PK, Auto Increment)
- `ranking_id` (`INT`, FK -> `ranking_results.ranking_id`, Not Null)
- `discount_type` (`VARCHAR(50)`, Nullable) — Contoh: `"Diskon 20%"`, `"Flash Sale"`, `"Buy 1 Get 1"`
- `reason` (`TEXT`, Nullable)
- `decided_by` (`INT`, FK -> `users.user_id`, Not Null) — Merujuk pada Owner
- `decided_at` (`DATETIME`, Not Null)
- `created_at`, `updated_at` (`TIMESTAMP`)

#### Tabel `reports`
*Tabel riwayat pembuatan dan pencetakan laporan SPK.*
- `report_id` (`INT`, PK, Auto Increment)
- `generated_by` (`INT`, FK -> `users.user_id`, Not Null)
- `generated_at` (`DATETIME`, Not Null)
- `file_path` (`VARCHAR(255)`, Nullable) — Path file PDF/Excel laporan
- `created_at`, `updated_at` (`TIMESTAMP`)

---

## 4. Spesifikasi Model Eloquent & Relasi (`app/Models`)

Berikut adalah spesifikasi lengkap 12 Model Laravel beserta definisi relasi Eloquent:

### `app/Models/Role.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'role_id';
    protected $fillable = ['role_name'];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
```

### `app/Models/User.php`
```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = ['username', 'password', 'role_id', 'full_name', 'status'];
    protected $hidden = ['password'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function pairwiseComparisons()
    {
        return $this->hasMany(PairwiseComparison::class, 'created_by', 'user_id');
    }

    public function promotionDecisions()
    {
        return $this->hasMany(PromotionDecision::class, 'decided_by', 'user_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'generated_by', 'user_id');
    }

    public function isOwner(): bool
    {
        return optional($this->role)->role_name === 'owner';
    }

    public function isAdmin(): bool
    {
        return optional($this->role)->role_name === 'admin';
    }
}
```

### `app/Models/Criterion.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    protected $table = 'criteria';
    protected $primaryKey = 'criterion_id';
    protected $fillable = ['criterion_code', 'criterion_name', 'type', 'status'];

    public function ahpResults()
    {
        return $this->hasMany(AhpResult::class, 'criterion_id', 'criterion_id');
    }

    public function normalizationResults()
    {
        return $this->hasMany(NormalizationResult::class, 'criterion_id', 'criterion_id');
    }
}
```

### `app/Models/Product.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $fillable = ['product_code', 'product_name', 'category', 'status'];

    public function productAssessments()
    {
        return $this->hasMany(ProductAssessment::class, 'product_id', 'product_id');
    }
}
```

### `app/Models/AssessmentPeriod.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentPeriod extends Model
{
    protected $table = 'assessment_periods';
    protected $primaryKey = 'period_id';
    protected $fillable = ['period_name', 'start_date', 'end_date', 'status'];

    public function pairwiseComparisons()
    {
        return $this->hasMany(PairwiseComparison::class, 'period_id', 'period_id');
    }

    public function ahpResults()
    {
        return $this->hasMany(AhpResult::class, 'period_id', 'period_id');
    }

    public function productAssessments()
    {
        return $this->hasMany(ProductAssessment::class, 'period_id', 'period_id');
    }

    public function rankingResults()
    {
        return $this->hasMany(RankingResult::class, 'period_id', 'period_id');
    }
}
```

### `app/Models/PairwiseComparison.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PairwiseComparison extends Model
{
    protected $table = 'pairwise_comparisons';
    protected $primaryKey = 'comparison_id';
    protected $fillable = ['period_id', 'criterion_a_id', 'criterion_b_id', 'value', 'created_by'];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }

    public function criterionA()
    {
        return $this->belongsTo(Criterion::class, 'criterion_a_id', 'criterion_id');
    }

    public function criterionB()
    {
        return $this->belongsTo(Criterion::class, 'criterion_b_id', 'criterion_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
```

### `app/Models/AhpResult.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AhpResult extends Model
{
    protected $table = 'ahp_results';
    protected $primaryKey = 'ahp_result_id';
    protected $fillable = ['period_id', 'criterion_id', 'weight', 'lambda_max', 'ci', 'cr', 'is_valid'];

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }

    public function criterion()
    {
        return $this->belongsTo(Criterion::class, 'criterion_id', 'criterion_id');
    }
}
```

### `app/Models/ProductAssessment.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAssessment extends Model
{
    protected $table = 'product_assessments';
    protected $primaryKey = 'assessment_id';
    protected $fillable = [
        'product_id', 'period_id', 'initial_stock', 'final_stock',
        'units_sold', 'selling_price', 'cost_price', 'entry_date', 'data_status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }

    public function normalizationResults()
    {
        return $this->hasMany(NormalizationResult::class, 'assessment_id', 'assessment_id');
    }

    public function rankingResult()
    {
        return $this->hasOne(RankingResult::class, 'assessment_id', 'assessment_id');
    }
}
```

### `app/Models/NormalizationResult.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NormalizationResult extends Model
{
    protected $table = 'normalization_results';
    protected $primaryKey = 'norm_id';
    protected $fillable = ['assessment_id', 'criterion_id', 'raw_value', 'normalized_value'];

    public function productAssessment()
    {
        return $this->belongsTo(ProductAssessment::class, 'assessment_id', 'assessment_id');
    }

    public function criterion()
    {
        return $this->belongsTo(Criterion::class, 'criterion_id', 'criterion_id');
    }
}
```

### `app/Models/RankingResult.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RankingResult extends Model
{
    protected $table = 'ranking_results';
    protected $primaryKey = 'ranking_id';
    protected $fillable = ['assessment_id', 'period_id', 'preference_value', 'rank'];

    public function productAssessment()
    {
        return $this->belongsTo(ProductAssessment::class, 'assessment_id', 'assessment_id');
    }

    public function period()
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id', 'period_id');
    }

    public function promotionDecision()
    {
        return $this->hasOne(PromotionDecision::class, 'ranking_id', 'ranking_id');
    }
}
```

### `app/Models/PromotionDecision.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionDecision extends Model
{
    protected $table = 'promotion_decisions';
    protected $primaryKey = 'decision_id';
    protected $fillable = ['ranking_id', 'discount_type', 'reason', 'decided_by', 'decided_at'];

    public function rankingResult()
    {
        return $this->belongsTo(RankingResult::class, 'ranking_id', 'ranking_id');
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by', 'user_id');
    }
}
```

### `app/Models/Report.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'report_id';
    protected $fillable = ['generated_by', 'generated_at', 'file_path'];

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by', 'user_id');
    }
}
```

---

## 5. Algoritma & Perhitungan Matematis (AHP & SAW)

### 5.1 Perhitungan Metode AHP (Analytical Hierarchy Process)

Langkah-langkah komputasi di `AhpService` atau `AhpCalculationController`:

1. **Susun Matriks Perbandingan Berpasangan ($A$)**:
   Misalkan terdapat $n$ kriteria aktif. Nilai elemen $a_{ij}$ diambil dari tabel `pairwise_comparisons`.
   - $a_{ii} = 1$
   - $a_{ji} = \frac{1}{a_{ij}}$

2. **Hitung Jumlah Kolom Matriks**:
   $$S_j = \sum_{i=1}^n a_{ij}$$

3. **Normalisasi Matriks AHP**:
   $$N_{ij} = \frac{a_{ij}}{S_j}$$

4. **Hitung Bobot Prioritas Kriteria ($W_i$)**:
   $$W_i = \frac{\sum_{j=1}^n N_{ij}}{n}$$

5. **Pengujian Konsistensi (Consistency Check)**:
   - Hitung Vektor Perkalian Matriks $A \cdot W$: $V_i = \sum_{j=1}^n (a_{ij} \times W_j)$
   - Hitung $\lambda_{max}$:
     $$\lambda_{max} = \frac{1}{n} \sum_{i=1}^n \left(\frac{V_i}{W_i}\right)$$
   - Hitung *Consistency Index* ($CI$):
     $$CI = \frac{\lambda_{max} - n}{n - 1}$$
   - Hitung *Consistency Ratio* ($CR$):
     $$CR = \frac{CI}{RI}$$
     Tabel Random Index ($RI$) standar Saaty:
     | $n$ | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 |
     |---|---|---|---|---|---|---|---|---|---|---|
     | $RI$ | 0.00 | 0.00 | 0.58 | 0.90 | 1.12 | 1.24 | 1.32 | 1.41 | 1.45 | 1.49 |

   - **Keputusan**: Jika $CR \le 0.10$, maka `is_valid = 1` (bobot konsisten dan disimpankan ke `ahp_results`). Jika $CR > 0.10$, tampilkan peringatan bahwa matriks perbandingan tidak konsisten dan minta Owner menginput ulang perbandingan.

---

### 5.2 Perhitungan Metode SAW (Simple Additive Weighting)

Langkah-langkah komputasi di `SawService` atau `SawCalculationController`:

1. **Ekstrak Nilai Mentah ($X_{ij}$)**:
   Ambil data penilaian produk dari `product_assessments` (hanya produk yang `data_status = 'layak'`).
   Contoh Pemetaan Kriteria:
   - $C1$ (Terjual / Sales Volume): `units_sold` (Benefit)
   - $C2$ (Sisa Stok / Inventory): `final_stock` (Cost)
   - $C3$ (Margin Keuntungan): `(selling_price - cost_price)` (Benefit)
   - $C4$ (Persentase Penjualan): `(units_sold / initial_stock) * 100` (Benefit)

2. **Normalisasi Matriks SAW ($R_{ij}$)**:
   - **Kriteria Benefit**:
     $$R_{ij} = \frac{X_{ij}}{\max_k (X_{kj})}$$
   - **Kriteria Cost**:
     $$R_{ij} = \frac{\min_k (X_{kj})}{X_{ij}}$$
   Simpan nilai $X_{ij}$ (`raw_value`) dan $R_{ij}$ (`normalized_value`) ke tabel `normalization_results`.

3. **Hitung Nilai Preferensi ($V_i$)**:
   Kalikan nilai normalisasi $R_{ij}$ dengan Bobot Prioritas AHP ($W_j$) dari periode yang sesuai:
   $$V_i = \sum_{j=1}^n (W_j \times R_{ij})$$

4. **Pemeringkatan (Ranking)**:
   Urutkan alternatif berdasarkan nilai $V_i$ dari yang tertinggi hingga terendah. Simpan nilai $V_i$ dan urutan peringkat (`rank`) ke tabel `ranking_results`.

---

## 6. Tahapan Implementasi Langkah demi Langkah (Roadmap untuk Junior Programmer / AI Model)

Ikuti urutan fase berikut secara berurutan:

### Fase 1: Setup Environment & Database
1. Buat database baru di MySQL: `jitanichigo_spk`.
2. Update `.env` dengan kredensial database.
3. Jalankan `composer require laravel/breeze --dev` dan `php artisan breeze:install blade`.
4. Jalankan `php artisan key:generate`.
5. Jalankan `npm install && npm run build`.

### Fase 2: Pembuatan Migrasi & Seeder Database
1. Buat 11 file migration sesuai spesifikasi di Bab 3.
2. Buat `RoleSeeder` untuk mengisi data default tabel `roles` (`owner`, `admin`).
3. Buat `UserSeeder` untuk membuat akun awal Owner dan Admin.
4. Buat `CriteriaSeeder` untuk mengisi kriteria awal ($C1-C5$).
5. Jalankan `php artisan migrate:fresh --seed`.

### Fase 3: Pembuatan Model Eloquent & Relasi
1. Buat/Update 12 model di `app/Models/` sesuai kode di Bab 4.
2. Uji relasi dasar menggunakan `php artisan tinker`.

### Fase 4: Middleware & Autentikasi (Hak Akses)
1. Buat Middleware `RoleMiddleware` (`php artisan make:middleware RoleMiddleware`) untuk membatasi akses route berdasarkan role (`owner` vs `admin`).
2. Daftarkan middleware di `bootstrap/app.php` (Laravel 11 style).
3. Atur hak akses:
   - **Owner**: Kelola Perbandingan AHP, Keputusan Promosi, Lihat Laporan, Kelola User.
   - **Admin**: Input Master Produk, Input Master Kriteria, Input Periode Penilaian, Input Data Penilaian Produk, Jalankan Perhitungan SAW, Cetak Laporan.

### Fase 5: Modul Master Data (CRUD)
1. Buat `ProductController` (CRUD Data Produk).
2. Buat `CriterionController` (CRUD Data Kriteria).
3. Buat `AssessmentPeriodController` (Kelola Periode & Status Aktif/Selesai).
4. Buat tampilan UI (Views) menggunakan Blade + Tailwind CSS.

### Fase 6: Modul Perhitungan AHP (Owner)
1. Buat `AhpController`:
   - Halaman form perbandingan berpasangan matriks $n \times n$ (Saaty 1-9).
   - Logika simpan ke `pairwise_comparisons`.
2. Buat `AhpService`:
   - Eksekusi algoritma AHP (Matriks normalisasi, hitung $W_i$, $\lambda_{max}$, $CI$, $CR$).
   - Jika $CR \le 0.10$, simpan ke `ahp_results` (`is_valid = 1`).
   - Tampilkan visualisasi tabel bobot kriteria dan status konsistensi pada UI.

### Fase 7: Modul Penilaian Produk & Perhitungan SAW
1. Buat `ProductAssessmentController`:
   - Form input / import data stok dan penjualan produk per periode.
   - Penentuan otomatis `data_status` ('layak' jika `initial_stock > 0`, dsb).
2. Buat `SawService`:
   - Hitung `raw_value` per kriteria.
   - Hitung normalisasi $R_{ij}$ (Benefit / Cost) dan simpan ke `normalization_results`.
   - Ambil bobot AHP valid dari periode aktif.
   - Hitung $V_i$ dan urutkan peringkat, simpan ke `ranking_results`.
3. Tampilkan tabel hasil pemeringkatan produk dengan badge indikator prioritas promosi.

### Fase 8: Modul Keputusan Promosi & Laporan
1. Buat `PromotionDecisionController` (Khusus Owner):
   - Owner memilih produk berperingkat tinggi dan menetapkan `discount_type` (misal: "Diskon 20%") serta alasan.
   - Simpan ke `promotion_decisions`.
2. Buat `ReportController`:
   - Export laporan hasil rekomendasi & keputusan promosi ke format PDF (menggunakan package `barryvdh/laravel-dompdf`).
   - Simpan riwayat laporan ke `reports`.

### Fase 9: Pengujian, Verifikasi & QA Checklist
1. **Uji Validasi Form**: Pastikan input tidak bernilai minus/null.
2. **Uji Konsistensi AHP**: Uji dengan matriks konsisten ($CR \le 0.10$) dan matriks tidak konsisten ($CR > 0.10$).
3. **Uji Normalisasi SAW**: Verifikasi rumus Benefit ($X / \max$) dan Cost ($\min / X$) dengan kalkulasi manual/Excel.
4. **Uji Hak Akses**: Pastikan Admin tidak bisa mengakses menu input perbandingan AHP atau penetapan keputusan promosi Owner.

---
**Dokumen ini siap digunakan sebagai acuan kerja teknis.**
