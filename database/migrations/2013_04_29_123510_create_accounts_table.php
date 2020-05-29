<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 64)->unique();
            $table->string('domain', 64);
            $table->string('email', 64)->unique()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->boolean('activated')->default(false);
            $table->string('token');
            $table->ipAddress('signup_ip_address')->nullable();
            $table->ipAddress('signup_confirmation_ip_address')->nullable();
            $table->ipAddress('signup_sm_ip_address')->nullable();
            $table->ipAddress('admin_ip_address')->nullable();
            $table->ipAddress('updated_ip_address')->nullable();
            $table->ipAddress('deleted_ip_address')->nullable();
            $table->string('confirmation_key', 14)->nullable();
            $table->string('ip_address', 39)->nullable();;
            $table->string('user_agent', 256)->nullable();;
            $table->datetime('creation_time')->nullable();;
            $table->datetime('expire_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('passwords', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id')->unsigned()->index();
            $table->string('password', 255);
            $table->string('algorithm', 10)->default('SHA-256');
            $table->timestamps();

            //Relationships
            //$table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('passwords');
    }
}
