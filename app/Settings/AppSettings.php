<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public string $app_name;
    public string $app_logo;
    public string $app_slogon;
    public string $app_description;
    public string $contact_mail;
    public string $customer_service_number;
    public string $whatsapp_number;
    public string $facebook_link;
    public string $twitter_link;
    public string $linkedin_link;
    public string $website_link;
    public string $youtube_link;
    
    public static function group(): string
    {
        return 'general';
    }
}