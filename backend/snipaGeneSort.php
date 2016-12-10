<?php

# Genesort braucht ein mehrdimensionales Array mit den Keys [0] => array("START" => ..., "STOP" => ..., "NAME"...)
# PHP-Adaption des GeneSort.R Algorithmus'
function genesort($glist, $labels = true, $plotrange_x = 500000, $plotrange_x_px = 1024, $mindist = 3000) {
	$glistQueue = array();
	$glistDrawn = array();
	foreach($glist as $gene) {
		$tmparray = array(
				'START' => $gene['START'],
				'STOP' => $gene['STOP'],
				'SIZE' => $gene['STOP']-$gene['START'],
				'PLOTSTART' => $gene['START'],
				'PLOTSTOP' => $gene['STOP'],
				'PLOTSIZE' => $gene['STOP']-$gene['START'],
				'NAME' => $gene['NAME'],
				'LAYER' => 1
			);
		if (array_key_exists('ID',$gene)) { $tmparray['ID'] = $gene['ID']; }
		if (array_key_exists('STRAND',$gene)) { $tmparray['STRAND'] = $gene['STRAND']; }
		if (array_key_exists('HIGHLIGHT',$gene)) { $tmparray['HIGHLIGHT'] = $gene['HIGHLIGHT']; }
		$glistQueue[] = $tmparray;
	}
	
	# berechne die tatsächliche Breite eines Strings im Plot
	# im Gegensatz zur R-Version wird hier anstatt plotten lediglich geschätzt bzw. eine feste Breite pro Zeichen angenommen
	# siehe unten im Kommentar
	
	if ($labels) { 
		$slope = 12.86177;
		$intercept = -196.9772;
		for($i=1;$i<=count($glistQueue);$i++) {
			$j=$i-1;
			$labelchars = strlen($glistQueue[$j]['NAME']);
			$labelwidth = ($plotrange_x/$plotrange_x_px)*($labelchars*$slope) + ($labelchars*$intercept);
			$glistQueue[$j]['PLOTSIZE'] = intval(max($labelwidth,$glistQueue[$j]['PLOTSIZE']));
			$glistQueue[$j]['PLOTSTART'] = intval($glistQueue[$j]['PLOTSTART']-0.5*($glistQueue[$j]['PLOTSIZE']-$glistQueue[$j]['SIZE']));
			$glistQueue[$j]['PLOTSTOP'] = intval($glistQueue[$j]['PLOTSTOP']+0.5*($glistQueue[$j]['PLOTSIZE']-$glistQueue[$j]['SIZE']));
		}
    }
	
	
	$glistQueueLength = count($glistQueue);
	for ($a=1;$a<=$glistQueueLength;$a++) {
		#find longest gene
		$largestval = 0;
		foreach($glistQueue as $key => $val) { if ($val['PLOTSIZE'] > $largestval) { $largestval = $val['PLOTSIZE']; $largestindex = intval($key); } }
		
		# temporary list of overlaps
		$glistOl = $glistDrawn;
		
		if (count($glistOl)>0) {
			for($i=0;$i<count($glistOl);$i++) { $glistOl[$i]['OVERLAP'] = "false"; }
			
			
			# check if current gene is overlapping of the previously drawn genes
			for($j=0;$j<count($glistOl);$j++) {
				if (checkoverlap($glistQueue[$largestindex]['PLOTSTART'],$glistQueue[$largestindex]['PLOTSTOP'],$glistDrawn[$j]['PLOTSTART'],$glistOl[$j]['PLOTSTOP'],$mindist)) { $glistOl[$j]['OVERLAP'] = "true"; }
			}
			
			# get list of overlapping genes
			$glistOlTmp = array();
			foreach ($glistOl as $key => $val) { if($val['OVERLAP'] == "true") { $glistOlTmp[] = $val;} }
			$glistOl = $glistOlTmp;
			
			if (count($glistOl) > 0) {
				# find all layervalues and determine empty spot closest to base layer, otherwise add new layer
				$layerlist = array();
				foreach($glistOl as $key => $val) { if(!in_array($val['LAYER'],$layerlist)) { $layerlist[] = $val['LAYER']; } }
				sort($layerlist);
				$maxlayer = 1; foreach($glistDrawn as $key => $val){if($val['LAYER'] > $maxlayer) { $maxlayer = $val['LAYER']; } }
				$possiblelayers = range(1,$maxlayer+1);
				$glistQueue[$largestindex]['LAYER'] = min(array_diff($possiblelayers,$layerlist));
			}
		}
		
		# add to drawn list
		$glistDrawn[] = $glistQueue[$largestindex];
				
		# remove gene from queue
		unset($glistQueue[$largestindex]);
		$glistQueue = array_values($glistQueue);
	}
	
	# sort results by START ASC
	foreach($glistDrawn as $key => $row) { $start[$key] = $row['START']; }
	array_multisort($start, SORT_ASC, $glistDrawn);
	return($glistDrawn);
}

function checkoverlap($xstart,$xstop,$ystart,$ystop,$dist = 0) {
	if ($xstart <= ($ystop+$dist) && ($ystart-$dist) <= $xstop) { return(true); } else { return(false); }
}


/*
# R Version des Gen-Sorters
 
genesort2 <- function(glist, labels = TRUE, plotrange_x = 500000, plotrange_x_px = 1024, mindist = 3000, ...) {
	glist$SIZE <- glist$STOP-glist$START
	glist$PLOTSIZE <- glist$SIZE
	glist$PLOTSTART <- glist$START
	glist$PLOTSTOP <- glist$STOP
	glist$LAYER <- 1
	glist.queue <- glist
	glist.drawn <- glist[-c(1:nrow(glist)),]

	# calculate actual plot width with labels
	if (labels) { 
		png(file = "strwidth.tmp", width = plotrange_x_px, height=1000, pointsize = 30)
		plot(c(0,plotrange_x),c(0,0))
		for (i in c(1:nrow(glist.queue))) {
			labelwidth <- strwidth(glist.queue[i,'NAME'], units="user", cex=0.4) 
			glist.queue[i,'PLOTSIZE'] <- as.integer(max(labelwidth,glist.queue[i,'PLOTSIZE']))
			glist.queue[i,'PLOTSTART'] <- as.integer(glist.queue[i,'PLOTSTART']-0.5*(glist.queue[i,'PLOTSIZE']-glist.queue[i,'SIZE']))
			glist.queue[i,'PLOTSTOP'] <- as.integer(glist.queue[i,'PLOTSTOP']+0.5*(glist.queue[i,'PLOTSIZE']-glist.queue[i,'SIZE']))
		}
		dev.off()
    }
	
	
	checkoverlap <- function(xstart,xstop,ystart,ystop,dist = 0) {
		if ((xstart <= ystop+dist) && (ystart-dist <= xstop)) { return(TRUE) } else { return(FALSE) }
	}
	
	outersect <- function(x,y) { sort(c(setdiff(x,y),setdiff(y,x))) }

	for (i in c(1:nrow(glist.queue))) {
		# find longest gene
		largest <- which(glist.queue$PLOTSIZE == max(glist.queue$PLOTSIZE, na.rm=T))[1]

		# temporary list of overlaps
		glist.ol <- glist.drawn
		
		if (nrow(glist.ol)>0) {
			glist.ol$OVERLAP <- FALSE
			
			# check if current gene is overlapping of the previously drawn genes
			for (j in c(1:nrow(glist.ol))) {
				if (checkoverlap(glist.queue[largest,'PLOTSTART'],glist.queue[largest,'PLOTSTOP'],glist.drawn[j,'PLOTSTART'],glist.ol[j,'PLOTSTOP'],mindist)) { glist.ol[j,'OVERLAP'] <- TRUE }
			}
		
			# get list of overlapping genes
			glist.ol <- subset(glist.ol, OVERLAP == TRUE)
			if (nrow(glist.ol) > 0) {
				# find all layervalues and determine empty spot closest to base layer, otherwise add new layer
				layerlist <- unique(glist.ol$LAYER)
				layerlist <- layerlist[order(layerlist)]
				possiblelayers <- c(min(glist$LAYER):max(glist.drawn$LAYER+1))
				glist.queue[largest,'LAYER'] <- min(outersect(layerlist,possiblelayers))
			}
		}
			
		# add to drawn list
		glist.drawn[nrow(glist.drawn)+1,] <- glist.queue[largest,]
		
		# remove gene from queue
		glist.queue <- glist.queue[-largest,]
	}
	return(arrange(glist.drawn, START)) 
	#return(glist.drawn)
}
*/


/*
R-Code zur Berechnung der Formel für Stringweite

labels <- c("A","AB","ABC","ABCD","ABCDE","ABCDEF","ABCDEFG","ABCDEFGH")
plotrange <- seq(50000,300000,by=10000)
plotwidth <- seq(600,1200,by=10)

#labels <- c("MTND1P10")
#plotrange <- 200000;
#plotwidth <- 600


for (label in labels) {
	results <- data.frame()
		for (range in plotrange) {
			for(plotw in plotwidth) {
				png(file="tmp.png", width=plotw, height=1000, pointsize=30)
				plot(c(0,range),c(0,0))
				s <- strwidth(label, units="user", cex=0.4)
				results <- rbind(results, data.frame(RANGE=range,WIDTH=plotw,STRWIDTH=s))
				dev.off()
			}
		}


	tmpstrwidth <- results$STRWIDTH
	tmpquot <- (results$RANGE/results$WIDTH)
	slope <- lm(tmpstrwidth ~ tmpquot)$coefficients[2]
	intercept <- lm(tmpstrwidth ~ tmpquot)$coefficients[1]
	results$GUESS <- (results$RANGE/results$WIDTH)*slope + intercept
	#summary(abs(results$STRWIDTH-results$GUESS))

	cat("String length: "); cat(nchar(label)); cat("\t")
	cat("Slope: "); cat(slope); cat("\t")
	cat("Intercept: "); cat(intercept); cat("\n")
}



*/


?>
