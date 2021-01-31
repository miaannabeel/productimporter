<?php namespace Edata\ProductImporter\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateLovataReviewsShopaholicReviews extends Migration
{
    public function up()
    {
        Schema::table('lovata_reviews_shopaholic_reviews', function ($table) {
            $table->string('external_id')->nullable();
            $table->index('external_id');
        });
    }
    
    public function down()
    {
        Schema::table('lovata_reviews_shopaholic_reviews', function ($table) {
            
        });
    }
}
