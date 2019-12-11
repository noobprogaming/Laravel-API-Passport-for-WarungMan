<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarungmanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);

        Schema::create('item', function (Blueprint $table) {
            $table->bigIncrements('item_id');
            $table->string('name', 255); 
            $table->string('description', 255);
            $table->integer('stock');
            $table->bigInteger('purchasing_price');
            $table->bigInteger('selling_price');
            $table->integer('category_id')->unsigned(); //foreignKey
            $table->bigInteger('id')->unsigned(); //foreignKey
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::create('category', function (Blueprint $table) {
            $table->increments('category_id');
            $table->string('explanation', 255);
            $table->engine = 'InnoDB';
        });

        Schema::create('purchase_item', function (Blueprint $table) {
            $table->integer('amount');
            $table->bigInteger('purchasing_price');
            $table->bigInteger('selling_price');
            $table->bigInteger('purchase_id')->unsigned(); //foreignKey
            $table->bigInteger('item_id')->unsigned(); //foreignKey
            $table->bigInteger('seller_id')->unsigned(); //foreignKey
            $table->bigInteger('buyer_id')->unsigned(); //foreignKey
            $table->engine = 'InnoDB';
        });

        Schema::create('purchase', function (Blueprint $table) {
            $table->bigIncrements('purchase_id');
            $table->bigInteger('total_price')->nullable();
            $table->string('note')->nullable();
            $table->bigInteger('seller_id')->unsigned(); //foreignKey
            $table->bigInteger('buyer_id')->unsigned(); //foreignKey
            $table->timestamp('time')->nullable();
            $table->engine = 'InnoDB';
        });

        Schema::create('rating', function (Blueprint $table) {
            $table->integer('rating');
            $table->string('review', 255);
            $table->bigInteger('item_id')->unsigned(); //foreignKey
            $table->bigInteger('id')->unsigned(); //foreignKey
            $table->timestamp('time')->useCurrent();
            $table->engine = 'InnoDB';
        });

        DB::statement('ALTER TABLE item ADD CONSTRAINT fk_itemCategory_id FOREIGN KEY (category_id) REFERENCES category(category_id);');
        DB::statement('ALTER TABLE item ADD CONSTRAINT fk_itemUsers_id FOREIGN KEY (id) REFERENCES users(id);');

        DB::statement('ALTER TABLE purchase_item ADD CONSTRAINT fk_piPurchase_id FOREIGN KEY (purchase_id) REFERENCES purchase(purchase_id);');
        DB::statement('ALTER TABLE purchase_item ADD CONSTRAINT fk_piItem_id FOREIGN KEY (item_id) REFERENCES item(item_id);');
        DB::statement('ALTER TABLE purchase_item ADD CONSTRAINT fk_piSeller_id FOREIGN KEY (seller_id) REFERENCES users(id);');
        DB::statement('ALTER TABLE purchase_item ADD CONSTRAINT fk_piBuyer_id FOREIGN KEY (buyer_id) REFERENCES users(id);');

        DB::statement('ALTER TABLE purchase ADD CONSTRAINT fk_purchaseSeller_id FOREIGN KEY (seller_id) REFERENCES users(id);');
        DB::statement('ALTER TABLE purchase ADD CONSTRAINT fk_purchaseBuyer_id FOREIGN KEY (buyer_id) REFERENCES users(id);');
    
        DB::statement('ALTER TABLE rating ADD CONSTRAINT fk_ratingItem_id FOREIGN KEY (item_id) REFERENCES item(item_id);');
        DB::statement('ALTER TABLE rating ADD CONSTRAINT fk_ratingUsers_id FOREIGN KEY (id) REFERENCES users(id);');
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('purchase_item');
        Schema::dropIfExists('purchase');
        Schema::dropIfExists('rating');
    }
}
