<?php

namespace App\Observers;

use App\Models\Hearing;

class HearingObserver
{
    public function deleted(Hearing $hearing): void
    {
        // إعادة ترقيم الجلسات الخاصة بنفس القضية
        $hearings = Hearing::where('case_id', $hearing->case_id)
            ->orderBy('hearing_datetime')
            ->orderBy('id')
            ->get();

        foreach ($hearings as $index => $item) {
            $item->updateQuietly([
                'conter' => $index + 1,
            ]);
        }
    }
}
