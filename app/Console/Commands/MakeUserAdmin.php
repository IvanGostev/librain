<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:make {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant admin privileges to a user by email';

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

        $user->role = 'admin';
        $user->save();

        $this->info("User {$user->name} ({$email}) has been granted admin privileges.");
    }
}
