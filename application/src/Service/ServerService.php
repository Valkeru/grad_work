<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 16:28
 */

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
