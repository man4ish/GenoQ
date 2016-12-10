library(RCurl)
require(devtools)
library(rCharts)

push <- function(vec, item) {
     vec=substitute(vec)
     eval.parent(parse(text = paste(vec, ' <- c(', vec, ', ', item, ')', sep = '')), n = 1)
}

a1<-numeric(0)
b1<-numeric(0)
c1<-numeric(0)

dat<-read.csv("proxySearch.results.csv",sep="\t",header=T)


#dat<-dat[1:100,]

a<-dat$POS1
b<-dat$POS2
c<-round(dat$R2,1)

colmap=list()

auni=unique(a);
buni=unique(b);


for (i in 1:nrow(dat))
{
   colmap[[paste(a[i],b[i],sep="-")]]=c[i]   
}


m<-c(a,b)
muni<-unique(m)


a2<-numeric(0)
b2<-numeric(0)
c2<-numeric(0)

for (i in 1:length(muni))
{
   for (j in 1:length(muni))
   {
       if(j>=i){
       value<-colmap[[paste(muni[i],muni[j],sep="-")]]
       
       if(muni[i]==muni[j])
       {
         value<-1
       }
       else if(length(value)==0)
       {
          value<-colmap[[paste(muni[j],muni[i],sep="-")]]
          value<-0
       } 
       
       if(length(value)==0)
       {         
                 value<-0
       } 
       push(a2,muni[i])
       push(b2,muni[j])
       push(c2,value)
       }
       #rec<-paste(a[i],a[j],value)
       #print(rec)
   }   
}


a1<-numeric(0)
b1<-numeric(0)
c1<-numeric(0)

for (i in 1:length(a2)){

     push (a1,(which(muni==as.character(a2[i]))-1))
}

for (i in 1:length(b2)){

     push (b1,(which(muni==as.character(b2[i]))-1))
}


h <- rCharts:::Highcharts$new()
h$chart(animation=FALSE,
                plotBorderWidth=0,
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
#h$addAssets(js = c("https://code.highcharts.com/modules/exporting.js","https://code.highcharts.com/modules/heatmap.js"))
           
#df1<-read.csv("/home/mak2090/Desktop/rite/data.txt",sep="-",header=T)

df1 <- cbind(as.list(a1),as.list(b1),as.list(c2))
colnames(df1) <-NULL
h$plotOptions(column=list(series=list(borderColor='red')))
#h$chart(type='heatmap')
h$xAxis( categories=as.character(muni))
yAxistmp<-list()
yAxistmp[[1]]<-list(gridLineWidth=0,categories=as.character(muni))
h$yAxis(yAxistmp)
h$addParams(colorAxis=list(min=0,minColor='#FFFFFF',maxColor="#!Highcharts.getOptions().colors[0]!#"))

h$series(data = df1,borderWidth=1)
#print(h)
h$save('/home/metabolomics/snipa/web/frontend/js/heatmap.html', standalone = TRUE)
