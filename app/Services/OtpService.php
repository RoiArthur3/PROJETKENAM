<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function generateAndSendOtp(string $email, ?string $ipAddress = null): OtpCode
    {
        $this->invalidateExistingOtps($email);

        $code = OtpCode::generateCode();
        $expiresAt = Carbon::now()->addMinutes(10);

        $otpCode = OtpCode::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => $expiresAt,
            'ip_address' => $ipAddress,
        ]);

        $this->sendOtpEmail($email, $code);

        return $otpCode;
    }

    public function verifyOtp(string $email, string $code): ?User
    {
        $otpCode = OtpCode::where('email', $email)
            ->where('code', $code)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpCode) {
            return null;
        }

        $otpCode->update([
            'is_used' => true,
            'used_at' => Carbon::now(),
        ]);

        $user = User::where('email', $email)->first();
        if (!$user) {
            return null;
        }

        $user->update(['last_login_at' => Carbon::now()]);

        return $user;
    }

    private function invalidateExistingOtps(string $email): void
    {
        OtpCode::where('email', $email)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->update(['is_used' => true, 'used_at' => Carbon::now()]);
    }

    private function sendOtpEmail(string $email, string $code): void
    {
        try {
            Mail::raw(
                "Votre code de vérification est : {$code}\n\nCe code expire dans 10 minutes.\n\nSi vous n'avez pas demandé ce code, ignorez cet email.",
                function ($message) use ($email) {
                    $message->to($email)
                        ->subject('Code de vérification - Plateforme Groupage');
                }
            );
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
