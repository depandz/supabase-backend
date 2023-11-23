<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompletePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::whereNotNull('title')->update([
            'title_fr'=>'depanini',
            'title_ar'=>'ديبانيني',
            'sub_title_fr'=>'à propos de nous ',
            'sub_title_ar'=>'نبذة عنا',
            'content_fr'=>'<p class="text-white sm:text-xl sm:leading-relaxed">
            Depanini is an intellectual instant portal relying on internet technology, and Locations services in connecting customers who need transportation services, and road side assistance with qualified service providers 
            </p>
            <p class="text-white sm:text-xl sm:leading-relaxed">
            Depanini is an intellectual instant portal relying on internet technology, and Locations services in connecting customers who need transportation services, and road side assistance with qualified service providers 
            </p>
          ',
            'content_ar'=>'<p class="text-white sm:text-xl sm:leading-relaxed">
            الهدف هو تقليل التدخل الآدمي وأتمتة جميع إجراءات الخدمات اللوجستية والمساندة على الطرق. يستطيع ديبانيني اكمال جميع مراحل طلب الخدمة، ابتداء من إنشاء الطلب مرور
            </p>
            <p class="text-white sm:text-xl sm:leading-relaxed">
            الهدف هو تقليل التدخل الآدمي وأتمتة جميع إجراءات الخدمات اللوجستية والمساندة على الطرق. يستطيع ديبانيني اكمال جميع مراحل طلب الخدمة، ابتداء من إنشاء الطلب مرور
            </p>
          ',
        ]);
    }
}
