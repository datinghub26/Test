<?php

namespace App\Http\Controllers\Postback;

use App\Models\Lead;
use App\Models\Offer;
use App\Models\PendingOffer;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Log;
use Stevebauman\Location\Facades\Location;
use Validator;

abstract class Postback
{
    // public function handlePostback(Request $request, $companyName, $allowedIps = null, $modifiedData = null)
    // {
    //     try {
    //         if ($allowedIps) {
    //             $ip = ip();
    //             if (!in_array($ip, $allowedIps)) {
    //                 Log::channel('postback-error')->warning("Postback from $companyName failed. IP $ip not allowed.");
    //                 return response("0", 400);
    //             }
    //         }

    //         $data = $modifiedData ?? $request->all();
    //         $data['company'] = $companyName;

    //         $macroPattern = '/\{[^}]+\}|\{\{[^}]+\}\}|\[\[[^\]]+\]\]|\[%[^%]+%\]|\[[A-Z_]+\]/';
    //         foreach ($data as $key => $value) {
    //             if (is_string($value) && preg_match($macroPattern, $value)) {
    //                 $data[$key] = null;
    //             }
    //         }

    //         if ($data['amount'] < 0 || $data['payout'] < 0)
    //             $data['status'] = 2;

    //         $this->validate($data);
    //         $this->successPostback($data);
    //         return response("1", 200);
    //     } catch (Exception $e) {
    //         $this->exception($request, $e);
    //         return response("0", 400);
    //     }
    // }
    
    
    
    public function handlePostback(Request $request, $companyName, $allowedIps = null, $modifiedData = null)
{
    try {
        // ✅ IP whitelist check
        if ($allowedIps) {
            $ip = ip(); // Your helper to get real user IP
            if (!in_array($ip, $allowedIps)) {
                Log::channel('postback-error')->warning("Postback from $companyName failed. IP $ip not allowed.");
                return response("0", 400);
            }
        }

        // ✅ Get incoming data
        $data = $modifiedData ?? $request->all();
        $data['company'] = $companyName;

        // ✅ Clean up placeholder macros (like {subId}, [[USER_ID]], etc.)
        $macroPattern = '/\{[^}]+\}|\{\{[^}]+\}\}|\[\[[^\]]+\]\]|\[%[^%]+%\]|\[[A-Z_]+\]/';
        foreach ($data as $key => $value) {
            if (is_string($value) && preg_match($macroPattern, $value)) {
                $data[$key] = null;
            }
        }

        // ✅ Normalize important fields
        $data['user_id'] = $data['user_id'] ?? $data['subId'] ?? $data['subid'] ?? $data['sub_id'] ?? $data['sub1'] ?? $data['uid'] ?? $data['player_id'] ?? null;
        $data['amount'] = isset($data['amount']) ? floatval($data['amount']) : (isset($data['points']) ? floatval($data['points']) : (isset($data['reward']) ? floatval($data['reward']) : (isset($data['currency_amount']) ? floatval($data['currency_amount']) : 0)));
        $data['payout'] = isset($data['payout']) ? floatval($data['payout']) : (isset($data['revenue']) ? floatval($data['revenue']) : 0);
        $data['trx'] = $data['trx'] ?? $data['transaction_id'] ?? $data['trans_id'] ?? $data['tx_id'] ?? $data['txid'] ?? $data['tracking_id'] ?? $data['conversion_id'] ?? null;
        $data['campaign_id'] = $data['campaign_id'] ?? $data['offer_id'] ?? $data['of_id'] ?? null;
        $data['campaign_name'] = $data['campaign_name'] ?? $data['offer_name'] ?? $data['of_name'] ?? null;

        // ✅ Handle network test pings gracefully
        if ($request->input('test') == '1' || $request->input('is_test') == '1' || $request->input('status') === 'test' || $data['user_id'] === 'test' || $data['user_id'] === 'test_user') {
            Log::channel('postback')->info("Test conversion ping received and acknowledged for {$companyName}", $data);
            return response("1", 200);
        }

        // ✅ Mark as failed if payout or amount is negative
        if ($data['amount'] < 0 || $data['payout'] < 0) {
            $data['status'] = 2;
        }

        // ✅ Optional: Log the request for debugging
        // Log::debug('Normalized postback data:', $data);

        // ✅ Run validation (make sure your validate() accepts this structure)
        $this->validate($data);

        // ✅ Postback success handler
        $this->successPostback($data);

        return response("1", 200);

    } catch (Exception $e) {
        $this->exception($request, $e); // Your custom error logging
        return response("0", 400);
    }
}

    

    protected function exception(Request $request, Exception $exception)
    {
        $data = [
            'method' => $request->getMethod(),
            'ip' => ip(),
            'request' => $request->all(),
            'exception' => [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]
        ];

        $message = $request->getPathInfo();
        Log::channel('postback-error')->error($message, $data);
    }

    protected function log(Request $request)
    {
        $data = [
            'method' => $request->getMethod(),
            'ip' => ip(),
            'request' => $request->all(),
        ];

        $message = $request->getPathInfo();
        Log::channel('postback')->info($message, $data);
    }

    protected function validate($data)
    {
        $validator = Validator::make($data, [
            'user_id' => 'required|exists:users,id',
            'ip' => 'ip|nullable',
            'amount' => 'required|numeric',
            'payout' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    protected function successPostback($data)
    {
        if ($this->hasChargeback($data)) {
            $this->applyChargeback($data);
            return;
        }

        $this->applyPending($data);

        // ✅ Check if a transaction with this trx ID already exists
        if (!empty($data['trx'])) {
            $existingLead = Lead::where('offer_trx_id', $data['trx'])->first();

            if ($existingLead) {
                // If previously pending and incoming postback is approved (not pending)
                if ($existingLead->status === 'pending' && !isset($data['pending'])) {
                    $existingLead->update([
                        'status' => 'approved',
                        'release_at' => null,
                    ]);

                    $user = $existingLead->user;
                    if ($user) {
                        $user->updateUserPointsAndLevel(floatval($existingLead->points));
                        $user->addNotification(
                            'Offer Completed',
                            "Your pending offer '{$existingLead->name}' was approved and {$existingLead->points} ERC credited!",
                            'offer_completed'
                        );
                    }

                    Log::channel('postback')->info("Pending lead #{$existingLead->id} approved via follow-up postback for trx {$data['trx']}");
                    return;
                }

                // If already pending and incoming is still pending, ignore duplicate
                if ($existingLead->status === 'pending' && isset($data['pending'])) {
                    Log::channel('postback')->info("Duplicate pending postback ignored for trx {$data['trx']}");
                    return;
                }

                // If already approved, ignore duplicate to avoid double-crediting
                if ($existingLead->status === 'approved') {
                    Log::channel('postback')->info("Duplicate postback ignored for already approved trx {$data['trx']}");
                    return;
                }

                // If already rejected, ignore
                if ($existingLead->status === 'rejected') {
                    Log::channel('postback')->info("Duplicate postback ignored for already rejected trx {$data['trx']}");
                    return;
                }
            }
        }

        $this->saveLead($data);
        $this->increasePoints($data);
    }

    protected function hasChargeback($data)
    {
        if (isset($data['status'])) {
            $s = strtolower(trim((string)$data['status']));
            if (in_array($s, ['2', 'chargeback', 'rejected', 'reversed', 'cancel'])) {
                return true;
            }
        }

        if (floatval($data['amount'] ?? 0) < 0 || floatval($data['payout'] ?? 0) < 0) {
            return true;
        }

        return false;
    }

    protected function applyChargeback($data)
    {
        if (isset($data['trx'])) {
            $lead = Lead::where('offer_trx_id', $data['trx'])->first();
            if ($lead) {
                $lead->update([
                    'status' => 'rejected',
                    'reason' => 'Chargeback',
                ]);

                $lead->user->updateUserPointsAndLevel($lead->points * -1);
                $lead->user->addNotification(
                    'Offer Chargeback',
                    "Chargeback: {$lead->points} ERC was deducted for '{$lead->name}'.",
                    'chargeback'
                );
                return;
            }
        }

        $user = User::findOrFail($data['user_id']);
        if (!$user)
            return;

        $user->leads()
            ->where('offer_id', $data['campaign_id'])
            ->update([
                'status' => 'rejected',
                'reason' => 'Chargeback',
            ]);

        $reduction_points = abs(floatval($data['amount'])) * -1;
        $user->updateUserPointsAndLevel($reduction_points);
        $user->addNotification(
            'Offer Chargeback',
            "Chargeback: " . abs(floatval($data['amount'])) . " ERC was deducted for '{$this->getHandledName($data)}'.",
            'chargeback'
        );
    }

    protected function applyPending(&$data)
    {
        // 1. Check for specific Pending Offer hold duration rules by Offer ID / campaign_id
        if (!empty($data['campaign_id'])) {
            $holdDuration = PendingOffer::getHoldDurationForOffer((string)$data['campaign_id']);
            if ($holdDuration && $holdDuration > 0) {
                Log::channel('postback-hold')->info("Offer ID {$data['campaign_id']} matched Pending Offer Rule with hold of {$holdDuration} days", $data);
                $data['pending'] = true;
                $data['hold_duration_days'] = (int)$holdDuration;
                $data['release_at'] = now()->addDays((int)$holdDuration);
                return;
            }
        }

        // 2. Global pending threshold
        $pending = setting('postback.enable_pending');
        $pendingAmount = (int)setting('postback.pending_threshold', 0);
        if ($pending && floatval($data['amount'] ?? 0) >= $pendingAmount) {
            Log::channel('postback-hold')->info("Global Postback Threshold Reached", $data);
            $data['pending'] = true;
            $defaultDays = (int)setting('postback.pending_duration', 7);
            if ($defaultDays <= 0) $defaultDays = 7;
            $data['hold_duration_days'] = $defaultDays;
            $data['release_at'] = now()->addDays($defaultDays);
            return;
        }

        // 3. Status from network indicating pending (0, 3, pending, hold, waiting)
        if (isset($data['status'])) {
            $s = strtolower(trim((string)$data['status']));
            if (in_array($s, ['0', '3', 'pending', 'hold', 'waiting'])) {
                Log::channel('postback-hold')->info("Postback Status Pending ({$s})", $data);
                $data['pending'] = true;
                if (empty($data['hold_duration_days'])) {
                    $defaultDays = (int)setting('postback.pending_duration', 7);
                    if ($defaultDays <= 0) $defaultDays = 7;
                    $data['hold_duration_days'] = $defaultDays;
                    $data['release_at'] = now()->addDays($defaultDays);
                }
            }
        }
    }

    protected function saveLead($data)
    {
        $user = User::findOrFail($data['user_id']);
        $user->leads()->create([
            'provider' => $data['company'],
            'name' => $this->getHandledName($data),
            'offer_id' => $data['campaign_id'] ?? null,
            'offer_name' => $data['campaign_name'] ?? null,
            'offer_trx_id' => $data['trx'] ?? null,
            'points' => $data['amount'] ?? 0,
            'payout' => $data['payout'] ?? 0,
            'ip' => $data['ip'] ?? null,
            'country_code' => $this->getCountryCode($data),
            'type' => 'offer',
            'status' => isset($data['pending']) ? 'pending' : 'approved',
            'release_at' => $data['release_at'] ?? null,
            'hold_duration_days' => $data['hold_duration_days'] ?? null,
        ]);

        if (!isset($data['pending'])) {
            $user->addNotification(
                'Offer Completed',
                "You earned {$data['amount']} ERC for completing '{$this->getHandledName($data)}'!",
                'offer_completed'
            );
        } else {
            // Dynamic hold duration calculation
            $holdDuration = $data['hold_duration_days'] ?? null;
            if (!$holdDuration && !empty($data['release_at'])) {
                $releaseDate = \Carbon\Carbon::parse($data['release_at']);
                $holdDuration = max(1, (int)ceil(now()->diffInHours($releaseDate, false) / 24));
            }
            if (!$holdDuration) {
                $defaultDays = (int)setting('postback.pending_duration', 7);
                $holdDuration = $defaultDays > 0 ? $defaultDays : 7;
            }

            $daysUnit = $holdDuration == 1 ? 'day' : 'days';
            $template = setting('postback.pending_notification_template');
            if (empty($template)) {
                $template = "Your offer '{offer_name}' is pending review. Hold time: {hold_days} {days_unit}.";
            }

            $message = str_replace(
                ['{offer_name}', '{hold_days}', '{days}', '{hold_time}', '{days_unit}'],
                [$this->getHandledName($data), $holdDuration, $holdDuration, "{$holdDuration} {$daysUnit}", $daysUnit],
                $template
            );

            $user->addNotification(
                'Offer Pending',
                $message,
                'warning'
            );
        }
    }

    protected function increasePoints($data)
    {
        if (isset($data['pending']))
            return;

        $user = User::findOrFail($data['user_id']);
        $user->updateUserPointsAndLevel(floatval($data['amount']) ?? 0);
    }

    protected function getHandledName($data)
    {
        $name = $data['company'];
        if (isset($data['campaign_name']))
            $name .= " - {$data['campaign_name']}";
        if (isset($data['campaign_id']))
            $name .= " ({$data['campaign_id']})";

        return $name;
    }

    protected function getCountryCode($data)
    {
        if (!isset($data['ip']))
            return null;

        if (isset($data['country_code']))
            return $data['country_code'];

        return Location::get($data['ip'])?->countryCode ?? null;
    }
}
