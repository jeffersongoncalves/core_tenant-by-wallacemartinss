<?php

namespace App\Filament\App\Resources\WhatsappInstanceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\App\Resources\WhatsappInstanceResource;
use App\Services\Evolution\Instance\CreateEvolutionInstanceService;

class CreateWhatsappInstance extends CreateRecord
{
    protected static string $resource = WhatsappInstanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $service = new CreateEvolutionInstanceService();
        $result = $service->createInstance($data);

        // Inclui os dados retornados no array de dados do formulário
        return array_merge($data, $result);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}
