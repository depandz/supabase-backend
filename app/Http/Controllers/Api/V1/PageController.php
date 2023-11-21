<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
      /**
       * @OA\Get(
        * path="/api/v1/pages/{lang}/{title}",
        * operationId="getPageByTitle",
        * tags={"pages"},
        * summary="get page details by title ",
        * description="get page details by title  ",
        *      @OA\Parameter( name="lang",in="path",description="the page language(ar,en,fr)",required=true),
        *      @OA\Parameter( name="title",in="path",description="the page title",required=true),
        *      @OA\Response( response=200, description="page fetched succefully", @OA\JsonContent() ),
        *      @OA\Response( response=404, description="no page found with the given title", @OA\JsonContent() ),
        *    )
        */
        public function index($lang,$title){
             try {
                $page = Page::where('title','like',"%$title%")
                            ->orWhere('slug','like',"%$title%")
                            ->whereLanguage($lang)
                            ->firstOrfail();
                
                 return $this->api_responser->success()
                        ->message('Page fetched successfully')
                        ->payload((new PageResource($page))->resolve())
                        ->send();
                }
              catch(\Exception $ex){
                if ($ex instanceof ModelNotFoundException) {
                    return $this->api_responser->failed()->code(404)
                                ->message("no page found with the given title")
                                ->send();
        
                }
                return $this->api_responser->failed()->code(500)
                                    ->message($ex->getMessage())
                                    ->send();
            }
                        
        }
}
