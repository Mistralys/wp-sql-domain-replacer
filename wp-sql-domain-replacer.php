<?php
    // --------------------------------------------
    // SQL DOMAIN NAME REPLACEMENT SCRIPT
    // --------------------------------------------
    // Can be used to move the application to a 
    // new domain name, by replacing all instances
    // of the old domain name in an SQL dump file.
    // 
    // This is done safely, by replacing the domain
    // name in serialized arrays first, so they do
    // not get corrupted.
    //
    // Note: Call this from the command line.
    //
    // The output file is called output-domain.ext.sql


    // --------------------------------------------
    // CONFIGURATION
    // --------------------------------------------
    
    $importFile = 'source.sql'; // the dump file to load the SQL from
    $targetScheme = 'http'; // the HTTP scheme to use for the new domain (http/https)
    $fromDomain = 'source-domain.com'; // the old domain name to search for
    $toDomain = 'target-domain.com'; // the new domain name to use

    
    // --------------------------------------------
    // DO NOT CHANGE ANYTHING BELOW
    // --------------------------------------------

    ini_set('memory_limit', '900M');
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    if(!file_exists($importFile)) {
        die('ERROR: Input SQL file ['.basename($importFile).'] not found.');
    }
    
    $outputFile = 'output-'.$toDomain.'.sql';
    
    $searches['http://'.$fromDomain] = $targetScheme.'://'.$toDomain;
    $searches['https://'.$fromDomain] = $targetScheme.'://'.$toDomain;
    $searches[$fromDomain] = $toDomain;
    
    header('Content-Type:text/plain');
        
    $sql = file_get_contents($importFile);
        
    $searchCount = count($searches);
    
    foreach($searches as $search => $replace) 
    {
        echo 'Replace: '.$search.' ==> '.$replace.PHP_EOL;
        
        $diff = strlen($replace) - strlen($search);
        
        $regex = '%s:([0-9]+):"'.preg_quote($search).'(.*)"%siU';
        preg_match_all($regex, $sql, $result, PREG_PATTERN_ORDER);

        $total = count($result[0]);
        for($j=0; $j < $total; $j++) 
        {
            $fullMatch = $result[0][$j];
            $number = $result[1][$j];
            
            $newText = str_replace($search, $replace, $fullMatch);
            
            $length = $number + $diff;
            
            $newText = str_replace('s:'.$number, 's:'.$length, $newText);
            
            $sql = str_replace($fullMatch, $newText, $sql);
            
            echo 'Serialized text replaced: '.$newText.PHP_EOL;
        }
    }
    
    $sql = str_replace(array_keys($searches), array_values($searches), $sql);
    
    file_put_contents($outputFile, $sql);
    
    echo PHP_EOL;
    
    die('SQL Converted into file '.$outputFile.'.');
