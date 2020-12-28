<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\AdminAccount;
use App\Models\Role;
use App\Models\UserAccount;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Base extends Controller
{
  /**
   * Homepage
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @return Response
   */
  public function home(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, '@admin/home.twig', [
      'account' => AdminAccount::where('id', $this->auth('id', 'admin'))->first(),
      'admins'  => AdminAccount::get(),
      'users'   => UserAccount::get(),
      'roles'   => Role::get()
    ]);
  }

  /**
   * Admins page
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @return Response
   */
  public function admins(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, '@admin/admins.twig', [
      'roles'  => Role::get(),
      'admins' => AdminAccount::orderBy('id', 'desc')->get()
    ]);
  }

  /**
   * Users page
   * 
   * @param Request  $request
   * @param Response $response
   * @param array    $data
   * 
   * @return Response
   */
  public function users(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, '@admin/users.twig', [
      'roles' => Role::get(),
      'users' => UserAccount::orderBy('id', 'desc')->get()
    ]);
  }
}
