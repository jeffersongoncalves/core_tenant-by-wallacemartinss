<?php

namespace App\Services\Evolution\Instance;

use App\Services\Traits\EvolutionClientTrait;

namespace App\Services\Evolution\Instance;

use App\Services\Traits\EvolutionClientTrait;
use Exception;

class DeleteEvolutionInstanceService
{
    use EvolutionClientTrait;

    public function deleteInstance(string $instanceID)
    {

            $response = $this->makeRequest("/instance/delete/{$instanceID}", 'DELETE');



            return $response;
            }
}
