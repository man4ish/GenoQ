library("rCharts")
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

extract <- function(x) { e <- ifelse(x == 0, 0, floor(log10(x))); m <- x/10^e; list(mantissa = m, exponent = e); }


# alte Variante, nach Ann-Kristins Sortiermethode
genesort1 <- function(inlist, labels = TRUE, plotrange_x = 500000, plotrange_x_px = 1024, ...) {
    
    inlist[,'START']<-as.numeric(inlist[,'START'])
    inlist[,'STOP']<-as.numeric(inlist[,'STOP'])
   
    if(nrow(inlist)==1){
      inlist <- t(as.matrix(inlist[order(as.numeric(inlist[,'STOP'])),]))
      inlist <- t(as.matrix(inlist[order(as.numeric(inlist[,'START'])),]))
      inlist <- t(as.matrix(inlist[duplicated(inlist)==F,]))} else {
      inlist <- as.matrix(inlist[order(as.numeric(inlist[,'STOP'])),])
      inlist <- as.matrix(inlist[order(as.numeric(inlist[,'START'])),])
      inlist <- as.matrix(inlist[duplicated(inlist)==F,])
      }
    
    k <- 1
    klasse <- rep(-5, times=nrow(inlist))
    klasse[1] <- k
    if(nrow(inlist)>=2)
    {
      for(bla in 2:nrow(inlist))
      {
        if(inlist[,'NAME'][bla]==inlist[,'NAME'][bla-1]){klasse[bla] <- k}
        else{k <- k+1
             klasse[bla] <-k}
      }
      inlist.sections <- matrix(-5, nrow=k,ncol=ncol(inlist))
	  colnames(inlist.sections) <- colnames(inlist)
      for(bla in 1:k)
      {
        inlist.sections[bla,'START'] <- min(as.numeric(inlist[klasse==bla,'START']))
        inlist.sections[bla,'STOP'] <- max(as.numeric(inlist[klasse==bla,'STOP']))
        inlist.sections[bla,'NAME'] <- levels(as.factor(as.character(inlist[klasse==bla,'NAME'])))
      }
      inlist <- as.data.frame(inlist.sections)
      inlist[,'START']<- as.numeric(as.character(inlist[,'START']))
      inlist[,'STOP']<- as.numeric(as.character(inlist[,'STOP']))
    }
    
	print("inlist.sections")
	print(inlist.sections)
    

    
    
    lab.width <- 0
    if (labels) { png(file = "strwidth.tmp", width = plotrange_x_px, height=1000, pointsize = 30)
	              plot(c(0,plotrange_x),c(0,0))
                  lab.width <- strwidth(inlist[,'NAME'], units="user", cex=0.4) 
                  dev.off()
                 
                 }
    STOP_hyp <- as.numeric(inlist[,'START'])+2*lab.width
	print("STOP_hyp")
	print(STOP_hyp)
    STOP_max <- rep(1,length(inlist[,'START']))
    	
	for(bla in 1:length(inlist[,'START']))
    {
      STOP_max[bla] <- as.numeric(max(as.numeric(inlist[,'STOP'][bla]), STOP_hyp[bla])) # mindestens die Länge des Namens
    }
	print("STOP_max")
	print(STOP_max)
    if(nrow(inlist)>=2)
    {
      diff <- rep(0,nrow(inlist))       # Differenz der Starposition und der Stopposition des vorherigen (positiv -> Überlappung)
      # Ueberlappung zwischen zwei Genen
      for(bla in 2:nrow(inlist))
      {
        diff[bla]<- STOP_max[bla-1]-inlist[,'START'][bla]   # wenn positiv, dann ueberlappen sich die gene
      }
	  print("diff")
	  print(diff)
      k<-1
      # Zaehler der Ueberlappungen
      zaehler_ueberlap <- rep(1,nrow(inlist)) # zählt tatsächlich Überlappungen
      for (bla in 2:nrow(inlist))
      {
        if(diff[bla]>0)
        {
          k <- k+1                    # je mehr ueberlappungen desto mehr stufen
          zaehler_ueberlap[bla] <- k
        }else{
          k=1
          zaehler_ueberlap[bla] <- k
        }
      }
	  
	  print("zaehler_ueberlap")
	  print(zaehler_ueberlap)
	  
      max_k       <- max(zaehler_ueberlap)        # höchste Stufe, die vorkommt
      if(max_k > 2)
      {
        haeuf_k     <- table(zaehler_ueberlap)
        haeuf_max_k <- haeuf_k[max_k]               # anzahl höchste Stufe
        stellen <- rep(0,haeuf_max_k)
        bla=0
        for(j in 1:length(zaehler_ueberlap))
        {
          if( zaehler_ueberlap[j]==max_k)
          {
            bla=bla+1
            stellen[bla]=j
          }
        }
        # schleife über alle Vorkommen der höchsten Stufe
        max_k_neu <- rep(max_k,haeuf_max_k)
        for(j in 1:haeuf_max_k)
        {
          stelle <- stellen[j]
          for(l in (max_k-1):2)
          {
            if(STOP_max[stelle-l]-as.numeric(inlist[,'START'][stelle]) < 0){max_k_neu[j] <- max_k_neu[j] -1}
          }
        }
        # höchsten 8 Stufen sonst zu eng
        anzahl_stufen <- min(max(max_k_neu),8)
      }else{
        anzahl_stufen <- min(max(zaehler_ueberlap),8)
      }
      zaehler_stufen <- seq(1,anzahl_stufen)                             # vorkommende Stufen
      zaehler        <- rep(zaehler_stufen,length.out=length(zaehler_ueberlap))  # abwechselnd diese Stufen eintragen
      
    }else{
      # falls nur eine Stufe vorkommt
      anzahl_stufen <- 1
      zaehler       <- 1
    }

    
    # Layer für highcharts
    inlist$LAYER <- NA

    for( bla in 1:nrow(inlist) )
    {
      for(ebene in 1:anzahl_stufen)
      {
        if(zaehler[bla]==ebene)
        { inlist[bla,'LAYER'] <- ebene }
      }
    }
    
    return(inlist)
}

# neue Variante von Johannes
genesort2 <- function(glist, labels = TRUE, plotrange_x = 500000, plotrange_x_px = 1024, mindist = 3000, ...) {
	glist$SIZE <- glist$STOP-glist$START
	glist$PLOTSIZE <- glist$SIZE
	glist$PLOTSTART <- glist$START
	glist$PLOTSTOP <- glist$STOP
	glist$LAYER <- 1
	glist.queue <- glist
	glist.drawn <- glist[-c(1:nrow(glist)),]
	print("GLIST:")
	print(summary(glist.queue))

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
}





locus.plot.classic <- function(querysnp, chr, snplist, genelist,cnvlist,cpglist) {
  
  write.table(cnvlist,file="/home/metabolomics/snipa/web/backend/Rcnvlilst.txt",sep="\t")
  write.table(cpglist,file="/home/metabolomics/snipa/web/backend/Rcpglist.txt",sep="\t")
  write.table(genelist,file="/home/metabolomics/snipa/web/backend/Rgenelist.txt",sep="\t")

  
  # Query-SNP
  hit <- subset(snplist, SNP == querysnp)
  #snplist[which(is.na(snplist$R2)),'R2'] <- 0;
  
  #
  # size of the region
  min.pos <- min(snplist$POS) # - 10000
  max.pos <- max(snplist$POS) # + 10000
  size.pos <- max.pos - min.pos
  center.pos <- min.pos + ( size.pos / 2 )
  center.100kb.pos <- round(center.pos / 100000) * 100000
  offset.100kb.pos <- round((size.pos/3) / 100000) * 100000
  
  range <- 1*1.2

  # range of y-axis
  # this dedicates 33% of the yaxis to the genes, labels, recomb rate
  offset <- ( range * 4 / 3 ) - range
  big.range <- range + offset 
  
  ystart.gene <- - offset
  ystart.recomb <- - offset + (big.range / 8)

  # recombination rate 
  recomb <- subset(snplist, select=c("POS","CMMB"))
 
  # erstelle Spalte mit Symbolzahlen
  sym.unknown <- 21
  sym.trans.indir <- 22
  sym.reg.indir <- 23
  sym.reg.dir <- 24
  sym.trans.dir <- 25
  
  sym.multi.color <- "#006611" #  für multiple effekte
  sym.disease.color <- "#003399" #  für multiple effekte
  
  
  # SNP Gruppen
  
  snplist$SYMBOL <- NA;
  snplist$SYMBOL[which(snplist$FUNC == 1)] <- sym.unknown
  snplist$SYMBOL[which(snplist$FUNC == 2)] <- sym.trans.indir
  snplist$SYMBOL[which(snplist$FUNC == 3)] <- sym.reg.indir
  snplist$SYMBOL[which(snplist$FUNC == 4)] <- sym.reg.dir
  snplist$SYMBOL[which(snplist$FUNC == 5)] <- sym.trans.dir
    



  # default Farbe
  snplist$COLOR <- NA
  # nach r2 einfaerben
  colgradfun1 <- colorRampPalette(c("yellow","red"))
  colgrad <- c(rep("#EEEEEE",20),colgradfun1(60),rep("#FF0000",21))
  colgradfun2 <- function(x) { y <- floor(x*100)+1; return(colgrad[y]) }
  snplist$COLOR <- unlist(lapply(snplist$R2, function(x) colgradfun2(x)))
  snplist[which(is.na(snplist$COLOR)),'COLOR'] <- "#EEEEEE"
  
  # Randfarbe und -stärke für multiple Hits sowie disease Associated
  snplist$BORDERCOLOR <- "#222222"
  snplist[which(snplist$MULTIPLE == 1),'BORDERCOLOR'] <- sym.multi.color
  snplist[which(snplist$DISEASE == 1),'BORDERCOLOR'] <- sym.disease.color
  snplist$BORDERWIDTH <- 1
  snplist[which(snplist$MULTIPLE == 1),'BORDERWIDTH'] <- 1.5
  snplist[which(snplist$DISEASE == 1),'BORDERWIDTH'] <- 2
   
  # Sortiere Liste aufsteigend nach Priorität (Disease, Multiple, dann Funktion)
  snplist$PRIORITY <- snplist$FUNC
  snplist[which(snplist$MULTIPLE == 1),'PRIORITY'] <- 10
  snplist[which(snplist$DISEASE == 1),'PRIORITY'] <- 11
  
  snplist <- arrange(snplist,PRIORITY)

   
  # genotyped markers
  snplist[(which(is.na(snplist$R2))),'R2'] <- 0;
  markers.in.strong.ld <- subset(snplist, (row.names(snplist) != querysnp & snplist$R2 >= 0.8))
  markers.in.moderate.ld <- subset(snplist, (row.names(snplist) != querysnp & snplist$R2 >= 0.5))
  markers.in.weak.ld <- subset(snplist, (row.names(snplist) != querysnp & snplist$R2 >= 0.1))
  #markers.not.in.ld <- subset(snplist, (row.names(snplist) != querysnp & snplist$R2<0.2))
  
  # Query-SNP
  hit <- subset(snplist, SNP == querysnp)
  
  plot(0,0, type="n", axes=FALSE, xlab="", ylab="")
  box()
  layout(matrix(c(1,2,3,4,5), 5,1, byrow=TRUE), heights=c(5,3,3,2))
  par(mar=c(0,2,2,2))

  # start plot with recombination rate (in background)
  plot(recomb[,1], ystart.recomb + ( ( recomb[,2] / 60 ) * ( 6 * big.range / 8 )), type="l", col="lightblue", lwd=1, xlim=c(min.pos, max.pos), ylim=c(-offset,offset+1), xlab="", ylab="", axes=F)
  
  # axes, titles and legends
  axiscex <- 0.9
    
  #axis(1, at=c(center.100kb.pos - 2*offset.100kb.pos, center.100kb.pos - offset.100kb.pos, center.100kb.pos, center.100kb.pos + offset.100kb.pos, center.100kb.pos + 2*offset.100kb.pos), labels=c((center.100kb.pos - 2*offset.100kb.pos) / 1e6, (center.100kb.pos - offset.100kb.pos) / 1e6, center.100kb.pos /1e6, (center.100kb.pos + offset.100kb.pos) / 1e6, (center.100kb.pos + 2*offset.100kb.pos) / 1e6), las=1, cex.axis=0.8, cex=axiscex) 
  #mtext(paste("Chromosome ", chr, " (Mb)", sep=""), side=1, line=2.5, cex=0.7)
    
  axis(2, at=seq(0,range,ceiling(max(range,10)/10)), labels=seq(0,range,ceiling(max(range,10)/10)), las=1, cex=axiscex, cex.axis=0.8)
  mtext(expression("LLinkage disequilibrium" ~ (r^2)), side=2, at=(range/2), line=2, cex=0.3)
  
  axis(4, at=c( ystart.recomb, ystart.recomb + (big.range / 4), ystart.recomb + ( 2 * big.range / 4), ystart.recomb + ( 3 * big.range / 4 ) ), labels=c("0","20","40","60"), las=1, cex.axis=0.8, cex=axiscex)
  #mtext("RRecombination rate (cM/Mb)", side=4, line=2, cex=0.7, at=(-offset+big.range/2))
 
  #box()
  lines(c(min.pos, max.pos), c(0,0), lty="dotted", lwd=1, col="black")
  
  
  # plot the markers
  #points(markers.not.in.ld$POS, markers.not.in.ld$R2, pch=markers.not.in.ld$SYMBOL, cex=1.0, lwd=markers.not.in.ld$BORDERWIDTH, bg=markers.not.in.ld$COLOR, col=markers.not.in.ld$BORDERCOLOR)
  points(markers.in.weak.ld$POS, markers.in.weak.ld$R2, pch=markers.in.weak.ld$SYMBOL, cex=1.25, lwd=markers.in.weak.ld$BORDERWIDTH, bg=markers.in.weak.ld$COLOR, col=markers.in.weak.ld$BORDERCOLOR)
  points(markers.in.moderate.ld$POS, markers.in.moderate.ld$R2, pch=markers.in.moderate.ld$SYMBOL, cex=1.25, lwd=markers.in.moderate.ld$BORDERWIDTH, bg=markers.in.moderate.ld$COLOR, col=markers.in.moderate.ld$BORDERCOLOR)
  points(markers.in.strong.ld$POS, markers.in.strong.ld$R2, pch=markers.in.strong.ld$SYMBOL, cex=1.25, lwd=markers.in.strong.ld$BORDERWIDTH, bg=markers.in.strong.ld$COLOR, col=markers.in.strong.ld$BORDERCOLOR)
  
 
  # bester Hit
  points(hit$POS[1], hit$R2[1], pch=hit$SYMBOL[1], cex=1.5, bg="blue", col=hit$BORDERCOLOR[1], lwd=hit$BORDERWIDTH[1])
  text(hit$POS[1], hit$R2[1], 
		paste(hit$SNP[1]),
		pos=3, offset=1.4, cex=0.8)
    
  # plot the genes
  if (nrow(genelist) > 0) {      
          
		  if (!("LAYER" %in% colnames(genelist))) { genelist$LAYER <- 1 }
		  
		  genelistpos <- subset(genelist, STRAND == "+")
		  genelistneg <- subset(genelist, STRAND == "-")
		  
		  cat(paste(),sep="\n")
          if (nrow(genelistpos) > 0) { arrows(genelistpos$START, genelistpos$LAYER*(offset*0.15)-offset, genelistpos$STOP, genelistpos$LAYER*(offset*0.15)-offset, length=0.05, lwd=2, code=2, lty="solid", col="darkgreen") }
		  if (nrow(genelistneg) > 0) { arrows(genelistneg$START, genelistneg$LAYER*(offset*0.15)-offset, genelistneg$STOP, genelistneg$LAYER*(offset*0.15)-offset, length=0.05, lwd=2, code=1, lty="solid", col="darkgreen") }

          # kappe für die Beschriftung zu lange gene bei min.pos und max.pos
		  if (length(which(genelist$START < min.pos)) > 0) { genelist[which(genelist$START < min.pos),]$START  <- min.pos }
		  if (length(which(genelist$STOP > max.pos)) > 0) { genelist[which(genelist$STOP > max.pos),]$STOP  <- max.pos }
		  
		  text(genelist$START+0.5*(genelist$STOP-genelist$START), genelist$LAYER*(offset*0.15)-offset, labels=genelist$NAME, pos=3, offset=0.15, cex=0.6, col="black")
   }

  
  # legende für LD Farben
	  legend("topleft",c("sentinel SNP","not in LD",expression("LD"~r^2 >= 0.2),expression("LD"~r^2 >= 0.5),expression("LD"~r^2 >= 0.8)), fill=c("blue","#EEEEEE","yellow","orange","red"), cex=0.6)
  # plot the cnv
   par(mar=c(0,2,0,2))
   #plot(x=c(1,2,3),y=c(1,2,3), type="n", axes=FALSE, xlab="", ylab="")
   plot(recomb[,1], ystart.recomb + ( ( recomb[,2] / 60 ) * ( 6 * big.range / 8 )),xlab="", ylab="",ylim=c(-.4,-.1),axes=F,type="n")
   if (nrow(cnvlist) > 0) {      
          
		  if (!("LAYER" %in% colnames(cnvlist))) { cnvlist$LAYER <- 1 }
		  
		  cnvlistpos <- subset(cnvlist, STRAND == "+")
		  cnvlistneg <- subset(cnvlist, STRAND == "-")
		  
		  
          if (nrow(cnvlistpos) > 0) { arrows(cnvlistpos$START, (cnvlistpos$LAYER*(offset*0.15)-offset), cnvlistpos$STOP, (cnvlistpos$LAYER*(offset*0.15)-offset), length=0.05, lwd=2, code=2, lty="solid", col="red") }
		  if (nrow(cnvlistneg) > 0) { arrows(cnvlistneg$START, (cnvlistneg$LAYER*(offset*0.15)-offset), cnvlistneg$STOP, (cnvlistneg$LAYER*(offset*0.15)-offset), length=0.05, lwd=2, code=1, lty="solid", col="red") }

          # kappe für die Beschriftung zu lange gene bei min.pos und max.pos
		  if (length(which(cnvlist$START < min.pos)) > 0) { cnvlist[which(cnvlist$START < min.pos),]$START  <- min.pos }
		  if (length(which(cnvlist$STOP > max.pos)) > 0) { cnvlist[which(cnvlist$STOP > max.pos),]$STOP  <- max.pos }
		  
		  text(cnvlist$START+0.5*(cnvlist$STOP-cnvlist$START), cnvlist$LAYER*(offset*0.15)-offset, labels=cnvlist$NAME, pos=3, offset=0.15, cex=0.6, col="black")
   }

  # plot the cpg  
   par(mar=c(0,2,0,2))
   data<-cpglist
   cpgplot <- barplot(data$V4,ylim=c(0,1),border="#ffb31a",col=c("#ffb31a"),space=5,width = .1)
   arrows(x0=cpgplot,y0=data$V5,y1=data$V6,angle=90,code=3,length = 0.01)
 
   axis(1, at=cpgplot, labels=data$V2, las=1, cex.axis=0.8, cex=axiscex) 
   #mtext(paste("Chromosome ", chr, " (Mb)", sep=""), side=1, line=2.5, cex=0.7)	  
	 
   # dummy-Plot für Legende unterhalb des Plots 
   
   par(mar=c(2,2,2,2))
   plot(0,0, type="n", axes=FALSE, xlab="", ylab="")
	  

          
    # legende für Punktformen
    legend("top",c("recombination rate","transcript","unknown effect","direct effect on transcript","putative effect on transcript","direct regulatory effect","putative regulatory effect","multiple effects","associated with trait","CNV","CPG"), pch=c(NA,NA,sym.unknown,sym.trans.dir,sym.trans.indir,sym.reg.dir,sym.reg.indir,sym.unknown,sym.unknown,24,18), col=c("lightblue","darkgreen",rep("black",5),sym.multi.color,sym.disease.color,"red","#ffb31a"), cex=0.6, lwd=c(3,3,rep(1,5),3,3), lty=c(1,1,rep(0,9)),ncol=3)
}
 

#locus.plot.highcharts <- function(querysnp, chr, snplist, genelist, regellist) 
locus.plot.highcharts <- function(querysnp, chr, snplist, genelist, regellist, cnvlist)
{   
   # write.table(cnvlist,file="/home/metabolomics/snipa/web/backend/cnvlilst.txt",sep="\t")
   # write.table(genelist,file="/home/metabolomics/snipa/web/backend/genelist.txt",sep="\t")
   # Plotdaten aus der SNPliste extrahieren
   snps <- data.frame(x = numeric(length(snplist$POS)))
   snps$x <- snplist$POS
   snps$y <- snplist$R2
   snps$snpname <- snplist$SNP
   snps$group <- snplist$FUNC
   snps$cmmb <- snplist$CMMB

   # X-Achse Limits:
   plotxmin <- min(snps$x, na.rm=TRUE)
   plotxmax <- max(snps$x, na.rm=TRUE)

   # set Plotranges
   plotmax <- 1*1.2

   # Lower limit : 35 % of Plotrange under y = 0 for CMMB -Plot
   plotmin <- -0.35*plotmax

   # Pro Layer in the gene list 7.5 % down , 50% distance from the CMMB plot
   numgenelayers <- 0
   if (nrow(genelist) > 0) {      
	if (!("LAYER" %in% colnames(genelist))) { genelist$LAYER <- 1 }
	numgenelayers <- length(levels(factor(genelist$LAYER)))
        
   }

   if (numgenelayers > 0) { 
	genelist$LAYER <- plotmin-0.01*plotmax-0.075*plotmax*genelist$LAYER
	plotmin <- plotmin-0.01*plotmax-0.075*plotmax*numgenelayers 
   }

   # Pro Layer in regulating element list 50 % down , 20 % distance to the genes
   numregellayers <- 0
   if (nrow(regellist) > 0) {  
	if (!("LAYER" %in% colnames(regellist))) { regellist$LAYER <- 1 }
	numregellayers <- length(levels(factor(regellist$LAYER)))        
   }
   
   if (numregellayers > 0) { regellist$LAYER <- plotmin-0.02*plotmax-0.05*plotmax*regellist$LAYER
        plotmin <- plotmin-0.02*plotmax-0.05*plotmax*numregellayers 
   }

   # Pro Layer in CNV element list 50 % down , 10 % distance to the regullatory genes

   #qonecnvlist<-genelist

   qonecnvlist<-cnvlist
   
   numqonecnvlayers <- 0
   
   if (nrow(qonecnvlist) > 0) { 
	if (!("LAYER" %in% colnames(qonecnvlist))) { qonecnvlist$LAYER <- 1 }
	numqonecnvlayers <- length(levels(factor(qonecnvlist$LAYER)))           
   }

   if (numqonecnvlayers > 0) { qonecnvlist$LAYER <- plotmin-0.05*plotmax-0.1*plotmax*qonecnvlist$LAYER
                          plotmin <- plotmin-0.05*plotmax-0.1*plotmax*numqonecnvlayers 
   }

	
  #20% space for methylation plot

  plotmin <- plotmin-0.2*plotmax
			
  # Ending with another 5% distance down
  plotmin <- plotmin-0.05*plotmax

  plotrange <- -plotmin + plotmax

  # tickmarks für p-Werte
  pvalticks <- seq(from = 0, to = 1.0, by = 0.2)


  # SNPs einfärben
  # default Farbe
  snps$color <- NA
  # nach r2 einfaerben
  colgradfun1 <- colorRampPalette(c("yellow","red"))
  colgrad <- c(rep("#EEEEEE",20),colgradfun1(60),rep("#FF0000",21))
  colgradfun2 <- function(x) { y <- floor(x*100)+1; return(colgrad[y]) }

  snps$color <- unlist(lapply(snps$y, function(x) colgradfun2(x)))
  snps$color[snplist['R2'] < 0.2] <- NA

  # query-SNP einfärben
  snps$color[snplist['SNP'] == querysnp] <- "#0000FF"
  # query SNP selbst-r2 auf 1 setzen
  snps$y[snplist['SNP'] == querysnp] <- 1


# Rahmenfarbe
snps$lineColor <- NA
snps$lineColor[snplist['MULTIPLE'] == 1] <- "#006611"
snps$lineColor[snplist['DISEASE'] == 1] <- "#003399"
snps$lineWidth <- NA
snps$lineWidth[snplist['MULTIPLE'] == 1] <- 1.5
snps$lineWidth[snplist['DISEASE'] == 1] <- 2
snps$radius <- NA
#snps$radius[snplist['DISEASE'] == 1] <- 6
snps$radius[snplist['SNP'] == querysnp] <- 7

# SNP Gruppen
# 1: unknown
# 2: Transkript unbekannt
# 3: Regulatorisch unbekannt
# 4: Regulatorisch direkt
# 5: Transkript direkt

# Punktformen nach Funktion
groupsymbol <- c("circle","square","diamond","triangle","triangle-down")
groupname <- c("unknown effect","putative effect on transcript","putative regulatory effect","direct regulatory effect","direct effect on transcript")
groupsymbolsize <- c(4,4,5,5,5)


# annotierte Gene		 
if (dim(genelist)[1] > 0) {

	# kürze Gene
	genelist$START <- sapply(genelist$START,function(x) { max(x,plotxmin) } )
	genelist$STOP <- sapply(genelist$STOP,function(x) { min(x,plotxmax) } )
	
	
	# mittelpunkt für label
	genelist$middle <- (genelist$STOP+genelist$START)/2
	
	# label mit pfeilen 
	genelist$label <- NA
	if (length(which(genelist$STRAND == "+")) > 0) {	genelist[which(genelist$STRAND == "+"),]$label <- paste(genelist[which(genelist$STRAND == "+"),]$NAME,">") }
	if (length(which(genelist$STRAND == "-")) > 0) {	genelist[which(genelist$STRAND == "-"),]$label <- paste("<",genelist[which(genelist$STRAND == "-"),]$NAME) }

	genelist$genestart <- genelist$START
	genelist$genestop <- genelist$STOP
}

# Faerbe Gene ein, wenn HIGHLIGHT gesetzt ist
genelist$COLOR <- "#009900"
genelist$LINEWIDTH <- 2
if (dim(subset(genelist, HIGHLIGHT > 0))[1] > 0) {
	genelist[which(genelist$HIGHLIGHT > 0),]$COLOR <- "#005500"
	genelist[which(genelist$HIGHLIGHT > 0),]$LINEWIDTH <- 3
}




# annotierte CNV

if (nrow(qonecnvlist) > 0) 
{ 
		 
if (dim(qonecnvlist)[1] > 0) {

	# kürze Gene
	qonecnvlist$START <- sapply(qonecnvlist$START,function(x) { max(x,plotxmin) } )
	qonecnvlist$STOP <- sapply(qonecnvlist$STOP,function(x) { min(x,plotxmax) } )
	
	
	# mittelpunkt für label
	qonecnvlist$middle <- (qonecnvlist$STOP+qonecnvlist$START)/2
	
	# label mit pfeilen 
	qonecnvlist$label <- NA
	if (length(which(qonecnvlist$STRAND == "+")) > 0) {	qonecnvlist[which(qonecnvlist$STRAND == "+"),]$label <- paste(qonecnvlist[which(qonecnvlist$STRAND == "+"),]$NAME,">") }
	if (length(which(qonecnvlist$STRAND == "-")) > 0) {	qonecnvlist[which(qonecnvlist$STRAND == "-"),]$label <- paste("<",qonecnvlist[which(qonecnvlist$STRAND == "-"),]$NAME) }

	qonecnvlist$genestart <- qonecnvlist$START
	qonecnvlist$genestop <- qonecnvlist$STOP
}

# Faerbe Gene ein, wenn HIGHLIGHT gesetzt ist
qonecnvlist$COLOR <- "#009900"
qonecnvlist$LINEWIDTH <- 2
if (dim(subset(qonecnvlist, HIGHLIGHT > 0))[1] > 0) {
	qonecnvlist[which(qonecnvlist$HIGHLIGHT > 0),]$COLOR <- "#005500"
	qonecnvlist[which(qonecnvlist$HIGHLIGHT > 0),]$LINEWIDTH <- 3
}


}

# Rekombinationsrate
gendist <- subset(snplist, select = c("POS","CMMB"))
gendist <- gendist[which(!is.na(gendist$CMMB)),]

if (nrow(gendist) > 3) {
	gendist$keep <- TRUE
	for (i in c(2:(nrow(gendist)-1))) { 
	   if ((gendist[i,'CMMB'] == gendist[(i-1),'CMMB']) && (gendist[i,'CMMB'] == gendist[(i+1),'CMMB'])) 
	     { gendist[i,'keep'] <- FALSE }   
	}
    gendist <- subset(gendist, keep == TRUE)
	gendist$keep <- NULL
}


# starte recomb-track 15% der pval-Range unterhalb des SNP-Tracks
gendist$y <- (0-0.15*plotmax)+(gendist$CMMB/60)*(6*plotmax/8)
gendist$x <- gendist$POS
gendist$cmmbmap <- gendist$CMMB # workaround wg cmmb-Spalte in snps
gendist$CMMB <- NULL
gendist$POS <- NULL

# LD-Plots: Lösche alle SNPs mit LD < 0.1

#snps <- snps[(snps$y > 0.1),]


h <- rCharts:::Highcharts$new()


seriestmp <- list()

# SNPs Gruppenweise in plotseries Objekt schreiben
#for (grpcnt in c(1:length(groupname))) {
#    seriestmp[[grpcnt]] <- 
#    list(data = toJSONArray(subset(subset(snps, y > 0.1), group == grpcnt), json=F), 
#	       #yAxis = "pvals",
#	       color="#EEEEEE", 
#		   marker=list(lineColor="#444444",
#		               lineWidth=1.2, 
#					   symbol=groupsymbol[grpcnt],
#					   radius=groupsymbolsize[grpcnt],
#					   states=list(hover=list(fillColor="#006600",radius=groupsymbolsize[grpcnt]+3))
#					   ), 
#		   name = groupname[grpcnt], 
#		   turboThreshold = 0, 
#		   type="scatter",
#		   index = grpcnt+5
#		 )
#}

for (grpcnt in c(1:length(groupname))) {
    snpslisttmp <- list();
	snpstmp <- subset(subset(snps, y > 0.1), group == grpcnt)
	if (nrow(snpstmp) > 0) {
		for (k in rownames(snpstmp)) {
			if (is.na(snps[k,'y'])) { next; }
			m <- list("x" = snpstmp[k,'x'], "y" = snpstmp[k,'y'], "snpname" = snpstmp[k,'snpname']);
			if (!is.na(snpstmp[k,'cmmb'])) { m$cmmb = snpstmp[k,'cmmb'] }
			if (!is.na(snpstmp[k,'lineColor'])) { m$lineColor = snpstmp[k,'lineColor'] }
			if (!is.na(snpstmp[k,'lineWidth'])) { m$lineWidth = snpstmp[k,'lineWidth'] }
			if (!is.na(snpstmp[k,'color'])) { m$color = snpstmp[k,'color'] }
			if (!is.na(snpstmp[k,'radius'])) { m$radius = snpstmp[k,'radius'] }
			snpslisttmp[[length(snpslisttmp)+1]] <- m
		}
	}
	
	seriestmp[[grpcnt]] <- 
    list(data = snpslisttmp, 
	       #yAxis = "pvals",
	       color="#EEEEEE", 
		   marker=list(lineColor="#444444",
		               lineWidth=1.2, 
					   symbol=groupsymbol[grpcnt],
					   radius=groupsymbolsize[grpcnt],
					   states=list(hover=list(fillColor="#006600",radius=groupsymbolsize[grpcnt]+3))
					   ), 
		   name = groupname[grpcnt], 
		   turboThreshold = 0, 
		   type="scatter",
		   index = grpcnt+5
		 )
}


seriestmp[[(length(seriestmp)+1)]] <- 
			  list(data = list(x=0,y=0), 
				  #yAxis = "pvals",
				  type="scatter", 
				  color="#EEEEEE", 
				  showInLegend=TRUE,
				  name="multiple effects",
                  marker=list(enabled=TRUE,
							  states=list(hover=list(enabled=FALSE)),
							  symbol=groupsymbol[1],
							  lineWidth=2,
							  lineColor="#006611"
							  ),
				  events=list(legendItemClick="#! function() { return false; } !#"),
				  index= grpcnt + 6
				 )

seriestmp[[(length(seriestmp)+1)]] <- 
			  list(data = list(x=0,y=0), 
				  #yAxis = "pvals",
				  type="scatter", 
				  color="#EEEEEE", 
				  showInLegend=TRUE,
				  name="associated with trait",
                  marker=list(enabled=TRUE,
							  states=list(hover=list(enabled=FALSE)),
							  symbol=groupsymbol[1],
							  lineWidth=2,
							  lineColor="#003399"
							  ),
				  events=list(legendItemClick="#! function() { return false; } !#"),
				  index= grpcnt + 7
				 )



if (dim(genelist)[1] > 0) {
	# Dummy-Eintrag, um in der Legende Gene an- und auszuschalten
	seriestmp[[(length(seriestmp)+1)]] <- 
			  list(data = list(x=0,y=0), 
				  #yAxis = "pvals",
				  type="line", 
				  color="#009900", 
				  showInLegend=TRUE,
				  name="transcript",
                                  id="genes",				  
				  marker=list(enabled=FALSE,
							  states=list(hover=list(enabled=FALSE))
							  ),
				  index=3
				 )
	# Schreibe jedes Gen in seinen eigenen Layer im Plotseries-Objekt
	for (g in c(1:dim(genelist)[1])) {
	      r <- list(list(x=as.integer(genelist[g,'START']),
		                 y=genelist[g,'LAYER'],
						 label=NA,
						 genename=genelist[g,'NAME'],
						 geneid=genelist[g,'ID']
		                ),
					list(x=as.integer(genelist[g,'middle']),
		                 y=genelist[g,'LAYER'],
						 label=genelist[g,'label'],
						 genename=genelist[g,'NAME'],
						 geneid=genelist[g,'ID']
		                ),
					list(x=as.integer(genelist[g,'STOP']),
		                 y=genelist[g,'LAYER'],
						 label=NA,
						 genename=genelist[g,'NAME'],
						 geneid=genelist[g,'ID']
		                )
				    )
                         
                         
	  		
			  seriestmp[[(length(seriestmp)+1)]] <- 
			  list(data = r, 
				  #yAxis = "pvals",
				  type="line", 
				  color=genelist[g,'COLOR'], 
				  lineWidth=genelist[g,'LINEWIDTH'], 
				  showInLegend=FALSE,
				  linkedTo="genes",		  
				  marker=list(enabled=FALSE,
							  states=list(hover=list(enabled=FALSE))
							  ),
				  index=100+g
				) 
	}
}


if (nrow(qonecnvlist) > 0) 
{				
# Gene in plotseries Objekt schreiben
if (dim(qonecnvlist)[1] > 0) {
	# Dummy-Eintrag, um in der Legende Gene an- und auszuschalten
	seriestmp[[(length(seriestmp)+1)]] <- 
			  list(data = list(x=0,y=0), 
				  #yAxis = "pvals",
				  type="line", 
				  color="red", 
				  showInLegend=TRUE,
				  name="CNV",
                                  id="Q1",				  
				  marker=list(enabled=FALSE,
							  states=list(hover=list(enabled=FALSE))
							  ),
				  index=2
				 )
	# Schreibe jedes Gen in seinen eigenen Layer im Plotseries-Objekt
	for (g in c(1:dim(qonecnvlist)[1])) {
	      r <- list(list(x=as.integer(qonecnvlist[g,'START']),
		                 y=qonecnvlist[g,'LAYER'],
						 label=NA,
						 qgenename=qonecnvlist[g,'NAME'],
						 cnvid=qonecnvlist[g,'ID']
		                ),
					list(x=as.integer(qonecnvlist[g,'middle']),
		                 y=qonecnvlist[g,'LAYER'],
						 label=qonecnvlist[g,'label'],
						 qgenename=qonecnvlist[g,'NAME'],
						 cnvid=qonecnvlist[g,'ID']
		                ),
					list(x=as.integer(qonecnvlist[g,'STOP']),
		                 y=qonecnvlist[g,'LAYER'],
						 label=NA,
						 qgenename=qonecnvlist[g,'NAME'],
						 cnvid=qonecnvlist[g,'ID']
		                )
				    )
                          
                          cnvname<-qonecnvlist[g,'NAME']	 
                          lncol<-'';
                          #cat(paste(cnvname) , sep = "\n",file ="/home/metabolomics/snipa/web/backend/teout.txt",append = TRUE) 		
                          if(length(grep ("Q1",cnvname)!=0)) {lncol<-"#ff0000"} else if(length(grep ("Q2",cnvname)!=0)) {lncol<-"#ff0000"} else if(length(grep ("Q3",cnvname)!=0)) {lncol<-"#ff0000"}
 
			  seriestmp[[(length(seriestmp)+1)]] <- 
			  list(data = r, 
				  #yAxis = "pvals",
				  type="line",                                  
				  color=lncol, 
				  lineWidth=qonecnvlist[g,'LINEWIDTH'], 
				  showInLegend=FALSE,
				  linkedTo="Q1",		  
				  marker=list(enabled=FALSE,
							  states=list(hover=list(enabled=FALSE))
							  ),
				  index=100+g
				) 
	}
}

}

# Regulatorische Elemente in plotseries Objekt schreiben
if (dim(regellist)[1] > 0) {
	# Dummy entry to turn on and off in the legend Regele Mente
	seriestmp[[(length(seriestmp)+1)]] <- 
			  list(data = list(x=-10,y=0), 
				  #yAxis = "pvals",
				  type="line", 
				  color="#0000FF", 
				  showInLegend=TRUE,
				  name="regulatory element",
                  id="regel",				  
				  marker=list(enabled=FALSE,
							  states=list(hover=list(enabled=FALSE))
							  ),
				  index=2
				 )
	# Write each regulating element in its own layer in PlotSeries object
	for (g in c(1:dim(regellist)[1])) {
	      r <- list(list(x=as.integer(regellist[g,'START']),
		                 y=(regellist[g,'LAYER']),
						 regelname=regellist[g,'NAME']
		                ),
				    list(x=as.integer(regellist[g,'STOP']),
		                 y=(regellist[g,'LAYER']),
						 regelname=regellist[g,'NAME']
		                )
				    )
			
			  seriestmp[[(length(seriestmp)+1)]] <- 
			  list(data = r, 
				  #yAxis = "pvals",
				  type="line", 
				  color="#0000FF", 
				  showInLegend=FALSE,
				  linkedTo="regel",		  
				  marker=list(enabled=FALSE,
							  states=list(hover=list(enabled=FALSE))
							 ),
				  index=1000+g
				) 
	}
}


cpgpath=paste0("/home/metabolomics/snipa/web/tmpdata/",jobid,"/ldplot_cpglist_static.txt");
#data<-read.table("/home/metabolomics/snipa/web/backend/datameythlation.txt",sep="\t");
data<-read.table(cpgpath);
errorcpg<-list()
meancpg<-list()
for (i in 1:nrow(data)){meancpg[[i]]<-c(data$V2[i],(data$V4[i]))}
for (i in 1:nrow(data)){errorcpg[[i]]<-c(data$V2[i],(data$V5[i]),(data$V6[i]))}


#lapply(meancpg, write, "/home/metabolomics/snipa/web/tmpdata/test1.txt", append=TRUE)
#lapply(errorcpg, write, "/home/metabolomics/snipa/web/tmpdata/test2.txt", append=TRUE)

seriestmp[[(length(seriestmp)+1)]]<-list(data = meancpg,type="column",name="methylation",yAxis=1,color="#FE730C",pointWidth=8,hideTooltip=TRUE)
seriestmp[[(length(seriestmp)+1)]]<-list(data = errorcpg,type="errorbar",yAxis=1,hideTooltip=TRUE)

# CMMB in Plotseries Objekt schreiben
seriestmp[[(length(seriestmp)+1)]] <- 
        list(data = toJSONArray(gendist, json=F),
		     type="line",
			 #axis="pvals",
			 index=1,
			 showInLegend=TRUE,
			 name="recombination rate",
			 turboThreshold = 0, 
			 marker=list(enabled=FALSE,
		                 states=list(hover=list(enabled=FALSE))
						),
			 color="#BBBBFF",
			 events=list(legendItemClick = paste0("#! function() { if (this.visible) { chart.yAxis[1].update({labels: { enabled: false}, title: {text: null} }); } else { chart.yAxis[1].update({labels: { enabled: true}, title: {text: 'Recombination rate  (cM/Mb)'} }); } } !#"))
		    )

h$series(seriestmp)
yAxistmp <- list()
# Y-Achse für p-Werte
yAxistmp[[1]] <- list(
				# Y-Achse für p-Werte
				title=list(text= "Linkage disequilibrium (r<sup>2</sup>)",useHTML=TRUE),
				min=plotmin,
				max=plotmax,
                                height='85%',
				tickPositions=pvalticks,
				startOnTick=FALSE,
				endOnTick=FALSE,
				gridLineWidth=0,
				gridLineColor="transparent"
				)

yAxistmp[[2]] <- list(
				# Y-Achse für p-Werte
				title=list(text= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fraction of Methylation",useHTML=TRUE),
				min=plotmin,
				max=plotmax,
                                height='50%',
                                offset=0,
                                top='80%',
				tickPositions=pvalticks,
				startOnTick=FALSE,
				endOnTick=FALSE,
				gridLineWidth=0,
				gridLineColor="transparent"
				)

yAxistmp[[3]] <- list(
				# Y-Achse für Recombination rate
				title=list(text= "Recombination rate  (cM/Mb)"),
				min=plotmin,
				max=plotmax,
                                height='75%',
				tickPositions=c((0-0.15*plotmax)+(0/60)*(6*plotmax/8),(0-0.15*plotmax)+(20/60)*(6*plotmax/8),(0-0.15*plotmax)+(40/60)*(6*plotmax/8),(0-0.15*plotmax)+(60/60)*(6*plotmax/8)),
				startOnTick=FALSE,
				endOnTick=FALSE,
				labels=list(formatter = paste0("#! function() { return Math.round((this.value+",(0.15*plotmax),")*(80/",plotmax,")); } !#")),
				gridLineWidth=0,
				gridLineColor="transparent",
				opposite=TRUE
				)


			
h$yAxis(yAxistmp)
				

		

# X-Achse mit genetischer Position		
h$xAxis(title=list(text=paste("Chromosome",chr)), min=plotxmin, max=plotxmax)
 
tooltipfun <- paste("#! function() { 
                          if (this.series.options.hideTooltip) {return false;}
                          if (this.point.cnvid) {return false;}
			 
			   
                  
               var annourl = 'backend/snipaLDPlotsTooltips.php';
			   var s = '';
			   if (this.point.snpname) { 
				s = $.getAnnotations(annourl+'?jobid=",jobid,"&type=annotation&element=snp&name='+this.point.x); 
			   }
			   if (this.point.geneid) {
			     s = '<strong>' + 
					 this.point.genename + '</strong><br />' + 
					 $.getAnnotations(annourl+'?jobid=",jobid,"&type=annotation&element=gene&name='+this.point.geneid);
			   }
			   if (this.point.cmmbmap) {
			     
			     s = 'Recombination rate: ' + this.point.cmmbmap + ' cM/Mb';
			   }
			   if (this.point.regelname) {
			     s = '<strong>' + 
					 this.point.regelname + '</strong><br />' +
					 $.getAnnotations(annourl+'?jobid=",jobid,"&type=annotation&element=regelement&name='+this.point.regelname);
			   }
			   return s;
                            
			  } !#", sep ="")
			  
linkfun <- paste("#! function() { 
               		var annourl = 'backend/snipaLDPlotsTooltips.php';
					if (this.options.snpname) { url = $.getAnnotations(annourl+'?jobid=",jobid,"&type=link&element=snp&name='+this.options.x); }
					if (this.options.geneid) { url = $.getAnnotations(annourl+'?jobid=",jobid,"&type=link&element=gene&name='+this.options.geneid); }
					if (this.options.regelname) { url = $.getAnnotations(annourl+'?jobid=",jobid,"&type=link&element=regelement&name='+this.options.regelname); }
					if (url != null) {
						window.open(url,'_blank')
			        }
			  } !#", sep="")
			  

h$tooltip(useHTML=TRUE, formatter = paste(tooltipfun),hideDelay=0,animation=FALSE, borderRadius=0)
h$chart(zoomType = "x",
		animation=FALSE,
		plotBorderColor="#000000",
		plotBorderWidth=1, 
		resetZoomButton = list(position = list("x" = -30, "y" = 10)),
		renderTo = "snipa-raplot-dynamic",
		alignTicks=FALSE,
                height=1100,width=850  #new line added
		)
h$plotOptions(scatter=list(animation=FALSE,
						   cursor="pointer",
						   point=list(events=list(
												click=paste("#! function(event) { var tmpevent = event; 
																				  var tmpsnpname = this.snpname; 
																				  var tmpsnppos = this.x; 
																				  var tmpsnpchr = '",chr,"';
																				  var tmpgenomerelease = $('select#dataset-genomerelease').val();
																				  var tmpreferenceset = $('select#dataset-referenceset').val();
																				  var tmppopulation = $('select#dataset-population').val();
																				  var tmpannotation = $('select#dataset-annotation').val();
																	  
																				  showPlotAnnotationMenu(tmpevent,tmpsnpname,tmpsnppos,tmpsnpchr,",snplist[which(snplist$SNP == querysnp),'POS'],",tmpgenomerelease,tmpreferenceset,tmppopulation,tmpannotation);

																				  } !#",sep=""),
												# Zeichne vertikale Line, wenn Cursor über einem SNP steht
												mouseOver="#! function() { chart.xAxis[0].addPlotLine({color: '#FF0000', width: 1, value: this.x, id: 'vertline'}); } !#",
												mouseOut="#! function() { chart.xAxis[0].removePlotLine('vertline'); } !#"
												)
						              )
                          ),
              line=list(animation=FALSE,
						cursor="pointer",
						point=list(events=list(click=paste(linkfun))),
			            dataLabels=list(enabled=TRUE, 
						                formatter = "#! function() { return this.point.label; }!#"
										)
						)
			  )

# Credits aus
h$credits(enabled = FALSE);
			  
# Exportoptionen
h$exporting(enabled = TRUE, url = "backend/HighchartsExport/", buttons = list(contextButton = list(align = "right", verticalAlign = "top")), sourceWidth=1024, sourceHeight=768)

h$params$dom <- NULL
h$params$width <- NULL
h$params$height <- NULL
return(toJSON2(h$params))
}

