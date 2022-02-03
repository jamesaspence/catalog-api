<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use App\Models\Integration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GenerateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-token:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new API token based on the given prompts.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $integrationName = $this->ask('Integration (name):', 'discord-local');
        $integration = $this->getIntegration($integrationName);
        if (is_null($integration) && $this->confirm('Is it okay if we create this integration?')) {
            $integration = new Integration();
            $integration->name = $integrationName;
            $integration->save();
        }

        $name = $this->ask('Name:', 'api-token');
        while ($this->nameAlreadyTaken($name, $integration)) {
            $name = $this->ask('This name is already taken. Please try another:');
        }

        $description = $this->ask('Description (optional):');

        $clientId = $this->generateNewClientId();
        $token = Str::random(32);

        $apiToken = new ApiToken();
        $apiToken->integration()->associate($integration);
        $apiToken->name = $name;
        $apiToken->description = $description;
        $apiToken->client_id = $clientId;
        $apiToken->token = Hash::make($token);
        $apiToken->save();

        $this->info('Success: Credentials are below.');
        $this->info("Client id: $clientId");
        $this->info("Token: $token");
        $this->warn('This token cannot be retrieved again, so keep it safe!');
        return 0;
    }

    private function getIntegration(string $name): ?Integration
    {
        return Integration::query()
            ->where('name', '=', $name)
            ->first();
    }

    private function nameAlreadyTaken(string $name, Integration $integration): bool
    {
        return ApiToken::withTrashed()
            ->where('name', '=', $name)
            ->where('integration_id', '=', $integration->id)
            ->exists();
    }

    private function generateNewClientId(): string
    {
        $clientId = Str::random();
        if (ApiToken::withTrashed()->where('client_id', '=', $clientId)->exists()) {
            return $this->generateNewClientId();
        }

        return $clientId;
    }
}
