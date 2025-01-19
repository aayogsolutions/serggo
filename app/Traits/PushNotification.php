<?php

namespace App\Traits;

use Google\Auth\ApplicationDefaultCredentials;
use Illuminate\Support\Facades\Http;

trait PushNotification
{
    public function sendPushNotification($token, $title, $body, $data = [])
    {
        $fcmurl = "POST https://fcm.googleapis.com/v1/projects/serggo-a055c/messages:send";

        $notification = [
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => $data,
            'token' => $token,
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'bearer' . $this->getAccessToken(),
            ])->post($fcmurl, ['message' => $notification]);

            return response()->json();
        } catch (\Throwable $th) {
            return response()->json();
        }
    }

    private function getAccessToken()
    {
        $key_path = config('services.firebase.key_path');

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $key_path);
        
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        $credentials = ApplicationDefaultCredentials::getCredentials($scopes);

        $token = $credentials->fetchAuthToken();

        return $token['access_token'] ?? null;
    }
}
