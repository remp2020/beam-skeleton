<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Remp\BeamModule\Model\Account;
use Remp\BeamModule\Model\Property;

class DemoSeeder extends Seeder
{
    public function run()
    {
        if (Account::count() === 0) {
            $account = new Account();
            $account->name = 'Demo account';
            $account->uuid = '00291988-997e-443c-9b98-666f34148be0';
            $account->save();

            $property = new Property();
            $property->account_id = $account->id;
            $property->name = 'Web';
            $property->uuid = '1d585a37-5503-43c8-8be3-6360012a6541';
            $property->save();
        }
    }
}
