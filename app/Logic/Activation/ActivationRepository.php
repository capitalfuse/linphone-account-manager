<?php

namespace App\Logic\Activation;

use App\Models\Activation;
use App\Models\Account;
use App\Notifications\SendActivationEmail;
use App\Traits\CaptureIpTrait;
use Carbon\Carbon;

class ActivationRepository
{
    /**
     * Creates a token and send email.
     *
     * @param \App\Models\Account $user
     *
     * @return bool or void
     */
    public function createTokenAndSendEmail(Account $user)
    {
        $activations = Activation::where('account_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subHours(config('settings.timePeriod')))
            ->count();

        if ($activations >= config('settings.maxAttempts')) {
            return true;
        }

        //if user changed activated email to new one
        if ($user->activated) {
            $user->update([
                'activated' => false,
            ]);
        }

        // Create new Activation record for this user
        $activation = self::createNewActivationToken($user);

        // Send activation email notification
        self::sendNewActivationEmail($user, $activation->token);
    }

    /**
     * Creates a new activation token.
     *
     * @param \App\Models\Account $user
     *
     * @return \App\Models\Activation $activation
     */
    public function createNewActivationToken(Account $user)
    {
        $ipAddress = new CaptureIpTrait();
        $activation = new Activation();
        $activation->account_id = $user->id;
        $activation->token = str_random(64);
        $activation->ip_address = $ipAddress->getClientIp();
        $activation->save();

        return $activation;
    }

    /**
     * Sends a new activation email.
     *
     * @param \App\Models\Account $user  The user
     * @param string           $token The token
     */
    public function sendNewActivationEmail(Account $user, $token)
    {
        $user->notify(new SendActivationEmail($token));
    }

    /**
     * Method to removed expired activations.
     *
     * @return void
     */
    public function deleteExpiredActivations()
    {
        Activation::where('created_at', '<=', Carbon::now()->subHours(72))->delete();
    }
}
