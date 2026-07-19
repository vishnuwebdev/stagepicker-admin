<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NotificationCycle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\FCMService;

class SendDailyNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily notifications to users based on 14-day cycle schedule';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting daily notification process...');
        
        $today = Carbon::today();
        $notificationDays = [2, 4, 7, 10, 14];
        
        // Get all users who should receive notifications
        // Using chunking for scalability with millions of users
        $sentCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $totalUsers = 0;
        
        // Process users in chunks to avoid memory issues
        User::whereNotNull('device_token')
            ->where('device_token', '!=', '')
            ->where('id', '>', 1353)
            ->chunk(1000, function ($users) use (&$sentCount, &$skippedCount, &$errorCount, &$totalUsers, $today) {
                $totalUsers += $users->count();
                
                foreach ($users as $user) {
            try {
                $this->info("Processing user ID: {$user->id} {$user->email}");
                // Get or create notification cycle for user
                $cycle = NotificationCycle::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'cycle_day' => 0,
                        'cycle_start_date' => $today,
                        'message_index_day2' => 0,
                        'message_index_day4' => 0,
                        'message_index_day7' => 0,
                        'message_index_day10' => 0,
                        'message_index_day14' => 0,
                    ]
                );
                $hasSent = $cycle->isNotificationDayToday();
                $this->info("User ID: {$user->id} - {!$hasSent} isSkipped:  " . (!$hasSent ? 'Yes' : 'No'));


                // Check if notification was already sent today
                if ($cycle->wasNotificationSentToday() || $cycle->isNotificationCycleCompleted()) {
                    $skippedCount++;
                    continue;
                }
                
                // Check if today is a notification day
                if (!$cycle->isNotificationDayToday()) {
                    $skippedCount++;
                    continue;
                }
                
                // Get today's notification day (2, 4, 7, 10, or 14)
                $todayNotificationDay = $cycle->getTodayNotificationDay();
                
                if ($todayNotificationDay === 0) {
                    $skippedCount++;
                    continue;
                }
                
                // Get the current message for this day
                $message = $cycle->getCurrentMessageForDay($todayNotificationDay);
                
                if (!$message) {
                    $skippedCount++;
                    Log::warning("No message found for user {$user->id} on day {$todayNotificationDay}");
                    continue;
                }
                
                $this->info("Sending notification to user ID: {$user->id} - Day {$todayNotificationDay}: {$message}");
                $this->info("Using device token: {$user->device_token}");
                // Send notification using FCMService
                $fcmService = new FCMService();
                $fcmService->sendNotification(
                    $user->device_token, // FCM Token
                    $message,            // Title
                    '',           // Body/Message
                    [                    // Custom Data
                        // // 'type' => 'daily_reminder',
                        // 'cycle_day' => $todayNotificationDay,
                        // 'user_id' => $user->id,
                    ]
                );
                
                // Update cycle: increment message index and set last notification date
                $cycle->incrementMessageIndex($todayNotificationDay);
                $cycle->last_notification_date = $today;
                $cycle->cycle_day = $todayNotificationDay;
                $cycle->sent_notifications += 1;
                $cycle->save();
                
                $sentCount++;
                
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Error sending notification to user {$user->id}: " . $e->getMessage());
                $this->error("Error for user {$user->id}: " . $e->getMessage());
            }
        }
        });
        
        $this->info("Notification process completed.");
        $this->info("Total users processed: {$totalUsers}");
        $this->info("Sent: {$sentCount}, Skipped: {$skippedCount}, Errors: {$errorCount}");
        
        return Command::SUCCESS;
    }
}
