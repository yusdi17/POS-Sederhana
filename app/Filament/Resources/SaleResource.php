<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Sale;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SaleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SaleResource\RelationManagers;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('customer_id')
                ->label('Pelanggan')
                ->options(\App\Models\Customer::pluck('name', 'id'))
                ->searchable()
                ->nullable(),

            Forms\Components\Repeater::make('details')
                ->relationship('details')
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Produk')
                        ->options(\App\Models\Product::pluck('name', 'id'))
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => 
                            $set('subtotal', \App\Models\Product::find($state)?->price ?? 0)
                        ),

                    Forms\Components\TextInput::make('quantity')
                        ->label('Jumlah')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => 
                            $set('subtotal', $state * (\App\Models\Product::find($get('product_id'))?->price ?? 0))
                        ),

                    Forms\Components\TextInput::make('subtotal')
                        ->label('Subtotal')
                        ->numeric()
                        ->readOnly(),
                ])
                ->defaultItems(1)
                ->afterStateUpdated(fn ($state, callable $set) => 
                    $set('total_price', collect($state)->sum('subtotal'))
                ),

            Forms\Components\TextInput::make('total_price')
                ->label('Total Harga')
                ->numeric()
                ->readOnly()
                ->default(0),
            
            Forms\Components\Select::make('payment_status')
                ->label('Status Pembayaran')
                ->options([
                    'paid' => 'Lunas',
                    'unpaid' => 'Belum Lunas',
                ])
                ->default('unpaid')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('customer.name')
                ->label('Pelanggan')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('total_price')
                ->label('Total Harga')
                ->money('IDR')
                ->sortable(),

            Tables\Columns\TextColumn::make('payment_status')
                ->label('Status Pembayaran')
                ->badge()
                ->colors([
                    'unpaid' => 'danger',
                    'paid' => 'success',
                ])
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Tanggal Transaksi')
                ->dateTime()
                ->sortable(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
