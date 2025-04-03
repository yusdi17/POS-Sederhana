<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
            ->required()
            ->maxLength(255),

        Forms\Components\Select::make('category_id')
            ->label('Kategori')
            ->options(\App\Models\Category::pluck('name', 'id'))
            ->searchable()
            ->required(),

        Forms\Components\TextInput::make('price')
            ->label('Harga')
            ->required()
            ->numeric()
            ->prefix('Rp'),

        Forms\Components\Select::make('status')
            ->label('Status')
            ->options([
                'tersedia' => 'Tersedia',
                'tidak_tersediahabis' => 'Habis',
            ])
            ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),
    
            Tables\Columns\TextColumn::make('category.name') // Menampilkan nama kategori
                ->label('Kategori')
                ->sortable()
                ->searchable(),
    
            Tables\Columns\TextColumn::make('price')
                ->label('Harga')
                ->money('idr')
                ->sortable(),
    
            Tables\Columns\TextColumn::make('status')
                ->label('Status'),
    
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
    
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            //
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
