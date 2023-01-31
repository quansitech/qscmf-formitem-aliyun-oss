<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMimeTypeLengthForFilcPic extends Migration
{

    public function beforeCmmUp()
    {
        //
    }

    public function beforeCmmDown()
    {
        //
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qs_file_pic', function (Blueprint $table) {
            $columns = DB::select('show columns from qs_file_pic');

            $count = collect($columns)->filter(function ($column) {
                return $column->Field == 'mime_type';
            })->count();

            if(!!$count){
                $table->string('mime_type', 200)->default('')->change();
            }
            else{
                $table->string("mime_type", 200)->default("")->after("cate");
            }


        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qs_file_pic', function (Blueprint $table) {
            $table->string('mime_type', 50)->default('')->change();
        });
    }

    public function afterCmmUp()
    {
        //
    }

    public function afterCmmDown()
    {
        //
    }
}
