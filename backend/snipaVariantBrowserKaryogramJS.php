<?php

require("../backend/snipaConfig.php");

$genomerelease = $_REQUEST['genomerelease'];
$annotation = $_REQUEST['annotation'];
$chr = $_REQUEST['chr'];
$type = $_REQUEST['type'];

if ($type == "functional_annotation") {
	$data = new SplFileObject($path_to_data."/annotation/".$genomerelease."/".$annotation."/karyograms/chr".$chr.".lowres.txt");
	$output = array();
	$data->seek(1); // skip header
	while (!$data->eof()) {
		$line = explode("\t",rtrim($data->current()));
			if($line[0] > 0 && $line[1] != "NA") {
				$output[] = array(round($line[0],0),round($line[1],1));
			}
		$data->next();
	}
	print(json_encode($output));
}

if ($type == "karyogram_image") {
	$karyogram = imagecreatefrompng($path_to_data."/annotation/".$genomerelease."/".$annotation."/karyograms/chr".$chr.".png");
	if ($karyogram) {
		header("Content-type: image/png");
		imagepng($karyogram);
	}
}

if ($type == "karyogram_size") {
	require_once("../backend/snipaCsvImporter.php");
	$maxposfh = new CsvImporter($path_to_data."/annotation/".$genomerelease."/".$annotation."/karyograms/chr_maxpos.txt", true, ";");
	$maxposarray = $maxposfh->get();
	$maxpos = "";
	foreach ($maxposarray as $key => $value) {
		if($value['chromosome'] == $chr) { $maxpos = $value['maxPos']; break; } 
	}
	print(utf8_encode("karyogramsize = ".$maxpos.";"));
}






/*
Bash / R-Code zum generieren der auflösungsreduzierten Maps

bash im Annotationsverzeichnis:
for i in `ls chr*.tabix.gz`; do j=`basename $i .tabix.gz`; zcat $i | cut -f2,4 > $j.fullres.txt; done

R:
for (j in c(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,"X")) {
	chr <- read.table(paste0("chr",j,".fullres.txt"), header =T)
	pixels <- 1000
	binsize <- ceiling(max(chr[,1])/pixels)

	lowres <- data.frame()
	for (i in c(1:pixels)) {
		curbin <- subset(chr, POS >= (i-1)*binsize & POS < i*binsize)
		lowres <- rbind(lowres, data.frame(POS=((i-1)*binsize+binsize/2),FUNC=mean(curbin$FUNC, na.rm=T)))
	}
	lowres[which(is.na(lowres$FUNC)),]$FUNC <- 1

	write.table(lowres, file = paste0("chr",j,".lowres.txt"), col.names=T, row.names=F, sep="\t", quote=F)
}

*/




?>