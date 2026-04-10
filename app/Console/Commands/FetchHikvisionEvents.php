<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\HikvisionEventController;
use App\Models\FacialDevice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchHikvisionEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hikvision:fetch-events
                            {--device= : ID du terminal spécifique}
                            {--ip=192.168.1.70 : IP du terminal Hikvision}
                            {--url=/ISAPI/AccessControl/AcsEvent : URL ISAPI}
                            {--username=admin : Nom d\'utilisateur}
                            {--password=admin123 : Mot de passe}
                            {--port=80 : Port HTTP}
                            {--protocol=http : Protocole (http/https)}
                            {--max-results=100 : Nombre maximum d\'événements}
                            {--timeout=15 : Timeout en secondes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Récupérer les événements depuis l\'API ISAPI Hikvision';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Récupération des événements Hikvision...');

        // Options depuis la ligne de commande
        $deviceId = $this->option('device');
        $ip = $this->option('ip');
        $url = $this->option('url');
        $username = $this->option('username');
        $password = $this->option('password');
        $port = $this->option('port');
        $protocol = $this->option('protocol');
        $maxResults = $this->option('max-results');
        $timeout = $this->option('timeout');

        try {
            if ($deviceId) {
                // Utiliser un device enregistré en base
                return $this->fetchFromDevice($deviceId);
            } else {
                // Utiliser les paramètres directs
                return $this->fetchFromDirectParams($ip, $url, $username, $password, $port, $protocol, $maxResults, $timeout);
            }
        } catch (\Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
            Log::error('Hikvision fetch command error', [
                'error' => $e->getMessage(),
                'options' => $this->options()
            ]);
            return 1;
        }
    }

    /**
     * Récupérer depuis un device enregistré
     */
    private function fetchFromDevice($deviceId)
    {
        $device = FacialDevice::findOrFail($deviceId);

        $this->info("📡 Device: {$device->name} ({$device->ip_address}:{$device->port})");

        // Construire l'URL
        $baseUrl = "{$device->protocol}://{$device->ip_address}:{$device->port}";
        $eventsUrl = "{$baseUrl}/ISAPI/AccessControl/AcsEvent?searchID=0&maxResults=100";

        $this->info("🌐 URL: {$eventsUrl}");

        // Faire la requête
        $response = Http::withBasicAuth($device->username, $device->password)
            ->timeout($this->option('timeout'))
            ->get($eventsUrl);

        if (!$response->successful()) {
            $this->error("❌ Erreur HTTP {$response->status()}");
            $this->line("Response: " . $response->body());
            return 1;
        }

        return $this->processResponse($response, $device->name, $eventsUrl);
    }

    /**
     * Récupérer depuis paramètres directs
     */
    private function fetchFromDirectParams($ip, $url, $username, $password, $port, $protocol, $maxResults, $timeout)
    {
        $this->info("📡 Terminal direct: {$ip}:{$port}");
        $this->info("👤 Auth: {$username}");

        // Utiliser les variables d'environnement si non spécifiées
        if ($ip === '192.168.1.70' && $username === 'admin' && $password === 'admin123') {
            $ip = env('HIKVISION_IP', $ip);
            $username = env('HIKVISION_USER', env('HIKVISION_USERNAME', $username));
            $password = env('HIKVISION_PASS', env('HIKVISION_PASSWORD', $password));
            $port = env('HIKVISION_PORT', $port);
            $protocol = env('HIKVISION_PROTOCOL', $protocol);

            $this->info("🔧 Utilisation des variables d'environnement:");
            $this->info("   IP: {$ip}");
            $this->info("   User: {$username}");
            $this->info("   Port: {$port}");
            $this->info("   Protocol: {$protocol}");
        }

        // Construire l'URL complète
        $baseUrl = "{$protocol}://{$ip}:{$port}";
        $fullUrl = $baseUrl . $url;

        if (strpos($url, '?') === false) {
            $fullUrl .= "?searchID=0&maxResults={$maxResults}";
        }

        $this->info("🌐 URL: {$fullUrl}");

        // Utiliser cURL comme dans le code fourni
        $this->info("⏳ Connexion avec cURL...");

        $ch = curl_init($fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Pour les connexions HTTP
        curl_setopt($ch, CURLOPT_USERAGENT, 'KENAM-Hikvision-Client/1.0');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->error("❌ Erreur cURL: {$error}");
            return 1;
        }

        if ($httpCode !== 200) {
            $this->error("❌ Erreur HTTP {$httpCode}");
            $this->line("Response: " . $response);

            // Essayer des URLs alternatives avec cURL
            $alternativeUrls = [
                "/ISAPI/AccessControl/EventLog/Info?searchID=0&maxResults={$maxResults}",
                "/ISAPI/AccessControl/AcsEvent?searchID=0&maxResults={$maxResults}",
                "/ISAPI/System/deviceInfo",
                "/ISAPI/Event/notification/subscribe",
                "/ISAPI/AccessControl/RemoteControl/door/1",
                "/ISAPI/Intelligent/FDLib/FaceDataRecord"
            ];

            $this->info("🔄 Test d'URLs alternatives avec cURL...");
            foreach ($alternativeUrls as $altUrl) {
                $this->info("🌐 Test: {$baseUrl}{$altUrl}");

                $chAlt = curl_init($baseUrl . $altUrl);
                curl_setopt($chAlt, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($chAlt, CURLOPT_USERPWD, "{$username}:{$password}");
                curl_setopt($chAlt, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($chAlt, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($chAlt, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($chAlt, CURLOPT_SSL_VERIFYPEER, false);

                $altResponse = curl_exec($chAlt);
                $altHttpCode = curl_getinfo($chAlt, CURLINFO_HTTP_CODE);
                $altError = curl_error($chAlt);
                curl_close($chAlt);

                if ($altError) {
                    $this->line("   ❌ Erreur cURL: {$altError}");
                } elseif ($altHttpCode === 200) {
                    $this->info("✅ URL alternative fonctionnelle: {$altUrl}");
                    $fullUrl = $baseUrl . $altUrl;
                    $response = $altResponse;
                    $httpCode = 200;
                    break;
                } else {
                    $this->line("   ❌ HTTP {$altHttpCode}");
                }
            }

            if ($httpCode !== 200) {
                return 1;
            }
        }

        // Créer une réponse simulée pour compatibilité avec processResponse
        $mockResponse = new class($response, $httpCode) {
            private $body;
            private $status;

            public function __construct($body, $status) {
                $this->body = $body;
                $this->status = $status;
            }

            public function successful() {
                return $this->status === 200;
            }

            public function body() {
                return $this->body;
            }

            public function handlerStats() {
                return ['total_time' => 0];
            }
        };

        return $this->processResponse($mockResponse, "Direct ({$ip})", $fullUrl);
    }

    /**
     * Traiter la réponse XML
     */
    private function processResponse($response, $deviceName, $url)
    {
        $this->info("✅ Connexion réussie!");
        $this->line("⏱️  Temps de réponse: " . ($response->handlerStats()['total_time'] ?? 'N/A') . "s");

        try {
            $xml = simplexml_load_string($response->body());

            if (!$xml) {
                $this->error("❌ Impossible de parser le XML");
                $this->line("Raw response: " . substr($response->body(), 0, 500));
                return 1;
            }

            // Détecter le type de réponse
            $eventType = 'unknown';
            $events = [];

            if (isset($xml->AcsEventInfo)) {
                $eventType = 'AcsEvent';
                $events = $xml->AcsEventInfo;
            } elseif (isset($xml->EventLogInfo)) {
                $eventType = 'EventLog';
                $events = $xml->EventLogInfo;
            }

            $this->info("📋 Type d'événements: {$eventType}");
            $this->info("📊 Nombre d'événements trouvés: " . count($events));

            if (count($events) === 0) {
                $this->warn("⚠️  Aucun événement trouvé");
                return 0;
            }

            // Afficher les événements
            $this->table(
                ['ID', 'Employé', 'Nom', 'Heure', 'Type', 'Direction', 'Confiance'],
                collect($events)->take(10)->map(function ($event) use ($eventType) {
                    if ($eventType === 'AcsEvent') {
                        return [
                            (string) ($event->id ?? 'N/A'),
                            (string) ($event->employeeNoString ?? 'N/A'),
                            (string) ($event->name ?? 'N/A'),
                            (string) ($event->time ?? 'N/A'),
                            (string) ($event->eventType ?? 'N/A'),
                            (string) ($event->door ?? 'N/A'),
                            (string) ($event->faceScore ?? 'N/A') . '%'
                        ];
                    } else {
                        return [
                            (string) ($event->id ?? 'N/A'),
                            (string) ($event->employeeNoString ?? 'N/A'),
                            (string) ($event->name ?? 'N/A'),
                            (string) ($event->time ?? 'N/A'),
                            (string) ($event->majorEventType ?? 'N/A'),
                            (string) ($event->inAndOutFlag ?? 'N/A'),
                            (string) ($event->faceScore ?? 'N/A') . '%'
                        ];
                    }
                })->toArray()
            );

            // Statistiques
            $this->newLine();
            $this->info("📈 Statistiques:");
            $this->line("   • URL utilisée: {$url}");
            $this->line("   • Device: {$deviceName}");
            $this->line("   • Total événements: " . count($events));
            $this->line("   • Taille réponse: " . number_format(strlen($response->body()) / 1024, 2) . " KB");

            // Sauvegarder en option
            if ($this->confirm('💾 Sauvegarder les événements dans un fichier JSON?', false)) {
                $filename = "hikvision_events_" . date('Y-m-d_H-i-s') . ".json";
                $filePath = storage_path("app/{$filename}");

                $jsonData = [
                    'device' => $deviceName,
                    'url' => $url,
                    'fetched_at' => now()->toISOString(),
                    'event_type' => $eventType,
                    'total_events' => count($events),
                    'events' => json_decode(json_encode($events), true)
                ];

                file_put_contents($filePath, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $this->info("✅ Fichier sauvegardé: {$filename}");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Erreur traitement XML: " . $e->getMessage());
            $this->line("Response preview: " . substr($response->body(), 0, 200));
            return 1;
        }
    }
}
