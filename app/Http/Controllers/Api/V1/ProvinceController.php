<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\ProvinceContract;
use App\Http\Controllers\Controller;

class ProvinceController extends Controller
{
    public $province_contract;

    /**
     * @var ProvinceContract
     */
    public function __construct(ProvinceContract $province_contract)
    {
        parent::__construct();
        $this->province_contract = $province_contract;
    }
    /**
     * @OA\Get(
     * path="/api/v1/provinces/",
     * operationId="provinces_list",
     * tags={"provinces"},
     * summary="get list of available provinces",
     * description="get list of available provinces",
     * @OA\Response( response=200, description="Porvinces fetched successfully", @OA\JsonContent() ),
     * @OA\Response( response=404,description="no province found", @OA\JsonContent()),
     * @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *     )
     */
    public function index()
    {

        $provinces = $this->province_contract->fetchAll();

        return $this->api_responser
            ->success()
            ->message('provinces fetched successfully')
            ->payload($provinces)
            ->send();
    }
    /**
     * @OA\Get(
     * path="/api/v1/provinces/find-by-code/{code}",
     * operationId="findByCode",
     * tags={"provinces"},
     * summary="get province using its code",
     * description="get province using its code",
     * @OA\Parameter(  name="code", in="path", description="province code ex=2 ", required=true),
     * @OA\Response( response=200, description="Porvince details fetched successfully", @OA\JsonContent() ),
     * @OA\Response( response=404,description="no province found", @OA\JsonContent()),
     * @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *     )
     */
    public function findByCode($code)
    {

        $province = $this->province_contract->findByCode($code);

        return $this->api_responser
            ->success()
            ->message('province fetched successfully')
            ->payload($province)
            ->send();
    }
}
