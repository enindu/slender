<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Admin;
use App\Models\Image;
use App\Models\Role;
use App\Models\Section;
use App\Models\User;
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
      'admin'    => Admin::where('id', $this->auth('id', 'admin'))->first(),
      'roles'    => Role::get(),
      'sections' => Section::get(),
      'admins'   => Admin::get(),
      'users'    => User::get(),
      'images'   => Image::get()
    ]);
  }
}
