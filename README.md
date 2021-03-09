# Install

For installing the library, run:

` composer require seriyyy95/tail `

# Examples 

```
$filename = "./file.txt"; 
$numLines = 5;
 
$tail = new Tail($filename, $numLines); 
$tail->onReadLine(function($line){ 
        print_r($line . "\n");   
}); 
$tail->run();
```

