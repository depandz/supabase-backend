<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::create([
            'title'=>'About Us',
            'sub_title'=>'About',
            'slug'=>'about-us',
            'content'=>'<p class="text-white sm:text-xl sm:leading-relaxed">
            Depanini is an intellectual instant portal relying on internet technology, and Locations services in connecting customers who need transportation services, and road side assistance with qualified service providers 
            </p>
            <p class="text-white sm:text-xl sm:leading-relaxed">
            Depanini is an intellectual instant portal relying on internet technology, and Locations services in connecting customers who need transportation services, and road side assistance with qualified service providers 
            </p>
          ',
            'is_publishable'=>true,
        ]);
        //about us page
        Page::create([
            'title'=>'Privacy Pollicy',
            'sub_title'=>'privacy',
            'slug'=>'privacy-policy',
            'content'=>'<p class="text-white sm:text-xl sm:leading-relaxed">
            Morni is an intellectual instant portal relying on internet technology, and Locations services in connecting customers who need transportation services, and road side assistance with qualified service providers 
            </p>
            <p class="text-white sm:text-xl sm:leading-relaxed">
            Morni is an intellectual instant portal relying on internet technology, and Locations services in connecting customers who need transportation services, and road side assistance with qualified service providers 
            </p>
          ',
            'is_publishable'=>true,
        ]);
        //terms page
        Page::create([
            'title'=>'terms and conditions',
            'sub_title'=>'terms and conditions',
            'slug'=>'terms',
            'content'=>'<p class="text-white sm:text-xl sm:leading-relaxed">
            Morni is an intellectual instant portal relying on internet technology, and Locations services in connecting customers who need transportation services, and road side assistance with qualified service providers 
            </p>
            <p class="text-white sm:text-xl sm:leading-relaxed">
            Morni is an intellectual instant portal relying on internet technology, and Locations services in connecting customers who need transportation services, and road side assistance with qualified service providers 
            </p>
          ',
            'is_publishable'=>true,
        ]);
    }
}
