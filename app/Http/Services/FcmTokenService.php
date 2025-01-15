<?php

namespace App\Http\Services;

use App\Models\FcmToken;
use Exception;
use Google\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class FcmTokenService
{
    public function createOrUpdate(array $data): Model
    {
        return FcmToken::updateOrCreate(
            ['user_id' => $data['user_id']],
            $data
        );
    }

    public function saveMasterIdsToToken(string $token, array $masterIds): void
    {
        Redis::pipeline(function ($pipe) use ($token, $masterIds) {
            foreach ($masterIds as $masterId) {
                $pipe->sadd('masters:' . $masterId, $token);
            }
        });
    }

    public function getTokensForMasters(array $masterIds): array
    {
        $tokens = Redis::pipeline(function ($pipe) use ($masterIds) {
            foreach ($masterIds as $masterId) {
                $pipe->smembers('masters:' . $masterId);
            }
        });

        return array_unique(array_merge(...$tokens));
    }

    public function sendNotificationsToUsers(array $masterIds, string $message): void
    {
        $tokens = $this->getTokensForMasters($masterIds);
        $this->sendMessage($tokens, $message);
    }

    /**
     * @throws Exception
     */
    public function sendMessage(array $firebaseTokens, string $message, string $motion = 'master_status', string $category = 'service'): void
    {
        $serviceAccountPath = base_path('path-to-service-account.json'); // Вкажіть шлях до JSON-файлу
        $accessToken = $this->getAccessToken($serviceAccountPath);

        $projectId = $this->getProjectId($serviceAccountPath);
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        foreach ($firebaseTokens as $firebaseToken) {
            $payload = [
                'message' => [
                    'token' => $firebaseToken,
                    'data' => [
                        'motion' => $motion,
                        'body' => $message,
                        'category' => $category,
                    ],
                    'android' => [
                        'priority' => 'high',
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'alert' => $message,
                                'sound' => 'default',
                            ],
                        ],
                    ],
                ],
            ];

            $this->sendRequest($url, $accessToken, $payload);
        }
    }

    /**
     * @throws Exception
     */
    private function getProjectId(string $serviceAccountPath): string
    {
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
        return $serviceAccount['project_id'] ?? throw new Exception('Project ID not found in service account file');
    }
    private function getAccessToken(string $serviceAccountPath): string
    {
        $client = new Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->useApplicationDefaultCredentials();

        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }

    private function sendRequest(string $url, string $accessToken, array $data): void
    {
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            logger()->error('FCM Request Error', ['error' => $error]);
        } else {
            logger()->info('FCM Response', ['response' => $response]);
        }
    }
}
