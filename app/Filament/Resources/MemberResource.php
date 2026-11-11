<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationLabel = 'Data Anggota';
    protected static ?string $modelLabel = 'Anggota';
    protected static ?string $pluralModelLabel = 'Data Anggota';
    protected static ?string $navigationGroup = 'Perpustakaan';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Anggota')->columns(2)->schema([
                    TextInput::make('nis')->label('NIS')->required()->unique(ignoreRecord: true)->maxLength(30),
                    TextInput::make('name')->label('Nama Lengkap')->required(),
                    TextInput::make('class_name')->label('Kelas')->required()->maxLength(30),
                    TextInput::make('email')->label('Email')->email()->unique(ignoreRecord: true),
                    TextInput::make('phone')->label('Nomor Telepon'),
                    Select::make('status')->label('Status')->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'])->default('aktif')->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nis')->label('NIS')->searchable()->sortable(),
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('class_name')->label('Kelas')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->toggleable(),
                TextColumn::make('status')->label('Status')->badge()->color(fn (string $state): string => $state === 'aktif' ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']),
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
