<?php

namespace Seriyyy95/Tail;

class Tail {

	private $filename;
	private $lines = 10;
	private $updateInterval = 200;
	private $callback = null;

	public function __construct($filename, $lines=10){
		$this->filename = $filename;
		$this->lines = $lines;
	}

	public function onReadLine($closure){
		$this->callback = $closure;
	}

	public function run(){
		if($this->callback == null){
			throw new \Exception("Callback function is not set!");
		}
		$closure = $this->callback;
		$size = filesize($this->filename);
		$offset = $this->findStartOffset($size);
		while(true){
			$data = $this->readLines($offset);
			foreach($data["lines"] as $line){
				$closure($line);
			}
			$offset = $data["offset"];
			sleep(1);
		}

	}
	
	protected function readLines($offset){
		$file = fopen($this->filename, "r");
		if($file == FALSE){
			throw new \Exception("Can`t open file {$this->filename}");
		}
		clearstatcache(false, $this->filename);
		$size = filesize($this->filename);
		if($offset > $size){
			$offset = $size;
		}
		rewind($file);
		$lines = array();
		$currentLine = "";
		$loop = true;
		while($offset < $size){
			$result = fseek($file, $offset, SEEK_SET);
			$char = fgetc($file);
			if($char == PHP_EOL){
				$lines[] = $currentLine;
				$currentLine = "";
			}else{
				$currentLine .= $char;	
			}
			$offset++;
	
		}
		fclose($file);
		return array("lines" => $lines, "offset" => $offset);
	}

	protected function findStartOffset($size){
		$file = fopen($this->filename, "r");
		if($file == FALSE){
			throw new \Exception("Can`t open file {$this->filename}");
		}
		$pos = -1;
		$count = 0;
		do {
			$char = fgetc($file);
			if($char == PHP_EOL){
				$count++;
			}
			$pos--;
			$result = fseek($file, $pos, SEEK_END);

		} while($result !== -1 && $count < $this->lines);
		//От размера отнять текущую позицию плюс размер \n
		$offset = ($size + $pos) +2;
		fclose($file);
		return $offset;
	}

}
