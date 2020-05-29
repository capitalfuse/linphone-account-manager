<?php

namespace App\Traits;

use App\Logic\Activation\ActivationRepository;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;

trait ActivationTrait
{
    /**
     * Trigger Activation Email
     * Note: this was build pre laravel verification emails.
     *
     * @param  Account $user
     *
     * @return void
     */
    public function initiateEmailActivation(Account $user)
    {
        if (! config('settings.activation') || ! $this->validateEmail($user)) {
            return true;
        }

        $activationRepostory = new ActivationRepository();
        $activationRepostory->createTokenAndSendEmail($user);
    }

    /**
     * Validate the Accounts Email.
     *
     * @param  Account $user
     *
     * @return bool
     */
    protected function validateEmail(Account $user)
    {
        $validator = Validator::make(['email' => $user->email], ['email' => 'required|email']);

        if ($validator->fails()) {
            return false;
        }

        return true;
    }
}
