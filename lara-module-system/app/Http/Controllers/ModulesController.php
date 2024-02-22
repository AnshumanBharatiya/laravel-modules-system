<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Facades\Module;

use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class ModulesController extends Controller
{
    // display all modules
    public function modules(){
        $modules = Module::all();
        return view('modules', compact('modules'));

    }
    
    // modules inactive functionality
    public function disable(Request $request, $moduleName)
    {
        // Call the module:disable Artisan command with the module name as an argument
        Artisan::call("module:disable $moduleName");
        // Get the output of the Artisan command
        $output = Artisan::output();
        // Flash success message to session
        $request->session()->flash('success', "$moduleName module disabled");
        return back();
    }
    
    // modules active functionality
    public function enable(Request $request, $moduleName)
    {
        Artisan::call("module:enable $moduleName");
        $output = Artisan::output();
        // Flash success message to session
        $request->session()->flash('success', "$moduleName module enabled.");
        return back();
    }

    // modules export as a zip file feature
    public function export($moduleName)
    {
        // Path to the Modules directory
        $modulesPath = base_path('Modules');

        // Path to the module to be exported
        $modulePath = $modulesPath . '/' . $moduleName;

        // Check if the module exists
        if (!File::isDirectory($modulePath)) {
            abort(404, 'Module not found');
        }

        // Create a new ZipArchive instance
        $zip = new ZipArchive;

        // Path to the temporary zip file
        $zipFilePath = storage_path("{$moduleName}.zip");

        // Create the zip file
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Add all files and directories in the module to the zip file
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($modulePath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($modulesPath) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }

            // Close the zip file
            $zip->close();
        } else {
            abort(500, 'Failed to create zip file');
        }

        // Set the headers to force download the zip file
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    // modules delete features.
    public function delete($moduleName){
        // Path to the Modules directory
        $modulePath = base_path('Modules') . '/' . $moduleName;
        // Check if the module exists
        if (!File::isDirectory($modulePath)) {
            abort(404, 'Module not found');
        }
        // Delete the module directory
        File::deleteDirectory($modulePath);
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Module deleted successfully');
    }

    // upload zip file view page.
    public function upload(){
        return view('modulesupload');
    }

    // upload a modules zip file.
    public function uploadZip(Request $request){
      // Validate the uploaded file
        $request->validate([
            'zipFile' => 'required|mimes:zip|max:2048', // Maximum file size of 2MB
        ]);

        // Retrieve the uploaded zip file
        $zipFile = $request->file('zipFile');

        // Extract the zip file contents
        $zip = new ZipArchive;
        $zipPath = $zipFile->getPathname();
        $extractPath = storage_path('app/extracted_modules');
        
        // Create a directory to store extracted modules
        if (!file_exists($extractPath)) {
            mkdir($extractPath, 0777, true);
        }

        // Extract the zip file
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return redirect()->back()->with('error', 'Failed to extract zip file');
        }

        // Move extracted modules to Modules folder
        $modulesPath = base_path('Modules');
        $extractedModules = glob($extractPath . '/*', GLOB_ONLYDIR);
        foreach ($extractedModules as $module) {
            $moduleName = basename($module);
            $moduleDestination = $modulesPath . '/' . $moduleName;

            // Check if module already exists
            if (!file_exists($moduleDestination)) {
                rename($module, $moduleDestination);
            }
        }

        foreach ($extractedModules as $module) {
            if (file_exists($module)) {
                rmdir($module);
            }
        }
        
        if (file_exists($extractPath)) {
            rmdir($extractPath);
        }

        return redirect()->back()->with('success', 'Zip file uploaded and modules extracted successfully');
    }

}
