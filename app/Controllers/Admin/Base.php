<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\AdminAccount;
use App\Models\Role;
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
    return $this->view($response, '@admin/home.twig');
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
}
