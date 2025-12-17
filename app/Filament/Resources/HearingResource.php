<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HearingResource\Pages;
use App\Models\Hearing;
use App\Models\Legalcase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;

class HearingResource extends Resource
{
    protected static ?string $model = Hearing::class;

    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'الجلسات';
    protected static ?string $pluralLabel     = 'الجلسات';
    protected static ?string $label           = 'جلسة';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['case', 'court', 'lawyers', 'client']);

        $user = auth()->user();

        if ($user->can('view_any_hearing')) {
            return $query;
        }

        if ($user->can('view_hearing') && $user->lawyer_id) {
            return $query->whereHas('lawyers', function ($q) use ($user) {
                $q->where('lawyer_id', $user->lawyer_id);
            });
        }

        if ($user->can('view_hearing') && $user->client_id) {
            return $query->where('client_id', $user->client_id);
        }

        return $query->whereRaw('1=0');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Select::make('case_id')
                ->label('القضية')
                ->relationship('case', 'case_number')
                ->getOptionLabelFromRecordUsing(fn ($record) =>
                    "{$record->case_number} - {$record->title}"
                )
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $case = Legalcase::find($state);

                    if ($case) {
                        $set('court_id', $case->court_id);
                        $set('client_id', $case->client_id);

                        $count = Hearing::where('case_id', $case->id)->count();
                        $set('conter', $count + 1);
                    }
                }),

            Forms\Components\Select::make('court_id')
                ->label('المحكمة')
                ->relationship('court', 'name')
                ->searchable()
                ->required(),

            Forms\Components\Select::make('lawyers')
                ->label('المحامون')
                ->relationship('lawyers', 'name')
                ->multiple()
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Select::make('client_id')
                ->label('العميل')
                ->relationship('client', 'name')
                ->searchable(),

            Forms\Components\DateTimePicker::make('hearing_datetime')
                ->label('تاريخ ووقت الجلسة')
                ->required()
                ->rule(function ($get, $record) {
                    return function ($attribute, $value, $fail) use ($get, $record) {
                        $caseId = $get('case_id');
                        $date = Carbon::parse($value)->format('Y-m-d');

                        $exists = Hearing::where('case_id', $caseId)
                            ->whereDate('hearing_datetime', $date)
                            ->when($record?->id, fn ($q) => $q->where('id', '!=', $record->id))
                            ->exists();

                        if ($exists) {
                            $fail('يوجد جلسة مسجلة لهذه القضية في نفس اليوم.');
                        }
                    };
                }),

            Forms\Components\TextInput::make('topic')
                ->label('موضوع الجلسة')
                ->maxLength(200),

            Forms\Components\Textarea::make('decision')
                ->label('القرار'),

            Forms\Components\Textarea::make('required_action')
                ->label('الإجراء المطلوب'),

            Forms\Components\DatePicker::make('postponed_to')
                ->label('تأجيل إلى'),

            Forms\Components\TextInput::make('conter')
                ->label('رقم الجلسة')
                ->numeric()
                ->disabled()
                ->dehydrated(),

            Forms\Components\Textarea::make('notes')
                ->label('ملاحظات'),

            Forms\Components\Select::make('session_type')
                ->label('نوع الجلسة')
                ->options([
                    'urgent'    => 'مستعجلة',
                    'normal'    => 'عادية',
                    'follow-up' => 'متابعة',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('case.case_number')
                    ->label('القضية')
                    ->formatStateUsing(fn ($state, $record) =>
                        "{$record->case->case_number} - {$record->case->title}"
                    )
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('court.name')
                    ->label('المحكمة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('lawyers.name')
                    ->label('المحامون')
                    ->badge()
                    ->separator(' ، '),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('العميل'),

                Tables\Columns\TextColumn::make('hearing_datetime')
                    ->label('تاريخ الجلسة')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('topic')
                    ->label('الموضوع')
                    ->limit(30),

                Tables\Columns\TextColumn::make('decision')
                    ->label('القرار')
                    ->limit(30),

                Tables\Columns\TextColumn::make('postponed_to')
                    ->label('مؤجلة إلى')
                    ->date(),

                Tables\Columns\TextColumn::make('conter')
                    ->label('رقم الجلسة')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('postponed')
                    ->label('جلسات مؤجلة')
                    ->query(fn ($q) => $q->whereNotNull('postponed_to')),
            ])
            ->headerActions([
                ExportAction::make()->label('تصدير'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHearings::route('/'),
            'create' => Pages\CreateHearing::route('/create'),
            'edit'   => Pages\EditHearing::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الإدارة القانونية والقضايا';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
