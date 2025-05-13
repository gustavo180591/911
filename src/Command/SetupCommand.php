<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class SetupCommand extends Command
{
    protected static $defaultName = 'app:setup';
    protected static $defaultDescription = 'Configura directorios y permisos necesarios';
    
    private $params;
    private $filesystem;
    
    public function __construct(ParameterBagInterface $params, Filesystem $filesystem)
    {
        $this->params = $params;
        $this->filesystem = $filesystem;
        
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Crear directorios de carga si no existen
        $photoDir = $this->params->get('user_photos_directory');
        $docDir = $this->params->get('user_documents_directory');
        
        $this->createDirectory($photoDir, $io);
        $this->createDirectory($docDir, $io);
        
        $io->success('Directorios configurados correctamente.');
        
        return Command::SUCCESS;
    }
    
    private function createDirectory(string $dir, SymfonyStyle $io): void
    {
        if (!$this->filesystem->exists($dir)) {
            $this->filesystem->mkdir($dir, 0755);
            $io->text("Directorio creado: $dir");
        } else {
            $io->text("Directorio ya existe: $dir");
        }
    }
} 