<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\AdminAccount;
use App\Models\Image;
use App\Models\Role;
use App\Models\Type;
use App\Models\UserAccount;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Base extends Controller
{
  /**
   * Base page
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @return Response
   */
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, '@admin/home.twig', [
      'account' => AdminAccount::where('id', $this->auth('id', 'admin'))->first(),
      'admins'  => AdminAccount::get(),
      'users'   => UserAccount::get(),
      'roles'   => Role::get(),
      'images'  => Image::get(),
      'types'   => Type::get()
    ]);
  }
}
