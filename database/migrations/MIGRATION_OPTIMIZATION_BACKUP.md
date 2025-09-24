# Migration Optimization - Backup Script
# Tanggal: 2025-09-23
# Migrations yang akan dihapus karena sudah dikonsolidasi:

## BARANG_MEDIS TABLE - Dikonsolidasi ke: 2025_08_20_034642_create_barang_medis_table_optimized.php
# HAPUS:
# - 2025_08_20_034642_create_barang_medis_table.php (original)
# - 2025_09_23_091430_add_new_fields_to_barang_medis_table.php (add fields)
# - 2025_09_23_091828_remove_tanggal_from_barang_medis_table.php (remove tanggal)
# - 2025_09_23_092600_remove_jumlah_kemasan_from_barang_medis_table.php (remove jumlah_kemasan)
# - 2025_09_23_094135_update_tipe_enum_in_barang_medis_table.php (update enum)
# - 2025_09_23_094354_remove_duplicate_kategori_columns_from_barang_medis.php (remove duplicates)

## STOK_HISTORIES TABLE - Dikonsolidasi ke: 2025_09_12_113406_create_stok_histories_table_optimized.php
# HAPUS:
# - 2025_09_12_113406_create_stok_histories_table.php (original)
# - 2025_09_18_134654_add_details_to_stok_histories_table.php.php (add details)
# - 2025_09_23_135106_update_null_user_id_in_stok_histories.php (update nulls)

## DETAIL_PERMINTAAN_BARANG TABLE - Dikonsolidasi ke: 2025_09_03_100907_create_detail_permintaan_barang_table_optimized.php
# HAPUS:
# - 2025_09_03_100907_create_detail_permintaan_barang_table.php (original)
# - 2025_09_19_075903_add_additional_fields_to_detail_permintaan_barang_table.php.php (add fields)

## TOTAL: 11 files akan dihapus, 3 files baru dibuat sebagai konsolidasi

# Untuk restore, rename file _optimized dan hapus yang lama.