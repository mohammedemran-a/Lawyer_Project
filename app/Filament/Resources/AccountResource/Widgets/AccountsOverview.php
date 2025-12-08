<?php

namespace App\Filament\Resources\AccountResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transaction;

class AccountsOverview extends BaseWidget
{
    // تحميل Widget بشكل Lazy لتسريع فتح الصفحة
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        // استخدام Cache لمدة 30 ثانية لتقليل الاستعلامات
        $totalBalance = cache()->remember('total_balance', 30, function() {
            $totalIncome = Transaction::where('txn_type', 'income')->sum('amount');
            $totalExpense = Transaction::where('txn_type', 'expense')->sum('amount');
            return $totalIncome - $totalExpense;
        });

        $monthlyRevenue = cache()->remember('monthly_revenue', 30, function() {
            return Transaction::where('txn_type', 'income')
                ->whereMonth('txn_date', now()->month)
                ->sum('amount');
        });

        $monthlyExpenses = cache()->remember('monthly_expenses', 30, function() {
            return Transaction::where('txn_type', 'expense')
                ->whereMonth('txn_date', now()->month)
                ->sum('amount');
        });

        $netProfit = $monthlyRevenue - $monthlyExpenses;

        return [
            Stat::make('إجمالي الأرصدة', number_format($totalBalance) . ' ريال')
                ->description('مجموع جميع الحسابات')
                ->color('success'),

            Stat::make('الإيرادات الشهرية', number_format($monthlyRevenue) . ' ريال')
                ->description('لهذا الشهر')
                ->color('primary'),

            Stat::make('المصاريف الشهرية', number_format($monthlyExpenses) . ' ريال')
                ->description('لهذا الشهر')
                ->color('danger'),

            Stat::make('صافي الربح', number_format($netProfit) . ' ريال')
                ->description('الإيرادات - المصاريف')
                ->color($netProfit >= 0 ? 'success' : 'danger'),
        ];
    }
}
