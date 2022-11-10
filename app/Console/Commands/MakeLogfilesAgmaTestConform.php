<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeLogfilesAgmaTestConform extends Command
{
    const SALT = ';?[9D{5$iPHRcNXSb$7U*~A,mAQVJy`X(L@`9y#{RlU<=^VdGp[};"-oZ?Vn%2eq,b!<(Ll1h8p=J~SDHm%v+AnX9b7?p-Sj=}mOp0TI#OSs!8;2544W/4<gGo)ap94a';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcaster:make-logfiles-agma-test-conform';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts podcaster logfiles to AGMA test format';

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
        $pattern = storage_path('hostingstorage/logs/h/o/e/hoerspiele/archive/*.gz');
        foreach (glob($pattern) as $file) {
            $this->line("Found: $file");

            try {
                $lines = gzfile($file);
            } catch (\Exception $e) {
                $this->error("Failed: $file");
                continue;
            }

            $file = fopen(storage_path('hostingstorage/logs/h/o/e/hoerspiele/archive/' . Str::beforeLast(Str::after($file, 'archived_'), '.gz')), 'w');

            foreach($lines as $line) {
                //$this->line($line);
                $linepattern = '/^([^ ]+) ([^ ]+) ([^ ]+) \[([^\]]+)\] "(.*) (.*) (.*)" ([0-9\-]+) ([0-9\-]+) "(.*)" "(.*)" "(.*)"$/';

                if (!preg_match($linepattern, $line, $matches)) {
                    $this->error("Failed: $file, line: $line");
                    continue;
                }
                array_shift($matches);
                $hash = hash('sha256', $matches[11] . self::SALT);
                $matches[0] = $hash;
                $matches[3] = '[' . $matches[3] . ']';
                $matches[4] = '"' . $matches[4];
                $matches[6] = $matches[6] . '"';
                $matches[9] = '"' . $matches[9] . '"';
                $matches[10] = '"' . $matches[10] . '"';
                $digits = explode('.', $matches[11]);
                $digits[3] = '0';
                $matches[11] = '"' . implode('.', $digits) . '"';
                fwrite($file, implode(' ', $matches) . PHP_EOL);
            }

            fclose($file);
        }
        return 0;
    }
}
