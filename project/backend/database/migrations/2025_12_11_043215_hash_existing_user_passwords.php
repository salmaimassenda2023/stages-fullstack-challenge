<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up()
    {
        // Récupère tous les users
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            // Vérifie si le mot de passe n'est pas déjà hashé
            // Les mots de passe hashés commencent par $2y$ (bcrypt)
            if (!str_starts_with($user->password, '$2y$')) {
                // Hash le mot de passe en clair
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'password' => Hash::make($user->password)
                    ]);
            }
        }
    }

    public function down()
    {
        // Impossible de revenir en arrière (on ne peut pas "dé-hasher")
        // On pourrait reset tous les mots de passe, mais ce n'est pas recommandé
    }
};
