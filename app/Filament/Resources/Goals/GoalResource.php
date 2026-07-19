<?php

namespace App\Filament\Resources\Goals;

use App\Filament\Resources\Goals\Pages\CreateGoal;
use App\Filament\Resources\Goals\Pages\EditGoal;
use App\Filament\Resources\Goals\Pages\ListGoals;
use App\Filament\Resources\Goals\Pages\ViewGoal;
use App\Filament\Resources\Goals\Schemas\GoalForm;
use App\Filament\Resources\Goals\Tables\GoalsTable;
use App\Models\Goal;
use App\Models\User;
use App\Services\TransactionScopeService;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class GoalResource extends Resource
{
    protected static ?string $model = Goal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Target Tabungan';

    protected static ?string $modelLabel = 'Target Tabungan';

    protected static ?string $pluralModelLabel = 'Target Tabungan';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GoalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GoalsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Target Tabungan')
                    ->schema([
                        TextEntry::make('wallet.display_name')
                            ->label('Rekening'),
                        TextEntry::make('name')
                            ->label('Nama Target'),
                        TextEntry::make('target_amount')
                            ->label('Target Nominal')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),
                        TextEntry::make('target_date')
                            ->label('Target Tanggal')
                            ->date(),
                        IconEntry::make('active')
                            ->label('Aktif')
                            ->boolean(),
                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->default('-'),
                        TextEntry::make('createdBy.name')
                            ->label('Dibuat Oleh')
                            ->badge()
                            ->default('-'),
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = app(TransactionScopeService::class)->scopeGoalQuery(
            parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]),
        );

        if (! static::isSuperAdmin()) {
            $query->where('created_by', auth()->id());
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGoals::route('/'),
            'create' => CreateGoal::route('/create'),
            'view' => ViewGoal::route('/{record}'),
            'edit' => EditGoal::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        $query = app(TransactionScopeService::class)->scopeGoalQuery(
            parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]),
        );

        if (! static::isSuperAdmin()) {
            $query->where('created_by', auth()->id());
        }

        return $query;
    }

    public static function canEdit(Model $record): bool
    {
        return static::isSuperAdmin() || ((int) $record->created_by === (int) auth()->id());
    }

    public static function canDelete(Model $record): bool
    {
        return static::canEdit($record);
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::canEdit($record);
    }

    public static function canRestore(Model $record): bool
    {
        return static::canEdit($record);
    }

    protected static function isSuperAdmin(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user?->isSuperAdmin() ?? false;
    }
}
