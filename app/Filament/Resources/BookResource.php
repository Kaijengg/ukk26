<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationLabel = 'Data Buku';
    protected static ?string $modelLabel = 'Buku';
    protected static ?string $pluralModelLabel = 'Data Buku';
    protected static ?string $navigationGroup = 'Perpustakaan';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Buku')->columns(2)->schema([
                    TextInput::make('isbn')->label('ISBN')->maxLength(30)->unique(ignoreRecord: true),
                    TextInput::make('title')->label('Judul')->required()->maxLength(255),
                    TextInput::make('author')->label('Penulis')->required(),
                    TextInput::make('publisher')->label('Penerbit'),
                    TextInput::make('published_year')->label('Tahun Terbit')->numeric()->minValue(1900)->maxValue((int) date('Y') + 1),
                    TextInput::make('category')->label('Kategori')->required()->default('Umum'),
                    TextInput::make('stock')->label('Jumlah Eksemplar')->numeric()->required()->minValue(0)->default(0),
                    Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable()->sortable()->wrap(),
                TextColumn::make('author')->label('Penulis')->searchable()->sortable(),
                TextColumn::make('category')->label('Kategori')->badge()->searchable(),
                TextColumn::make('stock')->label('Total')->sortable(),
                TextColumn::make('available_stock')->label('Tersedia')->sortable()->badge()->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
                TextColumn::make('isbn')->label('ISBN')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')->label('Kategori')->options(fn () => Book::query()->distinct()->orderBy('category')->pluck('category', 'category')->all()),
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
