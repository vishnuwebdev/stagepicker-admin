<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NotificationCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cycle_day',
        'message_index_day2',
        'message_index_day4',
        'message_index_day7',
        'message_index_day10',
        'message_index_day14',
        'sent_notifications',
        'cycle_start_date',
        'last_notification_date',
    ];

    protected $casts = [
        'cycle_start_date' => 'date',
        'last_notification_date' => 'date',
    ];

    /**
     * Get messages for Day 2
     */
    public static function getDay2Messages(): array
    {
        return [
            "Casting directors are browsing today—share something new to stay visible.",
            "Your profile performs better when you post regularly. Drop a quick update.",
            "A fresh post can put you in front of more producers this week.",
            "Show the industry what you're working on. A small update goes a long way.",
        ];
    }

    /**
     * Get messages for Day 4
     */
    public static function getDay4Messages(): array
    {
        return [
            "What's one thing you've practiced this week? Share it with your community.",
            "Post a behind-the-scenes moment from your latest project.",
            "Got a new look, skill, or clip? Update your profile to keep it fresh.",
            "Share a quick voice note or selfie video—authenticity wins here.",
        ];
    }

    /**
     * Get messages for Day 7
     */
    public static function getDay7Messages(): array
    {
        return [
            "Producers are searching for talent in your category today. Update your profile to stand out.",
            "New opportunities are trending—post something to stay competitive.",
            "Your category is heating up. A new post can help you rise in searches.",
        ];
    }

    /**
     * Get messages for Day 10
     */
    public static function getDay10Messages(): array
    {
        return [
            "Your journey matters. Share a small win or lesson from this week.",
            "Consistency builds your brand. What's one thing you learned recently?",
            "Your future self will thank you for documenting your growth.",
        ];
    }

    /**
     * Get messages for Day 14
     */
    public static function getDay14Messages(): array
    {
        return [
            "Inspire someone today—share a tip or story from your creative path.",
            "Your voice adds to the community. What's on your mind this week?",
            "Spotlight another creative or share a collaboration moment.",
        ];
    }

    public function isNotificationCycleCompleted(): bool {
        return $this->sent_notifications >= 17;
    }

    /**
     * Get messages for a specific day
     */
    public static function getMessagesForDay(int $day): array
    {
        switch ($day) {
            case 2:
                return self::getDay2Messages();
            case 4:
                return self::getDay4Messages();
            case 7:
                return self::getDay7Messages();
            case 10:
                return self::getDay10Messages();
            case 14:
                return self::getDay14Messages();
            default:
                return [];
        }
    }

    /**
     * Get the count of messages for a specific day
     */
    public static function getMessageCountForDay(int $day): int
    {
        return count(self::getMessagesForDay($day));
    }

    /**
     * Get the current message for a specific day based on message index
     */
    public function getCurrentMessageForDay(int $day): ?string
    {
        $messages = self::getMessagesForDay($day);
        if (empty($messages)) {
            return null;
        }

        $indexField = "message_index_day{$day}";
        $currentIndex = $this->$indexField ?? 0;

        // Ensure index is within bounds
        if ($currentIndex >= count($messages)) {
            $currentIndex = 0;
        }

        return $messages[$currentIndex] ?? null;
    }

    /**
     * Get the message index field name for a specific day
     */
    public static function getMessageIndexField(int $day): string
    {
        return "message_index_day{$day}";
    }

    /**
     * Increment message index for a specific day
     */
    public function incrementMessageIndex(int $day): void
    {
        $indexField = self::getMessageIndexField($day);
        $messageCount = self::getMessageCountForDay($day);
        $currentIndex = $this->$indexField ?? 0;

        // Increment and wrap around if needed
        $newIndex = ($currentIndex + 1) % $messageCount;
        
        $this->$indexField = $newIndex;
        $this->save();
    }


    /**
     * Get the day in cycle (0-13) based on cycle start date
     * Day 0 = cycle start date, Day 1 = next day, etc.
     */
    public function getDayInCycle(): int
    {
        $today = Carbon::today();
        $startDate = Carbon::parse($this->cycle_start_date)->startOfDay();
        
        // Calculate days since cycle start (can be negative if start date is in future)
        $daysSinceStart = $startDate->diffInDays($today, false);
        
        // Ensure non-negative and within cycle (0-13)
        if ($daysSinceStart < 0) {
            return 0;
        }
        // return $daysSinceStart % 14;
        return ($daysSinceStart % 14) + 1; // 1 to 14
    }

    /**
     * Check if today is a notification day for this cycle
     */
    public function isNotificationDayToday(): bool
    {
        $dayInCycle = $this->getDayInCycle();
        $notificationDays = [2, 4, 7, 10, 14];
        return in_array($dayInCycle, $notificationDays);
    }

    /**
     * Get which notification day it is today (returns 2, 4, 7, 10, 14, or 0)
     */
    public function getTodayNotificationDay(): int
    {
        $dayInCycle = $this->getDayInCycle();
        $notificationDays = [2, 4, 7, 10, 14];
        return in_array($dayInCycle, $notificationDays) ? $dayInCycle : 0;
    }

    /**
     * Check if notification was already sent today
     */
    public function wasNotificationSentToday(): bool
    {
        
        if (!$this->last_notification_date) {
            return false;
        }

        return $this->last_notification_date->isToday();
    }
}
