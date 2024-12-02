<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TryoutsResource\Pages;
use App\Filament\Resources\TryoutsResource\RelationManagers;
use App\Models\BatchTryouts;
use App\Models\Tryouts;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TryoutsResource extends Resource
{
    protected static ?string $model = Tryouts::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    protected static ?string $navigationGroup = 'Tryouts';

    protected static ?string $navigationLabel = 'Tryouts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('batch_id')
                        ->relationship('batch', 'nama')
                        ->columnSpanFull()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('nama')
                                ->placeholder('Masukkan nama batch')
                                ->unique(BatchTryouts::class, 'nama')
                                ->validationMessages([
                                    'unique' => 'Nama batch sudah digunakan',
                                ])
                                ->required(),
                            Forms\Components\DatePicker::make('start_date')->label('Tanggal Mulai')
                                ->native(false)
                                ->minDate(now())
                                ->required(),
                            Forms\Components\DatePicker::make('end_date')->label('Tanggal Selesai')
                                ->native(false)
                                ->minDate(fn(?BatchTryouts $record): string => $record?->start_date ?? now())
                                ->required(),
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('nama')
                        ->placeholder('Nama Tryout')
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\TextInput::make('waktu')
                        ->placeholder('Waktu Pengerjaan Tryout (dalam menit)')
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal Dibuat')
                        ->default(now())
                        ->native(false)
                        ->displayFormat('l, d F Y')
                        ->locale('id')
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('batch.nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('waktu')
                    ->searchable(),
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
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\QuestionRelationManager::class,
            RelationManagers\TestimonisRelationManager::class,
            RelationManagers\UserTryoutsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTryouts::route('/'),
            'create' => Pages\CreateTryouts::route('/create'),
            'view' => Pages\ViewTryouts::route('/{record}'),
            'edit' => Pages\EditTryouts::route('/{record}/edit'),
        ];
    }
}
