<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationLabel = 'Transaksi Peminjaman';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?string $pluralModelLabel = 'Transaksi Peminjaman';
    protected static ?string $navigationGroup = 'Perpustakaan';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Peminjaman')->columns(2)->schema([
                    Select::make('member_id')->label('Anggota')->relationship('member', 'name')->searchable()->preload()->required()->options(fn () => \App\Models\Member::where('status', 'aktif')->pluck('name', 'id')),
                    Select::make('book_id')->label('Buku (stok tersedia)')->relationship('book', 'title')->searchable()->preload()->required()->options(fn () => \App\Models\Book::where('available_stock', '>', 0)->pluck('title', 'id')),
                    DatePicker::make('loan_date')->label('Tanggal Pinjam')->required()->default(now()),
                    DatePicker::make('due_date')->label('Batas Pengembalian')->required()->default(now()->addDays(7))->afterOrEqual('loan_date'),
                    Select::make('status')->label('Status')->options(['dipinjam' => 'Dipinjam', 'dikembalikan' => 'Dikembalikan'])->default('dipinjam')->required()->visibleOn('edit'),
                    Textarea::make('notes')->label('Catatan')->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.name')->label('Anggota')->searchable()->sortable(),
                TextColumn::make('book.title')->label('Buku')->searchable()->sortable()->wrap(),
                TextColumn::make('loan_date')->label('Tanggal Pinjam')->date()->sortable(),
                TextColumn::make('due_date')->label('Batas Kembali')->date()->sortable(),
                TextColumn::make('status')->label('Status')->badge()->color(fn (string $state): string => $state === 'dikembalikan' ? 'success' : 'warning'),
                TextColumn::make('due_date')->label('Keterangan')->state(fn ($record) => $record->status === 'dipinjam' && $record->due_date->isPast() ? 'Terlambat' : ($record->status === 'dikembalikan' ? 'Selesai' : 'Aktif'))->badge()->color(fn (string $state): string => $state === 'Terlambat' ? 'danger' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(['dipinjam' => 'Dipinjam', 'dikembalikan' => 'Dikembalikan']),
            ])
            ->actions([
                Action::make('return')->label('Tandai dikembalikan')->icon('heroicon-o-check-circle')->color('success')->requiresConfirmation()->visible(fn ($record) => $record->status === 'dipinjam')->action(function ($record) { $record->update(['status' => 'dikembalikan', 'returned_at' => now()->toDateString()]); }),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['member', 'book']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
}
