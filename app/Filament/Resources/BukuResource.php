<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuResource\Pages;
use App\Filament\Resources\BukuResource\RelationManagers;
use App\Models\Buku;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BukuResource extends Resource
{
    protected static ?string $model = Buku::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Manajemen Buku';
    protected static ?string $modelLabel = 'Buku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Buku')
                ->description('Isikan detail informasi buku.')
                ->schema([
                    TextInput::make('judul')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('pengarang')
                        ->required()
                        ->maxLength(255),
                    Select::make('kategori')
                        ->options([
                            'Fiksi' => 'Fiksi',
                            'Non Fiksi' => 'Non Fiksi',
                        ])
                        ->placeholder('Pilih Kategori') // Opsional
                        ->required(), // Jika kategori harus diisi
                ])->columns(2), // Menata field dalam 2 kolom
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pengarang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kategori')
                    ->searchable()
                    ->sortable(),
                    TextColumn::make('terjual')
                    ->label('Jumlah Terjual')
                    ->numeric(),
                // Kolom 'created_at' dan 'updated_at' otomatis dari $table->timestamps()
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
                // Filter berdasarkan kategori
                Tables\Filters\SelectFilter::make('kategori')
                    ->options([
                        'Fiksi' => 'Fiksi',
                        'Non Fiksi' => 'Non Fiksi',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBukus::route('/'),
            'create' => Pages\CreateBuku::route('/create'),
            'edit' => Pages\EditBuku::route('/{record}/edit'),
        ];
    }
}
