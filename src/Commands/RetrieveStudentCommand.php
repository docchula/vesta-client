<?php

namespace Docchula\VestaClient\Commands;

use Docchula\VestaClient\VestaClient;
use Illuminate\Console\Command;

class RetrieveStudentCommand extends Command
{
    public $signature = 'vesta-client:retrieve-student {identifier}';

    public $description = 'Retrieve student info from Vesta by identifier (for test)';

    public function handle(): int
    {
        $identifier = $this->argument('identifier');
        $this->comment('Retrieving student info for '.$identifier);
        $vestaClient = new VestaClient();
        $response = $vestaClient->retrieveStudent($identifier);
        if ($response->successful()) {
            $this->info('Student info retrieved successfully');
            $this->info(json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->error('Error retrieving student info: '.$response->status().' '.$response->body());
        }

        return self::SUCCESS;
    }
}
