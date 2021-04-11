<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\addUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddController extends AbstractController
{
    /**
     * @Route(
     * name="addUser",
     * path="api/admin/users",
     * methods={"POST"},
     * defaults={
     * "_controller"="\App\Controller\AddController::postadd",
     * "_api_resource_class"=User::class,
     * "_api_collection_operation_name"="post_user"
     * }
     * )
     */
    public function addUser(addUser $adduser, Request $request)
    {
        //dd("ok");
        $adduser->addUser($request);
        return $this->json('success',Response::HTTP_CREATED);

        
    }

    /**
     * @Route(
     * name="put_user",
     * path="/api/admin/users",
     * methods={"PUT"},
     * defaults={
     * "_controller"="\App\Controller\UserController::putuser",
     * "_api_resource_class"=User::class,
     * "_api_collection_operation_name"="put_user"
     * }
     * )
     */
    public function put(AddServices $addservices)
    {
        $addservices->addUse($request);
    }
}
