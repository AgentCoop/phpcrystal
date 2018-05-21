<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

use App\Component\Base\Database\PostgresMigration;

use App\Models\Physical\Repository\Role;

class CreateRolesTable extends PostgresMigration
{
    const ROLE_TYPE = 'rbac_role';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Role::TABLE_NAME, function (Blueprint $table) {
            $table->increments(Role::COL_ID);
            $table->timestamps();
        });

        $this->enum(Role::TABLE_NAME, Role::COL_NAME, self::ROLE_TYPE, Role::ROLE_NAMES, true);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Role::TABLE_NAME);

        DB::transaction(function () {
            DB::statement(sprintf('DROP TYPE %s;', self::ROLE_TYPE));
        });
    }
}
