<?php

namespace App\Service;

use App\Entity\Server;
use App\Repository\ServerRepository;

class ServerService
{
    /**
     * @var ServerRepository
     */
    private $serverRepository;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    /**
     * @return Server
     * @throws \Exception
     */
    public function selectServerForNewCustomer(): Server
    {
        $servers = $this->serverRepository->getServersForRegistration();

        return $servers[array_rand($servers)];
    }
}
