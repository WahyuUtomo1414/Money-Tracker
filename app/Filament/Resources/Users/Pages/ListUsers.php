<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function mount(): void
    {
        if (auth()->user()?->isPengguna()) {
            $this->redirect(UserResource::getUrl('view', ['record' => auth()->id()]));

            return;
        }

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
