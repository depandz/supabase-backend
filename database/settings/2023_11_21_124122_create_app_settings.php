<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.app_name', 'Depanini');
        $this->migrator->add('general.app_logo','settings/logo.png');
        $this->migrator->add('general.app_slogon','Save Time');
        $this->migrator->add('general.app_description',' Depanini is an intellectual instant portal relying on internet technology, and Locations services in connecting customers who need transportation services, and road side assistance with qualified service providers');
        $this->migrator->add('general.contact_mail','depaninidz@gmail.com');
        $this->migrator->add('general.customer_service_number','0655555555');
        $this->migrator->add('general.whatsapp_number','+213654555555');
        $this->migrator->add('general.facebook_link','https://www.facebook.com/depanini');
        $this->migrator->add('general.twitter_link','https://www.x.com');
        $this->migrator->add('general.linkedin_link','https://www.linkedin.com');
        $this->migrator->add('general.website_link','https://www.depanini.dz');
        $this->migrator->add('general.youtube_link','https://www.youtube.com');
    }
};
