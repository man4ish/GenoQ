library(rCharts)

push <- function(vec, item) {
     vec=substitute(vec)
     eval.parent(parse(text = paste(vec, ' <- c(', vec, ', ', item, ')', sep = '')), n = 1)
}

a1<-numeric(0)
b1<-numeric(0)
c1<-numeric(0)

dat<-read.csv("/home/metabolomics/snipa/web/snipa/RCode/proxySearch.results.csv",sep="\t",header=T)
#dat<-read.table("C:/Users/man4ish/Desktop/heatmap.lst",sep="\t",header=T)
#dat[rowSums(is.na(dat)) != ncol(dat),]

a<-dat$POS1
b<-dat$POS2
c<-round(dat$R2,1)


auni=unique(a);
buni=unique(b);

for (i in 1:length(a)){

     push (a1,(which(auni==as.character(a[i]))-1))
}

for (i in 1:length(b)){

     push (b1,(which(buni==as.character(b[i]))-1))
}


h <- rCharts:::Highcharts$new()
h$chart(animation=FALSE,
                plotBorderWidth=3,
                #resetZoomButton = list(position = list("x" = -30, "y" = 10)),
                #renderTo = "snipa-raplot-dynamic",
                #spacingLeft= 0,
                #margin=c(50,50,100,50),
                #margin=c(0,0,0,0),
                #borderWidth=2,
                borderColor="red",
                zoomType='x',
                type='heatmap'
                )
h$addAssets(js = c("https://code.highcharts.com/modules/exporting.js",
                   "https://code.highcharts.com/modules/heatmap.js"))
           

df1 <- cbind(as.list(a1),as.list(b1),as.list(c))
colnames(df1) <-NULL
h$plotOptions(column=list(series=list(borderColor='red')))
#h$chart(type='heatmap')
h$xAxis( categories=as.character(auni))
yAxistmp<-list()
yAxistmp[[1]]<-list(gridLineWidth=0,categories=as.character(buni))
h$yAxis(yAxistmp)
h$addParams(colorAxis=list(min=0,minColor='#FFFFFF',maxColor="#!Highcharts.getOptions().colors[0]!#"))

h$series(data = df1)
h$save('/home/metabolomics/snipa/web/snipa/RCode/heatmap.html', standalone = TRUE)
