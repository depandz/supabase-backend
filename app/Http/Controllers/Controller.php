<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="Depanini Api",
 *   @OA\License(name="MIT"),
 *   @OA\Attachable()
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $api_responser;

    public function __construct()
    {
        $this->api_responser = new ApiResponser();
    }
}

