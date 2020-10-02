<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUsersTable
 */
class CreateJournalTransactionsTable extends Migration
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('journal_id');
            $table->bigInteger('debit')->nullable();
            $table->bigInteger('credit')->nullable();
            $table->char('currency', 5);
            $table->text('memo')->nullable();
            $table->string('type', 20)->nullable()->index();
            $table->string('subject_type')->nullable();
            $table->integer('subject_id')->nullable();
            $table->timestamp('post_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_journals');
    }
}
