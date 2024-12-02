<?php

namespace App\Filament\Resources\TryoutsResource\RelationManagers;

use App\Models\BidangTryouts;
use App\Models\KompetensiTryouts;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';
    protected static ?string $title = 'List Soal';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nomor')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->default(function (): int {
                        return static::getOwnerRecord()->questions()->where('tryout_id', static::getOwnerRecord()->id)->max('nomor') + 1;
                    })
                    ->columnSpanFull(),
                Forms\Components\Select::make('bidang_id')
                    ->searchable()
                    ->native(false)
                    ->placeholder('Pilih salah satu')
                    ->relationship('bidang', 'nama')
                    ->columnSpanFull()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')
                            ->placeholder('Masukkan nama bidang')
                            ->unique(BidangTryouts::class, 'nama')
                            ->validationMessages([
                                'unique' => 'Nama bidang sudah digunakan',
                            ])
                            ->required(),
                    ])
                    ->required(),
                Forms\Components\Select::make('kompetensi_id')
                    ->searchable()
                    ->native(false)
                    ->placeholder('Pilih salah satu')
                    ->relationship('kompetensi', 'nama')
                    ->columnSpanFull()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')
                            ->placeholder('Masukkan nama kompetensi')
                            ->unique(KompetensiTryouts::class, 'nama')
                            ->validationMessages([
                                'unique' => 'Nama kompetensi sudah digunakan',
                            ])
                            ->required(),
                    ])
                    ->required(),
                Forms\Components\RichEditor::make('soal')
                    ->required()
                    ->fileAttachmentsDirectory('attachments')
                    ->fileAttachmentsVisibility('public')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('pilihan_a')->label('Pilihan A')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('pilihan_b')->label('Pilihan B')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('pilihan_c')->label('Pilihan C')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('pilihan_d')->label('Pilihan D')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('pilihan_e')->label('Pilihan E')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('jawaban')
                    ->label('Pilih Jawaban Benar')
                    ->required()
                    ->options([
                        'a' => 'A',
                        'b' => 'B',
                        'c' => 'C',
                        'd' => 'D',
                        'e' => 'E',
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomor')
            ->emptyStateHeading('Tidak Ada Soal')
            ->emptyStateDescription('Tidak ada soal yang tersedia saat ini.')
            ->emptyStateIcon('heroicon-o-archive-box-x-mark')
            ->columns([
                Tables\Columns\TextColumn::make('nomor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bidang.nama'),
                Tables\Columns\TextColumn::make('kompetensi.nama'),
                Tables\Columns\TextColumn::make('soal')
                    ->searchable()
                    ->formatStateUsing(fn(string $state): string => strip_tags($state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bidang_id')
                    ->relationship('bidang', 'nama')
                    ->searchable()
                    ->label('Bidang')
                    ->placeholder('Semua Bidang'),
                Tables\Filters\SelectFilter::make('kompetensi_id')
                    ->relationship('kompetensi', 'nama')
                    ->searchable()
                    ->label('Kompetensi')
                    ->placeholder('Semua Kompetensi'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tambah Soal')->icon('heroicon-o-plus'),
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
    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->questions ? $ownerRecord->questions->count() : null;
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
