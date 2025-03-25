<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use App\Models\Backup;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;


class BackupController extends Controller
{
    public function index() {
      $data['backups'] = Backup::orderBy('id', 'DESC')->paginate(10);
      return view('admin.backup', $data);
    }

    public function store()
    {
        $filename = uniqid('backup_') . '.sql';
        $filepath = storage_path('app/public/' . $filename);
    
        // Build the command securely using an array
        $process = new Process([
            'mysqldump',
            '-u' . config('database.connections.mysql.username'),
            '-p' . config('database.connections.mysql.password'),
            config('database.connections.mysql.database')
        ]);
    
        try {
            $process->mustRun();
            
            // Save output to storage
            Storage::put('public/' . $filename, $process->getOutput());
    
            // Store backup record in the database
            Backup::create(['filename' => $filename]);
    
            Session::flash('success', 'Backup saved successfully.');
        } catch (ProcessFailedException $exception) {
            Session::flash('error', 'Backup failed: ' . $exception->getMessage());
        }
    
        return back();
    }

    public function download(Request $request) {
      return response()->download('core/storage/app/public/'.$request->filename, 'backup.sql');
    }

    public function delete($id) {
      $backup = Backup::find($id);
      @unlink('core/storage/app/public/'.$backup->filename);
      $backup->delete();

      Session::flash('success', 'Database sql file deleted successfully!');
      return back();
    }
}
