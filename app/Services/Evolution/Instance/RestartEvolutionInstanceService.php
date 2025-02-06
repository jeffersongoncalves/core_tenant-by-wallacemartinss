<?php

namespace App\Services\Evolution\Instance;

use App\Services\Traits\EvolutionClientTrait;
use Exception;

class RestartEvolutionInstanceService
{
    use EvolutionClientTrait;
    public function restartInstance(string $instanceId)
    {
        try {
            $response = $this->makeRequest("/instance/{$instanceId}/restart", 'POST');

            if (isset($response['error'])) {
                throw new Exception($response['error']);
            }

            // Após o restart, chamar o serviço de conexão para gerar o QR Code
            $connectService = new ConnectEvolutionInstanceService();

            return $connectService->connectInstance($instanceId);

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
