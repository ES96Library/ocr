<?php
// Script to run local optical character recognition on images stored within hollre.com database
// This script will be run following image upload after a short delay

// This code requires tesseract-ocr, an OCR engine that can be downloaded at http://code.google.com/p/tesseract-ocr/downloads/list
// This code must still have error checking done for non-standard cases
 
	
	//get item data
	$data = file_get_contents("http://hollre.com/items.json"); 
	$json = json_decode($data, TRUE);	

	//loop thorugh each item to find image attribute
	foreach ($json["item"] as $number => $item)
		foreach ($item[1] as $key => $val)
			if ($key == "image")
			{
				//get image url
				$image = file_get_contents($val);

				//divide the url by "/" in order to get the jpg name stored by amazon
				$url = explode("/",$val);
				$jpg = explode(".", $url[6]);
				
				//some items had "jpg" attached to the end of the jpg name, while others did not. "jpg" was removed for those items
				if (substr("$jpg[0]", -3) == "jpg")
					$jpg[0] = substr_replace($jpg[0] ,"", -3);
				
				//the jpg name was used to create an individual location for the image, this location should be specific to the computer being used
				//the image file can be a temp file
				$location = "C:\\Users\\Stacey\\Documents\\ES96\\Images\\" . $jpg[0];
				
				//attach the .jpg file extension, necessary for the teseract program
				$fjpg = $location	. ".jpg"; 
				
				//open the file and save the image in it
				$file = fopen($fjpg, 'w');
				$saveimage = fwrite($file, $image);
				fclose($file);
				
				//create a file for the ocr text to be saved to
				$ftxt = $location . "txt";
				
				//run tesseract on the image file 
				exec("tesseract $fjpg $location");
				
				//get the OCR text 
				$OCR = file_get_contents($location . ".txt");
				
				//in the final version this should be a POST command, saving the OCR in the server as an attribute
				echo $OCR;
				
				//it may also be valuable to have an OCR attribute indicating whether OCR has been run yet
			}	
				
?>