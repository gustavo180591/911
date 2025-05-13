<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:remove-rejected-state',
    description: 'Elimina el estado "Rechazado" del sistema y actualiza las denuncias',
)]
class RemoveRejectedStateCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Eliminación del estado "Rechazado" del sistema');

        try {
            // Cargar el script SQL desde el archivo
            $sqlFile = file_get_contents(__DIR__ . '/../../migrations/remove_rejected_state.sql');
            if ($sqlFile === false) {
                throw new \Exception('No se pudo leer el archivo SQL');
            }

            // Ejecutar las consultas SQL
            $connection = $this->entityManager->getConnection();
            
            // Verificar si existe la tabla migration_log
            $schemaManager = $connection->createSchemaManager();
            if (!$schemaManager->tablesExist(['migration_log'])) {
                $connection->executeStatement('
                    CREATE TABLE migration_log (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        description VARCHAR(255) NOT NULL,
                        executed_at DATETIME NOT NULL
                    )
                ');
                $io->comment('Tabla migration_log creada');
            }
            
            // Dividir y ejecutar las consultas SQL
            $queries = explode(';', $sqlFile);
            $totalQueries = 0;
            
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $connection->executeStatement($query);
                    $totalQueries++;
                }
            }

            $io->success(sprintf('Se ejecutaron %d consultas correctamente', $totalQueries));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Ocurrió un error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 