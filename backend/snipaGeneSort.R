library("plyr")

# Aufbau snplist:
# POS SNP PVAL CMMB R2 FUNC MULTIPLE DISEASE
#
# Aufbau genelist:
# START STOP STRAND NAME 
#
# Aufbau regelementlist:
# START STOP NAME 

# genesort()
# Generiert Layerinformationen aus Gen/Regelement-Listen mit START, STOP, NAME


# neue Variante von Johannes
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
