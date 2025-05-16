<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Pterodactyl\BlueprintFramework\Libraries\ExtensionLibrary\Admin\BlueprintAdminLibrary as BlueprintExtensionLibrary;

return new class extends Migration
{
    public function up(): void
    {
        $blueprint = app(BlueprintExtensionLibrary::class);

        $blueprint->dbSetMany("{identifier}", [
            'port' => 22,
            'useSSHKey' => false,
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'like', '{identifier}::%')->delete();
    }
};
