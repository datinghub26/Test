<?php

namespace App\Helpers;

use App\Models\Campaigns\Campaign;
use App\Models\Campaigns\CampaignEvent;
use App\Models\Campaigns\CampaignUser;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Log;

class CampaignHelper
{
    public static function handleRegistration(User $user): void
    {
        try {
            $data = session()->only(['click', 'campaign', 'source', 'oid']);
            $validator = Validator::make($data, [
                'click' => ['required', 'string', 'unique:campaign_users,click_id'],
                'campaign' => 'required|uuid',
                'source' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return;
            }

            $campaign = Campaign::where('identifier', $data['campaign'])->first();
            if (!$campaign) {
                Log::channel('campaign')->error("Campaign with identifier {$data['campaign']} not found");
                return;
            }

            $campaign->users()->create([
                'user_id' => $user->id,
                'click_id' => $data['click'],
                'source' => $data['source'] ?? null,
                'oid' => $data['oid'] ?? null,
            ]);

            session()->forget(['click', 'campaign', 'source', 'oid']);
        } catch (\Exception $e) {
            Log::channel('campaign')->error($e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }

    public static function handleCampaignEvents(): void
    {
        try {
            $users = CampaignUser::with([
                'campaign' => fn($query) => $query->where('is_active', true),
                'campaign.events' => fn($query) => $query->where('is_active', true),
                'eventProgress' => fn($query) => $query->where('is_completed', true),
            ])->get();

            foreach ($users as $user) {
                $completedEvents = $user->eventProgress->pluck('campaign_event_id')->toArray();
                $pendingEvents = $user->campaign->events->whereNotIn('id', $completedEvents);
                foreach ($pendingEvents as $event) {
                    match ($event->action) {
                        'registration' => self::notifyEventCompleted($user, $event),
                        'points' => self::checkPointsEvent($user, $event),
                        default => Log::info("No handler for event action: $event->action"),
                    };
                }
            }


        } catch (\Exception $e) {
            Log::channel('campaign')->error($e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }

    private static function checkPointsEvent(CampaignUser $user, CampaignEvent $event): void
    {
        if ($user->leads()->sum('points') < $event->target)
            return;

        self::notifyEventCompleted($user, $event);
    }

    private static function notifyEventCompleted(CampaignUser $user, CampaignEvent $event): void
    {
        $campaign = $event->campaign;
        $url = $campaign->url;
        $eventMacro = $campaign->event_macro;
        if ($campaign->has_multi_event) {
            if (str_contains($url, $eventMacro)) {
                $pattern = '/[&?]?' . preg_quote($eventMacro, '/') . '=[^&]*/';
                $url = preg_replace($pattern, '', $url);
                $url = rtrim($url, '&?');
            }
        }

        $url = str_replace('{click_id}', $user->click_id, $url);
        if ($campaign->has_multi_event) {
            $separator = !str_contains($url, '?') ? '?' : '&';
            $url .= "$separator$eventMacro=$event->name";
        }

        $response = Http::get($url);
        if ($response->status() == 200) {
            Log::channel('campaign')->info("Event completed for user: {$user->user->id}", [
                'campaign' => $campaign->name,
                'event' => $event->name,
                'response' => $response->header('content-type') === 'application/json' ? $response->json() : $response->body(),
            ]);

            $user->eventProgress()->create([
                'campaign_id' => $campaign->id,
                'campaign_event_id' => $event->id,
                'is_completed' => true,
            ]);
        } else {
            Log::channel('campaign')->error("Error while sending request to $campaign->name provider for user: $user->id", [
                'response' => $response->header('content-type') === 'application/json' ? $response->json() : $response->body(),
                'status' => $response->status(),
            ]);
        }
    }
}
