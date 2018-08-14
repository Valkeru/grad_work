<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 14.08.18
 * Time: 21:02
 */

namespace App\Controller\PublicApi\Base;

use App\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BasePrivateController extends Controller
{
    protected function getUser()
    {
        /** @var Employee|null $user */
        $user = parent::getUser();

        if ($user !== NULL) {
            return $user;
        }

        $storage = $this->get('security.token_storage');

        return $storage->getToken()->getUser() ?? '';
    }
}
