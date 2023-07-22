<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Spatie\DbDumper\Compressors\GzipCompressor;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

use Spatie\DbDumper\Databases\MySql;


class DbBackupController extends Controller
{
    /** Download Backup file
     * @param 
   
     */
    public function DownloadBackup()
    {
   

    $databaseName = 'admin_task';
    $userName = 'root'; 
    $password = '123456'; 

    $pathToFile = storage_path('app/backup.sql'); // Define the path where you want to store the backup file
    $zipPath = storage_path('app/backup.zip'); 
    MySql::create()
        ->setDbName($databaseName)
        ->setUserName($userName)
        ->setPassword($password)
        ->dumpToFile($pathToFile);

        $zip = new ZipArchive();
         $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true;
            $zip->addFile($pathToFile, 'backup.sql');
            $zip->close();

            return response()->file($zipPath);
        
    //    return response()->json([
    //     'success'=> true,
    //    'message'=> 'file successfully download',
    //    'path'=>$pathToFile,
       
    // ]);
  

}

}