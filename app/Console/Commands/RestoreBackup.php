<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RestoreBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:restore {backup-archive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores a backup of the application';

    const BACKUP_ARCHIVE_FILETYPE = '.tar.gz';
    const PANEL_STORAGE_DIR = 'storage/app/panels';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $backupArchive = $this->argument('backup-archive');

        $reasonForInvalidity = $this->isValidArchive($backupArchive);
        if ($reasonForInvalidity) {
            echo "Aborting: given backup archive \"$backupArchive\" is $reasonForInvalidity.\n";
            return 1;
        }

        $backupDir = basename($backupArchive, self::BACKUP_ARCHIVE_FILETYPE);
        if ($this->unpackBackup($backupArchive, $backupDir)) {
            echo "Aborting: failed to unpack backup from \"$backupArchive\" to \"$backupDir\"\n";
            return 1;
        }

        if ($this->restorePanels($backupDir)) {
            echo "Aborting: failed to restore panels\n";
            return 1;
        }

        if ($this->restoreDatabase($backupDir)) {
            echo "Aborting: failed to restore database\n";
            return 1;
        }
        return 0;
    }

    protected function isValidArchive($filename) {
        if (! is_file($filename)) {
            return 'not a file';
        }
        if (! is_readable($filename)) {
            return 'not readable';
        }
        $expected_extension = self::BACKUP_ARCHIVE_FILETYPE;
        if (! str_ends_with($filename, $expected_extension)) {
            return "not an accepted archive (needs to be $expected_extension)";
        }
        return '';
    }

    protected function unpackBackup($archive, $targetDir) {
        $archive = escapeshellarg($archive);
        $targetDir = escapeshellarg($targetDir);

        echo "unpacking the backup...\n";
        exec("mkdir -p $targetDir");
        exec("tar --extract --file=$archive --directory=$targetDir", $output, $retval);
        if ($retval) {
            echo "Failed to unpack the backup:\n";
            print_r($output);
        }
        return $retval;
    }

    protected function restorePanels($archiveDir) {
        $datetime = date('Y-m-d_H-i-s');
        $overwrittenPanels = escapeshellarg("overwritten_panels.$datetime.tar.gz");
        echo "backing up the existing panels in $overwrittenPanels...\n";
        $panelDir = self::PANEL_STORAGE_DIR;
        exec("tar --create --file=$overwrittenPanels $panelDir", $output, $retval);
        if ($retval) {
            echo "Failed to make a backup of the existing panels:\n";
            print_r($output);
            return 1;
        }
        
        echo "removing the existing panels from the app storage...\n";
        exec("rm -rf $panelDir/*", $output, $retval);
        if ($retval) {
            echo "Failed to remove the existing panels:\n";
            print_r($output);
            return 1;
        }

        echo "copying the restored panels to the app storage...\n";
        $panelsInBackup = escapeshellarg("$archiveDir/var/www/html/sdash.sourcedata.io/current/$panelDir");
        exec("cp --recursive $panelsInBackup/* $panelDir", $output, $retval);
        if ($retval) {
            echo "Failed to copy the restored panels to their location:\n";
            print_r($output);
            return 1;
        }
        return 0;
    }

    protected function restoreDatabase($archiveDir) {
        echo "restoring database from dump\n";

        $dbDumps = glob("$archiveDir/home/ubuntu/backups/*.sql");
        if (! $dbDumps) {
            echo "No database dump file found!\n";
            return 1;
        }
        if (count($dbDumps) > 1) {
            echo "Multiple database dump files found in backup, selecting the first one\n";
        }
        $dbDump = escapeshellarg($dbDumps[0]);

        $dbHost = escapeshellarg($this->ask('What is the database\'s hostname?'));
        $dbUser = escapeshellarg($this->ask('What is the database user\'s name?'));
        $dbPassword = escapeshellarg($this->ask('What is the database user\'s password?'));

        $datetime = date('Y-m-d_H-i-s');
        $overwrittenData = escapeshellarg("overwritten_database.$datetime.sql");

        echo "backing up the existing database in $overwrittenData...\n";
        exec("mysqldump --host $dbHost --user $dbUser --password=$dbPassword --all-databases > $overwrittenData", $output, $retval);
        if ($retval) {
            echo "Failed to back up the existing database:\n";
            print_r($output);
            return 1;
        }

        $sdashDump = 'sdash-dump.sql';
        echo "Extracting sdash part from full database dump...\n";
        exec("(awk 'BEGIN{found=1} /Current Database: `mysql`/{found=0} /Current Database: `sdash`/{found=1} {if (found) print }' $dbDump > $sdashDump)", $output, $retval);
        if ($retval) {
            echo "Failed to extract sdash part from full dump:\n";
            print_r($output);
            return 1;
        }

        echo "Restoring database from dump...\n";
        exec("mysql --host $dbHost --user $dbUser --password=$dbPassword < $sdashDump", $output, $retval);
        if ($retval) {
            echo "Failed to restore database from dump:\n";
            print_r($output);
            return 1;
        }
        return 0;
    }
}
