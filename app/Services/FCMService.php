<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;


class FCMService
{
    private $serverKey;
    private $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    protected $serviceAccount;
    protected $projectId;
    protected $tokenUri;
    protected $client;

    /**
     * False when storage/app/service-account.json is missing or unreadable
     * JSON. Previously this class would silently limp along with a null
     * $serviceAccount/$tokenUri in that case — every send would eventually
     * fail deep inside generateAccessToken() with no log line anywhere
     * explaining why. sendNotification() now checks this up front and
     * fails loudly (logged) instead.
     */
    protected $configured = false;


    public function __construct()
    {
      //  $this->fcmUrl = 'https://fcm.googleapis.com/v1/projects/stage-picker/messages:send';
        $this->serverKey = 'AAAAYCe7MYI:APA91bG2BCIUbYLUUeEBmnvmP0gEyKaGwdejDUJC7PZGVQjjywyRwaB__vRSLEBNzD3-rfaODr9a3CeWs_9DVVjH4esvL-0M5xuvab7sS-KrLm--13tXWpXHSoz-IODsZkFxbQT5WpAb';

        $path = storage_path('app/service-account.json');

        if (!is_file($path) || !is_readable($path)) {
            Log::error('FCMService: service-account.json is missing (or unreadable) at ' . $path . ' — every push notification will fail until it is placed there.');
            $this->client = new Client();
            return;
        }

        $decoded = json_decode(file_get_contents($path), true);
        if (!is_array($decoded) || empty($decoded['token_uri']) || empty($decoded['private_key']) || empty($decoded['client_email'])) {
            Log::error('FCMService: service-account.json at ' . $path . ' is present but not valid Firebase service-account JSON (missing token_uri/private_key/client_email) — every push notification will fail.');
            $this->client = new Client();
            return;
        }

        $this->serviceAccount = $decoded;
        $this->projectId = 'stage-picker';
        $this->tokenUri = $decoded['token_uri'];
        $this->client = new Client();
        $this->configured = true;
    }

    /**
     * Send push notification to a single user
     * 
     * @param string $fcmToken
     * @param string $title
     * @param string $body
     * @param array $data - Custom payload (type, id, userId, referenceId, etc.)
     * @return array
     */
    public function sendNotificationOld($fcmToken, $title, $body, $data = [])
    {
        try {
            $notification = [
                'to' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => array_merge([
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ], $data),
                'priority' => 'high',
            ];
            dump($notification);
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $notification);
dd($response->status(), $response->body());

            if ($response->successful()) {
                Log::info('FCM notification sent', ['response' => $response->json()]);
                return ['success' => true, 'response' => $response->json()];
            }

            Log::error('FCM failed', ['status' => $response->status(), 'body' => $response->body()]);
            return ['success' => false, 'error' => $response->body()];

        } catch (\Exception $e) {
            Log::error('FCM exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

     private function sendCurlRequest($notification)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$this->fcmUrl");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: key=' . $this->serverKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	
        // curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
        // curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['success' => false, 'error' => $error];
        }
        
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'data' => json_decode($result, true)];
        }
        $response = json_decode($result, true);
       
        return ['success' => false, 'error' => $result];
    }

    public function sendNotification($fcmToken, $title, $body, $data = [])
    {
        if (!$this->configured) {
            // Constructor already logged exactly why (missing file vs
            // invalid JSON) — no need to repeat it on every call, but the
            // caller still needs a clean failure result rather than a
            // fatal error from calling generateAccessToken() on nulls.
            return [
                'success' => false,
                'error' => 'FCMService not configured — see FCMService constructor log for details.',
            ];
        }
        try {
            $accessToken = $this->generateAccessToken();

            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $notification = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => (Object)$data,
                    'android' => [
                      'priority' => 'high', // ✅ moved here
                      'notification' => [
                          'sound' => 'default',
                          'channel_id' => 'high_importance_channel',
                          'notification_priority' => 'PRIORITY_HIGH',
                      ],
                  ],
                  'apns' => [
                      'headers' => [
                          'apns-priority' => '10',
                      ],
                      'payload' => [
                          'aps' => [
                              'alert' => [
                                  'title' => $title,
                                  'body' => $body,
                              ],
                              'sound' => 'default',
                              // 'content-available' => 1,
                          ],
                      ],
                  ],
                ],
            ];

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $notification,
            ]);

            $result = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'response' => $result,
            ];
        } catch (\Throwable $e) {
            // Was `catch (\Exception $e)`, which does NOT catch \Error /
            // \TypeError — e.g. JWT::encode() throwing a TypeError when
            // $this->serviceAccount['private_key'] is null used to escape
            // this catch entirely and become an uncaught fatal error deep
            // inside whatever API request triggered a push. Catching
            // \Throwable and logging here means a bad/expired key, a
            // revoked device token, or an FCM outage now shows up in
            // storage/logs/laravel.log instead of just... nothing.
            Log::error('FCMService::sendNotification failed', [
                'fcmToken' => $fcmToken,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }


   /**
     * Generate Firebase OAuth2 Access Token manually
     */
    public function generateAccessToken(): string
    {
        $now = time();
        $jwt = [
            'iss' => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $this->tokenUri,
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        // Encode JWT using RS256
        $jwtEncoded = JWT::encode($jwt, $this->serviceAccount['private_key'], 'RS256');

        // Exchange JWT for Access Token
        $response = $this->client->post($this->tokenUri, [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwtEncoded,
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);
        return $body['access_token'];
    }


    /**
     * Send to multiple users
     */
    public function sendToMultiple($fcmTokens, $title, $body, $data = [])
    {
        try {
            $notification = [
                'registration_ids' => $fcmTokens,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => array_merge([
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ], $data),
                'priority' => 'high',
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $notification);

            return $response->successful() 
                ? ['success' => true, 'response' => $response->json()]
                : ['success' => false, 'error' => $response->body()];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}