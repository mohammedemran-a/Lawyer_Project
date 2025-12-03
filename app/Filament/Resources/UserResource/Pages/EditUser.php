<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
          Actions\DeleteAction::make()
    ->hidden(function ($record) {
        $user = auth()->user();

        if ($record->id === $user->id && $user->hasRole('super_admin')) {
            return true;
        }

        return false;
    }),

        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}


