<?php defined('SYSPATH') or die('No direct script access.');

class Media_Compiler_Coffee extends Media_Compiler implements Media_ICompiler {

	public function compile(array $filepaths, array $options)
	{
		$tmp_dir =  Arr::get($options, 'tmp_dir', APPPATH.'cache'.DIRECTORY_SEPARATOR.'coffee');
		
		foreach ($filepaths as $relative_path => $absolute_path)
		{
			$destination = $tmp_dir.DIRECTORY_SEPARATOR.$relative_path;
			$this->copy_file($absolute_path, $destination);
		}
		
		$save_dir = Arr::get($options, 'save_dir');
		
		$coffee_cmd = 'cd '.escapeshellarg($tmp_dir.'/'.$options['coffee_dir']).' && coffee --compile '; // compile
		$coffee_cmd .= '--output '.escapeshellarg($tmp_dir.'/'.$options['js_dir']).' .';

		exec($coffee_cmd);

		$compiled_files = Kohana::list_files($options['js_dir'], array($tmp_dir.DIRECTORY_SEPARATOR));
		$compiled_files = Arr::flatten($compiled_files);
		
		foreach ($compiled_files as $relative_path => $absolute_path)
		{
			$destination = $options['save_dir'].DIRECTORY_SEPARATOR.$relative_path;
			$this->copy_file($absolute_path, $destination, FALSE);
		}
		
		// Remove the tmp directory
		$this->delete_directory($tmp_dir);
	}
}