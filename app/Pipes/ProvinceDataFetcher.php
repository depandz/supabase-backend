<?php

namespace App\Pipes;

use App\Contracts\FeesContract;
use Closure;
use App\Services\Locale\Province as ProvinceRepository;
use Exception;

class ProvinceDataFetcher
{


    public $province_repository;
    public $fees_contract;

    /**
     * @var ProvinceRepository
     */
    public function __construct(ProvinceRepository $province_repository,FeesContract $fees_contract)
    {
        $this->province_repository = $province_repository;
        $this->fees_contract = $fees_contract;
    }

    public function handle(array $data, Closure $next)
    {
        try {
            $province = $this->province_repository->findByCode($data['current_province_id']);
            $province_fees =$this->fees_contract->findBy('province_id',$data['current_province_id']);
            $data['province'] =$province;
            $data['province_fees'] =$province_fees;
            return $next($data);
        }
        catch(Exception $ex){
            handleTwoCommunErrors($ex);
        }
    }
}