<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $kode
 * @property string $nama
 * @property string $posisi
 * @property string $grup
 * @property string|null $sub_grup
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JournalEntry> $journalEntries
 * @property-read int|null $journal_entries_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereGrup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account wherePosisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereSubGrup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereUpdatedAt($value)
 */
	class Account extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $user_name
 * @property string $user_role
 * @property string $action
 * @property string $object
 * @property string $menu
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $formatted_log
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereObject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserRole($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama
 * @property string $jenis
 * @property string $account_kode
 * @property int $sort_order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OperationalCost> $operationalCosts
 * @property-read int|null $operational_costs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereAccountKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseCategory whereUpdatedAt($value)
 */
	class ExpenseCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $journal_transaction_id
 * @property string $account_kode
 * @property int $debit
 * @property int $credit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Account $account
 * @property-read \App\Models\JournalTransaction $transaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalEntry whereAccountKode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalEntry whereCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalEntry whereDebit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalEntry whereJournalTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalEntry whereUpdatedAt($value)
 */
	class JournalEntry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $deskripsi
 * @property string|null $ref
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JournalEntry> $entries
 * @property-read int|null $entries_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalTransaction whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalTransaction whereRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalTransaction whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JournalTransaction whereUpdatedAt($value)
 */
	class JournalTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property int|null $expense_category_id
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $kat
 * @property string $desk
 * @property int $jumlah
 * @property string $jenis
 * @property int|null $journal_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExpenseCategory|null $expenseCategory
 * @property-read \App\Models\JournalTransaction|null $journalTransaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost whereDesk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost whereExpenseCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost whereJournalTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost whereKat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OperationalCost whereUpdatedAt($value)
 */
	class OperationalCost extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string|null $production_record_id
 * @property string $nama
 * @property string $satuan
 * @property int $harga
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProductionRecord|null $productionRecord
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereProductionRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $production_record_id
 * @property string $raw_material_id
 * @property float $jumlah
 * @property string $satuan
 * @property int $harga_satuan
 * @property int $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProductionRecord $productionRecord
 * @property-read \App\Models\RawMaterial $rawMaterial
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage whereHargaSatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage whereProductionRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage whereRawMaterialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage whereSatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionMaterialUsage whereUpdatedAt($value)
 */
	class ProductionMaterialUsage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $product_name
 * @property int $jumlah
 * @property string $satuan
 * @property string $status
 * @property string|null $notes
 * @property int $total_material_cost
 * @property int|null $journal_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductionMaterialUsage> $materialUsages
 * @property-read int|null $material_usages_count
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord whereJournalTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord whereSatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord whereTotalMaterialCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductionRecord whereUpdatedAt($value)
 */
	class ProductionRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $nama
 * @property numeric $jumlah
 * @property string $satuan
 * @property numeric $min
 * @property int $harga
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RawMaterialRestock> $restocks
 * @property-read int|null $restocks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterial query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterial whereHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterial whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterial whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterial whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterial whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterial whereSatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterial whereUpdatedAt($value)
 */
	class RawMaterial extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $raw_material_id
 * @property \Illuminate\Support\Carbon $tanggal
 * @property float $jumlah
 * @property int $harga
 * @property int $total
 * @property string|null $catatan
 * @property int|null $journal_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\JournalTransaction|null $journalTransaction
 * @property-read \App\Models\RawMaterial $rawMaterial
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock whereHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock whereJournalTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock whereRawMaterialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawMaterialRestock whereUpdatedAt($value)
 */
	class RawMaterialRestock extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property \Illuminate\Support\Carbon $tanggal
 * @property int $total
 * @property string $metode
 * @property int $jumlah
 * @property int|null $journal_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\JournalTransaction|null $journalTransaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTransaction whereJournalTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTransaction whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTransaction whereMetode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTransaction whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTransaction whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTransaction whereUpdatedAt($value)
 */
	class SalesTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unit whereUpdatedAt($value)
 */
	class Unit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $role
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 */
	class User extends \Eloquent {}
}

