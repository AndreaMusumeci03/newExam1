<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up()
    {
        // Disabilita foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Svuota la tabella
        DB::table('recommended_films')->truncate();
        
        // Riabilita foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Rimuovi la cache
        Cache::forget('films_last_update');
    }

    public function down()
    {
        // Non fare nulla nel rollback
    }
};