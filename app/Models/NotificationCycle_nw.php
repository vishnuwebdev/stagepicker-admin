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
        'message_index_day2',
        'message_index_day4',
        'message_index_day7',
        'message_index_day10',
        'message_index_day14',
        'cycle_start_date',
        'last_notification_date',
    ];

    protected $casts = [
        'cycle_start_date' => 'date',
        'last_notification_date' => 'date',
    ];

    /* =========================
        MESSAGE POOLS
    ========================== */

    public static function getDay2Messages(): array
    {
        return [
            "Casting directors are browsing today—share something new to stay visible.",
            "Your profile performs better when you post regularly. Drop a quick update.",
            "A fresh post can put you in front of more producers this week.",
            "Show the industry what you're working on. A small update goes a long way.",
        ];
    }

    public static function getDay4Messages(): array
    {
        return [
            "What's one thing you've practiced this week? Share it with your community.",
            "Post a behind-the-scenes moment from your latest project.",
            "Got a new look, skill, or clip? Update your profile to keep it fresh.",
            "Share a quick voice note or selfie video—authenticity wins here.",
        ];
    }

    public static function getDay7Messages(): array
    {
        return [
            "Producers are searching for talent in your category today. Update your profile to stand out.",
            "New opportunities are trending—post something to stay competitive.",
            "Your category is heating up. A new post can help you rise in searches.",
        ];
    }

    public static function getDay10Messages(): array
    {
        return [
            "Your journey matters. Share a small win or lesson from this week.",
            "Consistency builds your brand. What's one thing you learned recently?",
            "Your future self will thank you for documenting your growth.",
        ];
    }

    public static function getDay14Messages(): array
    {
        return [
            "Inspire someone today—share a tip or story from your creative path.",
            "Your voice adds to the community. What's on your mind this week?",
            "Spotlight another creative or share a collaboration moment.",
        ];
    }

    /* =========================
        HELPERS
    ========================== */



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

    public static function getMessageCountForDay(int $day): int
    {
        return count(self::getMessagesForDay($day));
    }

    public static function getMessageIndexField(int $day): string
    {
        return "message_index_day{$day}";
    }

    /* =========================
        CORE LOGIC
    ========================== */

    /**
     * Get current message for given day using rotating index
     */
    public function getCurrentMessageForDay(int $day): ?string
    {
        $messages = self::getMessagesForDay($day);

        if (empty($messages)) {
            return null;
        }

        $indexField = self::getMessageIndexField($day);
        $currentIndex = $this->$indexField ?? 0;

        if ($currentIndex >= count($messages)) {
            $currentIndex = 0;
        }

        return $messages[$currentIndex] ?? null;
    }

    /**
     * Increment message index safely
     */
    public function incrementMessageIndex(int $day): void
    {
        $indexField = self::getMessageIndexField($day);
        $messageCount = self::getMessageCountForDay($day);

        if ($messageCount === 0) {
            return;
        }

        $currentIndex = $this->$indexField ?? 0;
        $this->$indexField = ($currentIndex + 1) % $messageCount;
        $this->save();
    }

    /**
     * Day in cycle (1–14)
     */
    public function getDayInCycle(): int
    {
        $today = Carbon::today();
        $startDate = Carbon::parse($this->cycle_start_date)->startOfDay();

        $daysSinceStart = $startDate->diffInDays($today, false);

        if ($daysSinceStart < 0) {
            return 1;
        }

        return ($daysSinceStart % 14) + 1; // 1 to 14
    }

    /**
     * Returns 2,4,7,10,14 if today is notification day, else 0
     */
    public function getTodayNotificationDay(): int
    {
        $day = $this->getDayInCycle();
        return in_array($day, [2, 4, 7, 10, 14]) ? $day : 0;
    }

    /**
     * Check if notification already sent today
     */
    public function wasNotificationSentToday(): bool
    {
        if (!$this->last_notification_date) {
            return false;
        }

        return $this->last_notification_date->isToday();
    }
}
