options(echo=FALSE) # if you want see commands in output file
args <- commandArgs(trailingOnly = TRUE)
jobdir<-args[1]
#jobdir<-"/home/metabolomics/snipa/web/frontend/js/tmp"
#jobdir<-args[1]
#print(jobdir)
data<-read.table(paste0(jobdir,"/data.sample"),sep="\t",header=FALSE);
plot_title<-read.table(paste0(jobdir,"/title.sample"),sep="\t",header=FALSE);
x1<-data$V1
x2<-data$V3
x3<-data$V5
x4<-data$V7
y1<-data$V2
y2<-data$V4
y3<-data$V6
y4<-data$V8
color<-data$V9
color<-sub("\\s+", "", color)
xx<-c(x1,x2,x3,x4)
yy<-c(y1,y2,y3,y4)

xmin<-min(x1,x2,x3,x4)
xmax<-max(x1,x2,x3,x4)
ymin<-min(y1,y2,y3,y4)
ymax<-max(y1,y2,y3,y4)
#pdf("test.pdf")
#svg(paste0(jobdir,"/polygon.svg"),width=10,height=10)
png(paste0(jobdir,"/polygon.png"))
par(mar=c(5,1,3,1))
plot(xx,yy, type='n',xlim=c(xmin, xmax), ylim=c(ymin, ymax), xaxt='n', ann=FALSE, yaxt='n',axes = FALSE,cex.lab=.5)
axis(1,xlab="Variant Postion",cex.lab=.5,at=pretty(xx), labels=paste0(pretty(xx)/1000, "k"))
title(plot_title$V1,font.main= 1)
box(lty = 'solid', col = 'black',lwd=".2")
mtext("Variant Position", 1 , line=3,font=2)

#segments(xmin, 0, xmax, 0, col= 'green',lwd = 1)
segments(xmin, 1500, xmax, 1500, col= 'green',lwd = 1)
#segments(xmin, 3000, xmax, 3000, col= 'red',lwd = 1)
legend("topleft", c("not in LD","LD r2>=0.05","LD r2>=0.2","LD r2 >=0.5","LD r2>=08"),bty="n", fill=c("#FAFAFA" ,"#ffb3b3", "#ff8080","#ff0000" ,"#ff3333"), horiz=FALSE)
#abline(lm(y ~ x))
for (i in 1:nrow(data))
{
   
     xx=c(x1[i],x2[i],x3[i],x4[i])
     yy=c(y1[i],y2[i],y3[i],y4[i])
     polygon(xx, yy, col=color[i], border=NA, xaxt='n', ann=FALSE)
}

garbage <- dev.off()

#disableing this part of code to test the code with PHP
if(FALSE) 
{
    svg(paste0(jobdir,"/polygon.svg"),width=10,height=10)
    par(mar=c(5,1,3,1))
    plot(xx,yy, type='n',xlim=c(xmin, xmax), ylim=c(ymin, ymax), xaxt='n', ann=FALSE, yaxt='n',axes = FALSE)
    axis(1,xlab="Variant Postion")
    title(plot_title$V1,font.main= 1)
    for (i in 1:nrow(data))
    {
   
        xx=c(x1[i],x2[i],x3[i],x4[i])
        yy=c(y1[i],y2[i],y3[i],y4[i])
        polygon(xx, yy, col=color[i], border=NA, xaxt='n', ann=FALSE)
    }
    box(lty = 'solid', col = 'black',lwd=".2")
    mtext("Variant Position", 1 , line=3,font=2)

    #segments(xmin, 0, xmax, 0, col= 'green',lwd = 1)
    segments(xmin, 5, xmax, 5, col= 'green',lwd = 1)
    #segments(xmin, 10, xmax, 10, col= 'red',lwd = 1)

    #line<-read.csv(paste0(jobdir,"/data.line"),header=FALSE);

    #for (s in 1:(nrow(line)-1))
    #{
        #segments(line[s,1], 0, line[s+1,1], 0, col= 'blue',lwd = 2)
    #}

    legend("topleft", c("not in LD","LD r2>=0.2","LD r2 >=0.5","LD r2>=08"),bty="n", fill=c("white","yellow","orange","red"), horiz=FALSE)
    dev.off();
}


