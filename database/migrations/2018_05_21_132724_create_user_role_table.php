<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

use App\Models\Physical\Repository\UserRole;
use App\Models\Physical\Repository\User;
use App\Models\Physical\Repository\Role;

use App\Component\Base\Database\PostgresMigration;


class CreateUserRoleTable extends PostgresMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(UserRole::TABLE_NAME, function (Blueprint $table) {
            $this->createdAt($table);

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');

            $table->foreign('user_id')
                ->references('id')->on(User::TABLE_NAME)
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('role_id')
                ->references('id')->on(Role::TABLE_NAME)
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->primary(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(UserRole::TABLE_NAME);
    }
}
