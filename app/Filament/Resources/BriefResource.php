<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BriefResource\Pages;
use App\Models\Brief;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class BriefResource extends Resource
{
    protected static ?string $model = Brief::class;

    // أيقونة القائمة
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // أسماء المورد
    protected static ?string $navigationLabel = 'Briefs';
    protected static ?string $pluralLabel = 'Briefs';
    protected static ?string $modelLabel = 'Brief';

    /**
     * نموذج الإضافة / التعديل
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Brief Name')
                ->required()
                ->maxLength(255),

            Forms\Components\FileUpload::make('path')
                ->label('Upload Brief')
                ->directory('briefs')
                ->preserveFilenames()
                ->required(),
        ]);
    }

    /**
     * جدول العرض
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Brief Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('path')
                    ->label('File Path')
                    ->wrap(),
            ])
            ->actions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Brief $record) => asset('storage/' . $record->path))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->label('Edit'),

                Tables\Actions\DeleteAction::make()
                    ->label('Delete'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Delete Selected'),
            ]);
    }

    /**
     * صفحات المورد
     */
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBriefs::route('/'),
            'create' => Pages\CreateBrief::route('/create'),
            'edit'   => Pages\EditBrief::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'المستندات والتقارير';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
