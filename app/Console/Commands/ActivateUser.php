<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ActivateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:activate {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate a user account by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return;
        }

        if ($user->email_verified_at) {
            $this->info("User {$user->name} ({$email}) is already activated.");
            return;
        }

        $user->email_verified_at = now();
        $user->save();

        $this->info("User {$user->name} ({$email}) has been successfully activated.");
    }
}
