<?php

namespace App\Observers;

use Remp\BeamModule\Model\Account;

class AccountObserver
{
    public function created(Account $account): void
    {
        // handle the Account "created" event
    }

    public function updated(Account $account): void
    {
        // handle the Account "updated" event
    }

    public function deleted(Account $account): void
    {
        // handle the Account "deleted" event
    }

    public function forceDeleted(Account $account): void
    {
        // handle the Account "forceDeleted" event
    }
}
