<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class DumpStructs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:structs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'dump  structs';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dumpStructs();
        
        
        $this->comment(PHP_EOL.'---END---'.PHP_EOL);
    }
    
    protected function dumpStructs(){
        $tables = $this->getTableNames();
        
        $filename = base_path('sqls').DIRECTORY_SEPARATOR.'Auto-Dump-Structs.sql';
        $backup_filename = base_path('sqls').DIRECTORY_SEPARATOR.'Auto-Dump-Structs.bak.sql';
        
        if(file_exists($filename)){
            @copy($filename, $backup_filename);
        }
        
        $fp = fopen($filename,'w');
        
        fwrite($fp, $this->structFileHeader());
        
        foreach ($tables as $v){
            fwrite($fp, $this->tableStructTemplate($v));
        }
        fclose($fp);
    }
    
    
    protected function structFileHeader(){
        $now = date('Y-m-d H:i:s');
        $template =<<<EOF
-- ----------------------------
-- Date: $now
-- ----------------------------

SET FOREIGN_KEY_CHECKS=0;


EOF;
        return $template;
    }
    
    
    protected function tableStructTemplate($tablename){
        $createTable = $this->getTableStruct($tablename);
        $template =<<<EOF
-- ----------------------------
-- Table structure for $tablename
-- ----------------------------
DROP TABLE IF EXISTS `$tablename`;
$createTable;


EOF;
        return $template;
    }
    
    protected function getTableStruct($tablename){
        $tables = \DB::select('show create table '.$tablename);
        return $tables[0]->{'Create Table'};
    }
    
    protected function getTableNames(){
        $ret = [];
        $tables = \DB::select('show tables;');
        foreach ($tables as $v){
            $v = array_values((array)$v);
            $ret[] = $v[0];
        }
        return $ret;
    }
    
    
    
    
}
