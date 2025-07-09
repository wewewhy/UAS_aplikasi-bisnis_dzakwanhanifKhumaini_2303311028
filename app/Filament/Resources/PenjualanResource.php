<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use App\Models\Penjualan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Buku;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Manajemen Penjualan'; // Label di sidebar navigasi

    protected static ?string $modelLabel = 'Penjualan'; // Label untuk item tunggal

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Penjualan')
                ->description('Isikan informasi lengkap penjualan buku.')
                ->schema([
                    Select::make('buku_id')
                        ->label('Buku')
                        ->relationship('buku', 'judul')
                        ->getOptionLabelFromRecordUsing(fn (Buku $record) => "{$record->judul} ({$record->pengarang})")
                        ->searchable()
                        ->preload(),
                    DatePicker::make('tanggal')
                        ->required()
                        ->default(now()),
                    TextInput::make('eksemplar')
                        ->required()
                        ->numeric()
                        ->minValue(1),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('buku.judul')
                ->label('Judul Buku')
                ->searchable()
                ->sortable(),
            TextColumn::make('tanggal')
                ->date()
                ->sortable(),
            TextColumn::make('eksemplar')
                ->numeric()
                ->sortable(),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            // Filter berdasarkan tanggal penjualan
            Tables\Filters\Filter::make('tanggal')
                ->form([
                    Forms\Components\DatePicker::make('dari_tanggal')
                        ->label('Dari Tanggal'),
                    Forms\Components\DatePicker::make('sampai_tanggal')
                        ->label('Sampai Tanggal'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['dari_tanggal'],
                            fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                        )
                        ->when(
                            $data['sampai_tanggal'],
                            fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                        );
                }),
            // Filter berdasarkan Buku
            Tables\Filters\SelectFilter::make('buku_id')
                ->relationship('buku', 'judul')
                ->label('Filter Buku')
                ->searchable()
                ->preload(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}
