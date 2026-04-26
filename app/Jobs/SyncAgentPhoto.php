<?php

namespace App\Jobs;

use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SyncAgentPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public int $agentId, public string $sourceUrl) {}

    public function handle(): void
    {
        $agent = Agent::find($this->agentId);
        if (!$agent) return;

        try {
            $response = Http::timeout(30)->get($this->sourceUrl);
            if (!$response->ok()) {
                Log::warning('SyncAgentPhoto: non-OK response', [
                    'agent_id' => $this->agentId,
                    'url'      => $this->sourceUrl,
                    'status'   => $response->status(),
                ]);
                return;
            }

            $contentType = $response->header('Content-Type', '');
            $ext = match (true) {
                str_contains($contentType, 'png')  => 'png',
                str_contains($contentType, 'webp') => 'webp',
                str_contains($contentType, 'gif')  => 'gif',
                default                            => 'jpg',
            };

            $path = "agents/{$agent->id}.{$ext}";
            Storage::disk('public')->put($path, $response->body());

            // Remove a previous file with a different extension if any.
            foreach (['jpg', 'png', 'webp', 'gif'] as $other) {
                if ($other === $ext) continue;
                $stale = "agents/{$agent->id}.{$other}";
                if (Storage::disk('public')->exists($stale)) {
                    Storage::disk('public')->delete($stale);
                }
            }

            $agent->update([
                'image'            => $path,
                'photo_source_url' => $this->sourceUrl,
            ]);
        } catch (\Throwable $e) {
            Log::error('SyncAgentPhoto failed', [
                'agent_id' => $this->agentId,
                'url'      => $this->sourceUrl,
                'error'    => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
