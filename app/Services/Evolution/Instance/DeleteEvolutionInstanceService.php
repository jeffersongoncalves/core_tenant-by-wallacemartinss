<?php

namespace App\Services\Evolution\Instance;

namespace App\Services\Evolution\Instance;

class DeleteEvolutionInstanceService
{
    use EvolutionClientTrait;

    public function deleteInstance(string $instanceID)
    {

        $response = $this->makeRequest("/instance/delete/{$instanceID}", 'DELETE');

        return $response;
    }
}
