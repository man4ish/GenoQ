#a<-as.list(c(22,13,25,43,63,47,42,43,42,75))
#b<-as.list(c(12,32,15,41,61,71,45,23,22,45))
#c<-as.list(c(11,12,11,21,31,11,55,63,72,35))

data<-read.table("/home/metabolomics/snipa/web/snipa/tmp/Allefreq.txt",sep="\t");
a<-data$V1
b<-data$V2
c<-data$V3

library(RCurl)
require(devtools)
library(rCharts)
h <- rCharts:::Highcharts$new()
h$chart(
		animation=FALSE,
		plotBorderWidth=3, 
		resetZoomButton = list(position = list("x" = -30, "y" = 10)),
		renderTo = "snipa-raplot-dynamic",
		spacingLeft= 0,
                #margin=c(50,50,100,50),
                margin=c(0,0,0,0),
                borderWidth=2, 
                borderColor="red" 
       )
h$chart(margin = list(left =0))
xAxistmp <- list()
#labels=list(style=c(fontSize="15px"))
xAxistmp[[1]] <- list(title=list(text= "Minor Allele Frequency",useHTML=TRUE,labels=list(style=c(fontSize="15px"))),categories=c('0.05','0.1','0.15','0.2','.25','.3','.35','.4','.45','.5'))

h$xAxis(xAxistmp)
h$plotOptions(column=list(animation=FALSE),series=list(pointWidth=20,pointPadding=0.25,groupPadding=0.25)) 
yAxistmp <- list()
# Y-Achse für p-Werte
yAxistmp[[1]] <- list(
				# Y-Achse für p-Werte
                                lineWidth=1,
				title=list(text= "Count",useHTML=TRUE),
				min=0,
				#max=2000,
				#tickPositions=pvalticks,
				startOnTick=FALSE,
				endOnTick=FALSE,
				gridLineWidth=0,
				gridLineColor="transparent"
				)
				
h$yAxis(yAxistmp)				
seriestmp<-list()
seriestmp[[1]]<-list(data = a,type="column", name="GenoQ", color='black')
seriestmp[[2]]<-list(data = b,type="column", name="Common",color='green')
seriestmp[[3]]<-list(data = c,type="column", name="1000 Genome")
h$series(seriestmp)
h$save('/home/metabolomics/snipa/web/frontend/js/barplot.html', standalone = TRUE)


