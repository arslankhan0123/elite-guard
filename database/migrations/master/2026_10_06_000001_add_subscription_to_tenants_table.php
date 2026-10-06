<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'master';

    public function up(): void
    {
        Schema::connection('master')->table('tenants', function (Blueprint $table) {
            $table->string('subscription_type', 20)->default('trial')->after('is_active');   // trial | monthly | yearly
            $table->string('subscription_period', 20)->nullable()->after('subscription_type'); // 3days|1week|2weeks|3weeks|1month (trial only)
            $table->decimal('subscription_amount', 10, 2)->nullable()->after('subscription_period');
            $table->timestamp('subscription_starts_at')->nullable()->after('subscription_amount');
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_starts_at');
            $table->boolean('expiry_notified')->default(false)->after('subscription_ends_at');
        });
    }

    public function down(): void
    {
        Schema::connection('master')->table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_type',
                'subscription_period',
                'subscription_amount',
                'subscription_starts_at',
                'subscription_ends_at',
                'expiry_notified',
            ]);
        });
    }
};
