<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Settings\AppSettings;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SettingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
      /**
       * @OA\Get(
        * path="/api/v1/pages/{lang}/{title}",
        * operationId="getPageByTitle",
        * tags={"commun"},
        * summary="get page details by title ",
        * description="get page details by title  ",
        *      @OA\Parameter( name="lang",in="path",description="the page language(ar,en,fr)",required=true),
        *      @OA\Parameter( name="title",in="path",description="the page title",required=true),
        *      @OA\Response( response=200, description="page fetched succefully", @OA\JsonContent() ),
        *      @OA\Response( response=404, description="no page found with the given title", @OA\JsonContent() ),
        *    )
        */
        public function page($lang,$title){
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
      /**
       * @OA\Get(
        * path="/api/v1/app-settings",
        * operationId="get_app_settings",
        * tags={"commun"},
        * summary="get app settings ",
        * description="get app settings  ",
        *      @OA\Response( response=200, description="app settings fetched succefully", @OA\JsonContent() ),
        *      @OA\Response( response=500, description="internal server error", @OA\JsonContent() ),
        *    )
        */
        public function appSettings(AppSettings $settings){
             try {
                
                
                 return $this->api_responser->success()
                        ->message('settings fetched successfully')
                        ->payload(
                            [
                                'app_name'=>$settings->app_name,
                                'app_logo'=>$settings->app_logo,
                                'app_slogon'=>$settings->app_slogon,
                                // 'app_description'=>$settings->app_description,
                                'contact_mail'=>$settings->contact_mail,
                                'customer_service_number'=>$settings->customer_service_number,
                                'whatsapp_number'=>$settings->whatsapp_number,
                                'facebook_link'=>$settings->facebook_link,
                                'twitter_link'=>$settings->twitter_link,
                                'linkedin_link'=>$settings->linkedin_link,
                                'website_link'=>$settings->website_link,
                                'youtube_link'=>$settings->youtube_link,
                            ]
                        )
                        ->send();
                }
              catch(\Exception $ex){
                return $this->api_responser->failed()->code(500)
                                    ->message($ex->getMessage())
                                    ->send();
            }
                        
        }
}
