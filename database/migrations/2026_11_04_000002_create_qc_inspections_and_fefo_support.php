<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel QC Inspections (Inbound Receiving & Production Defrost)
        if (!Schema::hasTable('qc_inspections')) {
            Schema::create('qc_inspections', function (Blueprint $table) {
                $table->id();
                $table->string('reference_type'); // App\Models\BarangMasuk / App\Models\PermintaanBarang
                $table->unsignedBigInteger('reference_id');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Petugas QC / Pemeriksa
                $table->string('tipe_qc'); // inbound_receiving / production_defrost
                $table->string('nomor_dokumen')->nullable(); // FRM.JFD.01.01 (Inbound) / FRM.JFD.01.10 (Defrost)
                $table->date('tanggal_cek');
                $table->time('jam_cek')->nullable();

                // Parameter QC Inbound (Foto 3)
                $table->boolean('kebersihan_kendaraan')->default(true);
                $table->boolean('kemasan_utuh')->default(true);
                $table->boolean('expire_date_aman')->default(true);
                $table->boolean('bebas_benda_asing')->default(true);

                // Parameter QC Fisik (Foto 2 & 3)
                $table->boolean('kondisi_aroma')->default(true); // Bau
                $table->boolean('kondisi_tekstur')->default(true); // Tekstur
                $table->boolean('kondisi_penampakan')->default(true); // Visual / Warna
                $table->decimal('suhu', 8, 2)->nullable(); // Suhu bahan beku (°C)

                // Parameter Timbangan & Susut (Drip Loss / Shrinkage)
                $table->decimal('berat_keluar_gudang', 12, 2)->nullable();
                $table->decimal('berat_riil_diterima', 12, 2)->nullable();
                $table->decimal('susut_kg', 12, 2)->nullable();
                $table->decimal('susut_persen', 6, 2)->nullable();

                // Hasil & Status
                $table->string('status_qc')->default('pass'); // pass / fail
                $table->text('tindakan_koreksi')->nullable();
                $table->text('catatan')->nullable();

                $table->timestamps();

                $table->index(['reference_type', 'reference_id']);
            });
        }

        // 2. Tambah kolom batch_number & jumlah_pack di tabel barang_masuk jika belum ada
        Schema::table('barang_masuk', function (Blueprint $table) {
            if (!Schema::hasColumn('barang_masuk', 'batch_number')) {
                $table->string('batch_number')->nullable()->after('no_masuk');
            }
            if (!Schema::hasColumn('barang_masuk', 'jumlah_pack')) {
                $table->integer('jumlah_pack')->nullable()->after('jumlah');
            }
        });

        // 3. Tambah status_qc di permintaan_barang jika belum ada
        Schema::table('permintaan_barang', function (Blueprint $table) {
            if (!Schema::hasColumn('permintaan_barang', 'status_qc')) {
                $table->string('status_qc')->default('pending')->after('status'); // pending, pass, fail
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qc_inspections');

        Schema::table('barang_masuk', function (Blueprint $table) {
            if (Schema::hasColumn('barang_masuk', 'batch_number')) {
                $table->dropColumn('batch_number');
            }
            if (Schema::hasColumn('barang_masuk', 'jumlah_pack')) {
                $table->dropColumn('jumlah_pack');
            }
        });

        Schema::table('permintaan_barang', function (Blueprint $table) {
            if (Schema::hasColumn('permintaan_barang', 'status_qc')) {
                $table->dropColumn('status_qc');
            }
        });
    }
};

