<?php namespace Edata\ProductImporter\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateEdataProductimporterFiles extends Migration
{
    public function up()
    {
        Schema::create('edata_productimporter_files', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->text('org_file_name');
            $table->string('file_name', 50);
            $table->string('user_name', 190);
            $table->string('status', 50)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('edata_productimporter_files');
    }
}
