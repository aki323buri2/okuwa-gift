<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog', function (Blueprint $table) {
            // $table->increments('id');
            $catno  = $table->string ('catno' )     ->comment('カタログＣＤ');
            $shcds  = $table->string ('shcds' )     ->comment('ｼｮｸﾘｭｰＣＤ');
            $eoscd  = $table->string ('eoscd' )     ->comment('ＥＯＳＣＤ');
            $mekame = $table->string ('mekame')     ->comment('メーカー名');
            $shiren = $table->string ('shiren')     ->comment('仕入先ＣＤ');
            $hinmei = $table->string ('hinmei')     ->comment('品名');
            $sanchi = $table->string ('sanchi')     ->comment('産地');
            $tenyou = $table->string ('tenyou')     ->comment('天・養');
            $nouka  = $table->decimal('nouka' ,10,2)->comment('納価');
            $baika  = $table->decimal('baika' ,10,2)->comment('売価');
            $stanka = $table->decimal('stanka',10,2)->comment('仕入');
            $table->timestamps();

            $catno->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('catalog');
    }
}
