<?php

namespace App\Listeners;

use Auth;
use Session;

use Carbon\Carbon;

use App\Repositories\AccountRepository;

use App\Events\UserLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\DB;

class HandleUserLoggedIn
{

    /**
     * @var AccountRepository
     */
    protected $accountRepo;

    /**
     * Create the event listener.
     */
    public function __construct(AccountRepository $accountRepo)
    {
        $this->accountRepo = $accountRepo;
    }

    /**
     * Handle the event.
     */
    public function handle(UserLoggedIn $event): void
    {
        /* $account = Auth::user()->account;

        $account->last_login = Carbon::now()->toDateTimeString();
        $account->save(); */

        $users = $this->accountRepo->loadAccounts(Auth::user()->id);
        Session::put(SESSION_USER_ACCOUNTS, $users);

        $userId = Session::get('real_userid');

        $codes = DB::connection('main')
            ->table('users')
            ->join('user_permissions', 'users.role_id', '=', 'user_permissions.role_id')
            ->join('user_resources', 'user_permissions.resource_id', '=', 'user_resources.id')
            ->where('users.id', $userId)
            ->pluck('user_resources.code')
            ->toArray();

        session(['user_codes' => $codes]);
    }
}
