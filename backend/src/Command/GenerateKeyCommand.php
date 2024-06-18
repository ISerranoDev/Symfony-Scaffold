<?php

namespace App\Command;

use App\Utils\Classes\EncryptService;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:generate-key',
    description: 'Generate a key for encrypt your data',
)]
class GenerateKeyCommand extends Command
{

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        parent::__construct('generate-key');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if($this->filesystem->exists(EncryptService::KEY_FILE_PATH)){
            $io->error('The encryption key already exists!');
            return Command::FAILURE;
        }

        $encKey = KeyFactory::generateEncryptionKey();
        if(!is_dir(EncryptService::KEY_FOLDER_PATH)){
            mkdir(EncryptService::KEY_FOLDER_PATH);
        }
        KeyFactory::save($encKey, EncryptService::KEY_FILE_PATH);

        $io->success('Encryption Key generated successfully.');

        return Command::SUCCESS;
    }
}
