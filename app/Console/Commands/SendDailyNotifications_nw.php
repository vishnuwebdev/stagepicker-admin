<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NotificationCycle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\FCMService;

class SendDailyNotificationsNW extends Command
{
    protected $signature = 'notifications:send-daily';
    protected $description = 'Send daily notifications to users based on 14-day cycle schedule';

    public function handle()
    {
        $this->info('Starting daily notification process...');

        $today = Carbon::today();
        $fcmService = new FCMService();

        $sentCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $totalUsers = 0;
        //1353 user id is registered before 13 Jan 2026 
        // and we would want to send the notification after that users
        User::whereNotNull('device_token')
            ->where('device_token', '!=', '')
            ->where('id', ">=", 1353)
            ->orderBy('id')
            ->chunkById(1000, function ($users) use(
                $today,
                $fcmService,
                &$sentCount,
                &$skippedCount,
                &$errorCount,
                &$totalUsers
            ) {

                $this->info($users);
                $totalUsers += $users->count();

                foreach ($users as $user) {
                    DB::beginTransaction();

                    try {
                        // 1. Get or create cycle (DO NOT reset start date if exists)
                        $cycle = NotificationCycle::firstOrCreate(
                            ['user_id' => $user->id],
                            [
                                'cycle_start_date' => $today,
                                'message_index_day2' => 0,
                                'message_index_day4' => 0,
                                'message_index_day7' => 0,
                                'message_index_day10' => 0,
                                'message_index_day14' => 0,
                            ]
                        );

                        // 2. Skip if already sent today
                        if ($cycle->wasNotificationSentToday()) {
                            $skippedCount++;
                            DB::rollBack();
                            continue;
                        }

                        // 3. Check if today is a valid notification day
                        $todayNotificationDay = $cycle->getTodayNotificationDay();

                        if ($todayNotificationDay === 0) {
                            $skippedCount++;
                            DB::rollBack();
                            continue;
                        }

                        // 4. Get current message
                        $message = $cycle->getCurrentMessageForDay($todayNotificationDay);

                        if (!$message) {
                            $skippedCount++;
                            Log::warning("No message found for user {$user->id} on day {$todayNotificationDay}");
                            DB::rollBack();
                            continue;
                        }

                        // 5. Send FCM notification
                        $sent = $fcmService->sendNotification(
                            // $user->device_token,
                            "ebWiACBHRB6XSqV61JRucz:APA91bG4Eq0k_XK5I8ZLdus2ixYzetIwZdPGK87zKlu6R8jaMnQ2gT2e95EH5CiJtGKtr6vdABiBq-BoXLlhR09OUxaKwOaETuAxQYttsEzlYy7nkQP9nsQ",
                            'Stay Active',
                            $message,
                            [
                                'type' => 'daily_reminder',
                                'cycle_day' => $todayNotificationDay,
                                'user_id' => $user->id,
                            ]
                        );

                        if (!$sent) {
                            $errorCount++;
                            Log::error("FCM failed for user {$user->id}");
                            DB::rollBack();
                            continue;
                        }

                        // 6. Update cycle data
                        $cycle->incrementMessageIndex($todayNotificationDay);
                        $cycle->last_notification_date = $today;
                        $cycle->save();

                        DB::commit();
                        $sentCount++;

                    } catch (\Exception $e) {
                        DB::rollBack();
                        $errorCount++;
                        Log::error("Error sending notification to user {$user->id}: " . $e->getMessage());
                    }
                }
            });

        $this->info("Notification process completed.");
        $this->info("Total users processed: {$totalUsers}");
        $this->info("Sent: {$sentCount}");
        $this->info("Skipped: {$skippedCount}");
        $this->info("Errors: {$errorCount}");

        return Command::SUCCESS;
    }
}
