<?php

namespace Modules\Order\Filament\Resources;

use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Order\Filament\Resources\OrderResource\Pages;
use Modules\Order\Models\Order;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|UnitEnum|null $navigationGroup = 'Orders';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_phone')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Components\Section::make('Order Details')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(Order::getStatuses())
                            ->required()
                            ->default(Order::STATUS_PENDING),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order #')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('customer_phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Order::STATUS_PENDING => 'warning',
                        Order::STATUS_CONFIRMED => 'info',
                        Order::STATUS_SHIPPED => 'primary',
                        Order::STATUS_DELIVERED => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Order::getStatuses())
                    ->multiple(),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record): bool => $record->status === Order::STATUS_PENDING)
                    ->requiresConfirmation()
                    ->action(fn (Order $record) => $record->update(['status' => Order::STATUS_CONFIRMED]))
                    ->after(fn () => Notification::make()
                        ->success()
                        ->title('Order confirmed')
                        ->send()),
                Actions\Action::make('ship')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn (Order $record): bool => $record->status === Order::STATUS_CONFIRMED)
                    ->requiresConfirmation()
                    ->action(fn (Order $record) => $record->update(['status' => Order::STATUS_SHIPPED]))
                    ->after(fn () => Notification::make()
                        ->success()
                        ->title('Order marked as shipped')
                        ->send()),
                Actions\Action::make('deliver')
                    ->icon('heroicon-o-home')
                    ->color('success')
                    ->visible(fn (Order $record): bool => $record->status === Order::STATUS_SHIPPED)
                    ->requiresConfirmation()
                    ->action(fn (Order $record) => $record->update(['status' => Order::STATUS_DELIVERED]))
                    ->after(fn () => Notification::make()
                        ->success()
                        ->title('Order marked as delivered')
                        ->send()),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('Customer Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('customer_name'),
                        Infolists\Components\TextEntry::make('customer_email'),
                        Infolists\Components\TextEntry::make('customer_phone'),
                    ])
                    ->columns(3),

                Components\Section::make('Order Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                Order::STATUS_PENDING => 'warning',
                                Order::STATUS_CONFIRMED => 'info',
                                Order::STATUS_SHIPPED => 'primary',
                                Order::STATUS_DELIVERED => 'success',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('total')
                            ->money('USD'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                    ])
                    ->columns(3),

                Components\Section::make('Order Items')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('orderItems')
                            ->schema([
                                Infolists\Components\TextEntry::make('product_name')
                                    ->label('Product'),
                                Infolists\Components\TextEntry::make('product_price')
                                    ->label('Price')
                                    ->money('USD'),
                                Infolists\Components\TextEntry::make('quantity'),
                                Infolists\Components\TextEntry::make('subtotal')
                                    ->money('USD'),
                            ])
                            ->columns(4),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
