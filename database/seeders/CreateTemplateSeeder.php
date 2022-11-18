<?php

namespace Database\Seeders;
use App\Models\Template;
use Illuminate\Database\Seeder;

class CreateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $welcomeTemplate = file_get_contents(public_path('email-templates/welcome_emailer.php'));
        $referenceTemplate = file_get_contents(public_path('email-templates/reference_emailer.php'));
        Template::create(['name' => 'Welcome Emailer','content' => $welcomeTemplate]);
        Template::create(['name' => 'Reference Emailer','content' => $referenceTemplate]);
    }
}
