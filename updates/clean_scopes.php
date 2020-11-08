<?php namespace Waka\Mailtoer\Updates;

//use Excel;
use Seeder;
use Waka\Mailtoer\Models\WakaMailto;

class CleanScopes extends Seeder
{
    public function run()
    {
        WakaMailto::where('scopes', '<>', null)->update(['scopes' => null]);

    }
}
