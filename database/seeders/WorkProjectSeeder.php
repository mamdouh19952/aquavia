<?php

namespace Database\Seeders;

use App\Models\WorkProject;
use Illuminate\Database\Seeder;

class WorkProjectSeeder extends Seeder
{
    public function run(): void
    {
        $imagesPath = storage_path('app/seed-images');

        $projects = [
            [
                'title_ar' => 'حمام سباحة فيلا الشيخ زايد',
                'title_en' => 'Sheikh Zayed Villa Pool',
                'description_ar' => 'حمام سباحة فاخر بتصميم عصري لفيلا خاصة في الشيخ زايد، بمساحة استرخاء ومنطقة جلوس متكاملة حوله.',
                'description_en' => 'A luxury, modern-design swimming pool for a private villa in Sheikh Zayed, with an integrated lounge and seating area around it.',
                'location' => 'Sheikh Zayed, Egypt',
                'images' => ['img-11.jpg', 'img-12.jpg', 'img-13.jpg'],
            ],
            [
                'title_ar' => 'حمام سباحة كمبوند القاهرة الجديدة',
                'title_en' => 'New Cairo Compound Pool',
                'description_ar' => 'تنفيذ حمام سباحة مشترك لكمبوند سكني في القاهرة الجديدة مع إضاءة ليلية وإطلالة بانورامية.',
                'description_en' => 'A shared swimming pool built for a residential compound in New Cairo, featuring night lighting and a panoramic view.',
                'location' => 'New Cairo, Egypt',
                'images' => ['img-14.jpg', 'img-15.jpg'],
            ],
            [
                'title_ar' => 'حمام سباحة منتجع الساحل الشمالي',
                'title_en' => 'North Coast Resort Pool',
                'description_ar' => 'حمام سباحة بتصميم استوائي لمنتجع سياحي على الساحل الشمالي، مزود بنظام تنقية متطور وصيانة دورية.',
                'description_en' => 'A tropical-design swimming pool for a resort on the North Coast, equipped with an advanced filtration system and regular maintenance.',
                'location' => 'North Coast, Egypt',
                'images' => ['img-16.jpg', 'img-17.jpg', 'img-18.jpg'],
            ],
            [
                'title_ar' => 'حمام سباحة حديقة المعادي',
                'title_en' => 'Maadi Garden Pool',
                'description_ar' => 'حمام سباحة صغير أنيق داخل حديقة منزل عائلي في المعادي، مصمم ليتناسب مع المساحة المتاحة.',
                'description_en' => 'A small, elegant swimming pool set within a family home garden in Maadi, designed to fit the available space perfectly.',
                'location' => 'Maadi, Cairo',
                'images' => ['img-19.jpg', 'img-20.jpg'],
            ],
        ];

        foreach ($projects as $data) {
            $images = $data['images'];
            unset($data['images']);

            $project = WorkProject::updateOrCreate(
                ['title_en' => $data['title_en']],
                $data
            );

            if ($project->galleryImages()->isEmpty()) {
                foreach ($images as $image) {
                    $project->addMedia($imagesPath.DIRECTORY_SEPARATOR.$image)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery');
                }
            }
        }
    }
}
