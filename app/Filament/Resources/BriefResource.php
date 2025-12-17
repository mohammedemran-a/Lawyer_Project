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
    protected static ?string $navigationLabel = 'المذكرات';
    protected static ?string $pluralLabel = 'المذكرات';
    protected static ?string $modelLabel = 'مذكرة';

    /**
     * نموذج الإضافة / التعديل
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('اسم المذكرة')
                ->required()
                ->maxLength(255),

            Forms\Components\FileUpload::make('path')
                ->label('رفع المذكرة')
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
                    ->label('اسم المذكرة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('path')
                    ->label('مسار الملف')
                    ->wrap(),
            ])
            ->actions([
                Action::make('download')
                    ->label('تحميل')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Brief $record) => asset('storage/' . $record->path))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->label('تعديل'),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('حذف المحدد'),
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

    /**
     * مجموعة القائمة
     */
    public static function getNavigationGroup(): ?string
    {
        return 'المستندات والتقارير';
    }

    /**
     * ترتيب المورد في القائمة
     */
    public static function getNavigationSort(): ?int
    {
        return 1;
    }
}
