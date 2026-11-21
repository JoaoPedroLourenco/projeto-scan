<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\Import\ImportProductsFromCsvAction;

class ImportProductsFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import-csv {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa produtos em lote via CSV integrando com Open Food Facts';

    /**
     * Execute the console command.
     */
    public function handle(ImportProductsFromCsvAction $action)
    {
        $filePath = $this->argument('file') ?? storage_path('app/products_template.csv');

        $result = $action->execute($filePath);

        $this->info("Produtos importados com sucesso! Importados: {$result['imported']} | Atualizados: {$result['updated']} | Falharam: {$result['failed']}");
    }
}
