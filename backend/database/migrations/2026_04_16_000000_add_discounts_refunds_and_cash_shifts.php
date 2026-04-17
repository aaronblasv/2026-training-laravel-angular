<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('discount_type', ['amount', 'percentage'])->nullable()->after('diners');
            $table->integer('discount_value')->default(0)->after('discount_type');
            $table->integer('discount_amount')->default(0)->after('discount_value');
        });

        Schema::table('order_lines', function (Blueprint $table) {
            $table->enum('discount_type', ['amount', 'percentage'])->nullable()->after('tax_percentage');
            $table->integer('discount_value')->default(0)->after('discount_type');
            $table->integer('discount_amount')->default(0)->after('discount_value');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->integer('subtotal')->default(0)->after('value_date');
            $table->integer('tax_amount')->default(0)->after('subtotal');
            $table->integer('line_discount_total')->default(0)->after('tax_amount');
            $table->integer('order_discount_total')->default(0)->after('line_discount_total');
            $table->integer('refunded_total')->default(0)->after('total');
        });

        Schema::table('sales_lines', function (Blueprint $table) {
            $table->integer('line_subtotal')->default(0)->after('tax_percentage');
            $table->integer('tax_amount')->default(0)->after('line_subtotal');
            $table->enum('discount_type', ['amount', 'percentage'])->nullable()->after('tax_amount');
            $table->integer('discount_value')->default(0)->after('discount_type');
            $table->integer('discount_amount')->default(0)->after('discount_value');
            $table->integer('line_total')->default(0)->after('discount_amount');
            $table->integer('refunded_quantity')->default(0)->after('line_total');
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('restaurant_id')->constrained('restaurants');
            $table->foreignId('sale_id')->constrained('sales');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type', ['full', 'partial']);
            $table->enum('method', ['cash', 'card', 'bizum']);
            $table->string('reason')->nullable();
            $table->integer('subtotal')->default(0);
            $table->integer('tax_amount')->default(0);
            $table->integer('total')->default(0);
            $table->timestamps();
        });

        Schema::create('refund_lines', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('refund_id')->constrained('refunds')->cascadeOnDelete();
            $table->foreignId('sale_line_id')->constrained('sales_lines');
            $table->integer('quantity');
            $table->integer('subtotal');
            $table->integer('tax_amount');
            $table->integer('total');
            $table->timestamps();
        });

        Schema::create('cash_shifts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('restaurant_id')->constrained('restaurants');
            $table->foreignId('opened_by_user_id')->constrained('users');
            $table->foreignId('closed_by_user_id')->nullable()->constrained('users');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->integer('opening_cash')->default(0);
            $table->integer('cash_total')->default(0);
            $table->integer('card_total')->default(0);
            $table->integer('bizum_total')->default(0);
            $table->integer('refund_total')->default(0);
            $table->integer('counted_cash')->nullable();
            $table->integer('cash_difference')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_shifts');
        Schema::dropIfExists('refund_lines');
        Schema::dropIfExists('refunds');

        Schema::table('sales_lines', function (Blueprint $table) {
            $table->dropColumn([
                'line_subtotal',
                'tax_amount',
                'discount_type',
                'discount_value',
                'discount_amount',
                'line_total',
                'refunded_quantity',
            ]);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'tax_amount',
                'line_discount_total',
                'order_discount_total',
                'refunded_total',
            ]);
        });

        Schema::table('order_lines', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'discount_amount']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'discount_amount']);
        });
    }
};