<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Legalcase;
use App\Models\Client;
use App\Models\Hearing;
use Carbon\Carbon;

class CasesStats extends BaseWidget
{
    // تشغيل التحميل البطيء لزيادة سرعة فتح لوحة التحكم
    protected static bool $isLazy = true;

    protected ?string $heading = 'الإحصائيات';

    protected function getStats(): array
    {
        // استعلام واحد يجمع كل إحصائيات القضايا
        $casesStats = Legalcase::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_cases,
            SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_cases,
            SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_cases
        ")->first();

        // جلسات اليوم – استعلام سريع
        $todayHearings = Hearing::whereDate('hearing_datetime', today())->count();

        // العملاء النشطون – استعلام سريع
        $activeClients = Client::whereNull('end_at')->count();

        return [
            Stat::make('إجمالي القضايا', $casesStats->total)
                ->description('مجموع القضايا')
                ->color('primary')
                ->icon('heroicon-o-clipboard-document'),

            Stat::make('القضايا المفتوحة', $casesStats->open_cases)
                ->description('القضايا النشطة حالياً')
                ->color('success')
                ->icon('heroicon-o-scale'),

            Stat::make('القضايا المغلقة', $casesStats->closed_cases)
                ->description('تم الانتهاء منها')
                ->color('danger')
                ->icon('heroicon-o-check-circle'),

            Stat::make('قضايا اليوم', $casesStats->today_cases)
                ->description('عدد القضايا المسجلة اليوم')
                ->color('secondary')
                ->icon('heroicon-o-document-text'),

            Stat::make('جلسات اليوم', $todayHearings)
                ->description('عدد الجلسات المجدولة اليوم')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('العملاء النشطون', $activeClients)
                ->description('عملاء لديهم عقود سارية')
                ->color('info')
                ->icon('heroicon-o-user-group'),
        ];
    }
}
