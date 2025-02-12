use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventTrackingToVisitorAnalytics extends Migration
{
    public function up()
    {
        Schema::table('visitor_analytics', function (Blueprint $table) {
            $table->json('events')->nullable();
            $table->json('click_data')->nullable();
            $table->json('scroll_depth')->nullable();
        });
    }

    public function down()
    {
        Schema::table('visitor_analytics', function (Blueprint $table) {
            $table->dropColumn('events');
            $table->dropColumn('click_data');
            $table->dropColumn('scroll_depth');
        });
    }
} 