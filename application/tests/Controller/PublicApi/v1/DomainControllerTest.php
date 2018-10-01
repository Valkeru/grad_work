<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 01.10.18
 * Time: 17:10
 */

namespace App\Tests\Controller\PublicApi\v1;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DomainControllerTest
 *
 * @package App\Tests\Controller\PublicApi\v1
 */
class DomainControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * @var \Lcobucci\JWT\Token
     */
    private $token;

    /**
     * DomainControllerTest constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = static::createClient([], [
            'HTTP_HOST' => 'public.api.local'
        ]);

        $container = static::bootKernel()->getContainer();
        $key = new Key('file://' . $container->getParameter('app.public.private_key'));

        $now        = new \DateTime();
        $builder    = (new Builder())
            ->setIssuedAt($now->getTimestamp())
            ->set('userId', 1)
            ->set('userName', 'test')
            ->set('uuid', (string)Uuid::uuid4());

        $builder->setExpiration($now->add(new \DateInterval('P1W'))->getTimestamp());
        $this->token = $builder->sign(new Sha256(), $key)->getToken();
    }

    /**
     *
     */
    public function testActionList()
    {
        $this->client->request(Request::METHOD_GET, '/v1');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
}
