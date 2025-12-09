<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixArticlesCollationForAccents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change de latin1_general_ci vers utf8mb4_unicode_ci (ignore accents ET supporte tous les caractères)
        DB::statement('ALTER TABLE articles MODIFY title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('ALTER TABLE articles MODIFY content TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Retour à l'ancien état si besoin
        DB::statement('ALTER TABLE articles MODIFY title VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_ci');
        DB::statement('ALTER TABLE articles MODIFY content TEXT CHARACTER SET latin1 COLLATE latin1_general_ci');
    }
};
