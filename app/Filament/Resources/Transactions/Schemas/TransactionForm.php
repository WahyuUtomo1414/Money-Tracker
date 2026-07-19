<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Enums\TransactionTypeEnum;
use App\Models\TransactionLedger;
use App\Services\TransactionScopeService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->schema([
                        Hidden::make('uuid')
                            ->default(fn (): string => (string) Str::uuid()),
                        TextInput::make('transaction_no')
                            ->label('Nomor Transaksi')
                            ->helperText('Nomor transaksi diisi otomatis oleh sistem.')
                            ->disabled()
                            ->dehydrated(false),
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->validationAttribute('tanggal transaksi')
                            ->validationMessages([
                                'required' => 'Tanggal transaksi wajib diisi.',
                            ])
                            ->required()
                            ->native(false),
                        Select::make('transaction_type')
                            ->label('Tipe Transaksi')
                            ->options(TransactionTypeEnum::options())
                            ->placeholder('Pilih tipe transaksi')
                            ->validationAttribute('tipe transaksi')
                            ->validationMessages([
                                'required' => 'Tipe transaksi wajib dipilih.',
                            ])
                            ->required(),
                        Select::make('wallet_id')
                            ->label('Rekening')
                            ->relationship('wallet', 'account_name', fn ($query) => app(TransactionScopeService::class)->scopeWalletQuery($query))
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->display_name)
                            ->placeholder('Pilih rekening')
                            ->helperText(function (Get $get): ?string {
                                $walletId = $get('wallet_id');

                                if (! filled($walletId)) {
                                    return 'Pilih rekening untuk melihat saldo saat ini.';
                                }

                                $balance = static::getWalletBalance($walletId);

                                return 'Saldo rekening saat ini: Rp ' . number_format((float) $balance, 0, ',', '.');
                            })
                            ->live()
                            ->searchable()
                            ->preload()
                            ->validationAttribute('rekening')
                            ->validationMessages([
                                'required' => 'Rekening wajib dipilih.',
                            ])
                            ->required(),
                        TextInput::make('amount')
                            ->label('Nominal')
                            ->prefix('Rp')
                            ->inputMode('numeric')
                            ->stripCharacters('.')
                            ->formatStateUsing(fn ($state): ?string => filled($state) ? number_format((int) $state, 0, ',', '.') : null)
                            ->afterStateUpdatedJs(<<<'JS'
                                let value = ($state ?? '').toString().replace(/\D/g, '')
                                $set('amount', value ? new Intl.NumberFormat('id-ID').format(Number(value)) : null)
                            JS)
                            ->mutateStateForValidationUsing(fn (?string $state): ?int => filled($state) ? (int) preg_replace('/\D/', '', $state) : null)
                            ->dehydrateStateUsing(fn (?string $state): ?int => filled($state) ? (int) preg_replace('/\D/', '', $state) : null)
                            ->validationAttribute('nominal')
                            ->validationMessages([
                                'required' => 'Nominal transaksi wajib diisi.',
                                'integer' => 'Nominal transaksi harus berupa angka.',
                                'min' => 'Nominal transaksi minimal Rp 1.',
                            ])
                            ->required()
                            ->rule('integer')
                            ->rule('min:1')
                            ->rule(function (Get $get): \Closure {
                                return function (string $attribute, $value, \Closure $fail) use ($get): void {
                                    if ($get('transaction_type') !== TransactionTypeEnum::Payment->value) {
                                        return;
                                    }

                                    $walletId = $get('wallet_id');

                                    if (! filled($walletId) || ! filled($value)) {
                                        return;
                                    }

                                    $balance = static::getWalletBalance($walletId);

                                    if ((int) $value > $balance) {
                                        $fail('Nominal pembayaran tidak boleh melebihi saldo rekening yang dipilih.');
                                    }
                                };
                            }),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name', fn ($query) => $query->where('type', 'transaction'))
                            ->placeholder('Pilih kategori')
                            ->searchable()
                            ->preload(),
                        Select::make('goal_id')
                            ->label('Target Tabungan')
                            ->relationship('goal', 'name', fn ($query) => app(TransactionScopeService::class)->scopeGoalQuery($query))
                            ->placeholder('Pilih target tabungan')
                            ->searchable()
                            ->preload(),
                        FileUpload::make('image')
                            ->label('Bukti Gambar')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->disk('public')
                            ->directory('transactions')
                            ->openable()
                            ->previewable()
                            ->downloadable()
                            ->imagePreviewHeight('220')
                            ->maxSize(5120)
                            ->validationAttribute('bukti gambar')
                            ->validationMessages([
                                'image' => 'Berkas bukti harus berupa gambar.',
                                'max' => 'Ukuran bukti gambar maksimal 5 MB.',
                            ])
                            ->helperText('Unggah bukti gambar berformat JPG, PNG, atau WEBP maksimal 5 MB. Klik preview untuk membuka pop up gambar.'),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Tambahkan catatan transaksi bila diperlukan')
                            ->columnSpanFull(),
                        Hidden::make('active')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    protected static function getWalletBalance(int | string | null $walletId): float
    {
        if (! filled($walletId)) {
            return 0;
        }

        return (float) (TransactionLedger::query()
            ->where('wallet_id', $walletId)
            ->latest('transaction_date')
            ->latest('id')
            ->value('end_amount') ?? 0);
    }
}
