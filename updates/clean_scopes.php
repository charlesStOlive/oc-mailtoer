<?php namespace Waka\Mailtoer\Updates;

//use Excel;
use Seeder;
use Waka\Mailtoer\Models\WakaMailto;

//use System\Models\File;
//use Waka\Worder\Models\BlocType;

// use Waka\Crsm\Classes\CountryImport;

class CleanScopes extends Seeder
{
    public function run()
    {
        //$this->call('Waka\Crsm\Updates\Seeders\SeedWorder');
        WakaMailto::where('scopes', '<>', null)->update(['scopes' => null]);

    }
}