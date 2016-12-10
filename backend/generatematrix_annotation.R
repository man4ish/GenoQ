options(echo=FALSE) # if you want see commands in output file
args <- commandArgs(trailingOnly = TRUE)

#system('rm -r /home/metabolomics/snipa/web/frontend/js/tmp')
#dir.create("/home/metabolomics/snipa/web/frontend/js/tmp")

filename<-args[1];
jobdir<-args[6];
#stop("done");
outfile<-paste0(jobdir,"/data",args[3],".json",sep="")
print(outfile);
population<-args[2];
#colarray=c( "#ff0000" ,"#FFA500" ,"#ffff00", "#D8D8D8", "#FAFAFA")
colarray=c( "#ff0000" ,"#ff3333" ,"#ff8080", "#ff9999", "#FAFAFA","#ffb3b3")
library(MASS)

push <- function(vec, item) {
     vec=substitute(vec)
     eval.parent(parse(text = paste(vec, ' <- c(', vec, ', ', item, ')', sep = '')), n = 1)
}


intersection<-function (m1,m2,c1,c2)
{
 
   x<-(c2-c1)/(m1-m2);
   y<-m1*x+c1;
   return (c(x,y)); 
}

a1<-numeric(0)
b1<-numeric(0)
c1<-numeric(0)


dat<-read.csv(filename,sep="\t",header=T,row.names=NULL)
tmpdat<-dat[dat$MAF>0.05,]
dat<-tmpdat

#dat<-dat[1:10,]

chr<-as.vector(dat$CHR)
a<-as.vector(dat$POS1)
b<-as.vector(dat$POS2)
c<-dat$R2
qrsid<-as.vector(dat$QRSID)
rsid<-as.vector(dat$RSID)


coordmap=list();
colmap=list()
annotationmap=list()
auni=unique(a);
buni=unique(b);

#colmap[[paste(a,b,sep="-")]]=round(as.numeric(as.character(c)),1)
#annotationmap[[paste(a,b,sep="-")]]=paste(qrsid,"-",rsid," r2:",c,sep="")
#coordmap[a]=qrsid;
coordmap[a]=rsid;
coordmap[b]=rsid;

#coordmap[b]=rsid;
#cat(coordmap,file="/home/metabolomics/snipa/web/backend/sample.txt")


for (i in 1:nrow(dat))
{
     #colmap[[paste(a[i],b[i],sep="-")]]=round(as.numeric(as.character(c[i])),1) 
     colmap[[paste(a[i],b[i],sep="-")]]=as.numeric(as.character(c[i]))
     annotationmap[[paste(a[i],b[i],sep="-")]]=paste(qrsid[i],"-",rsid[i]," r2:",c[i],sep="")   
     #coordmap[a[i]]=qrsid[i];
     #coordmap[b[i]]=rsid[i];
}

#m<-c(a,b)
m<-a
muni<-sort(unique(m))
#print(length(muni))

#muni<-muni[1:(length(muni)-2)]

a2<-numeric(0)
b2<-numeric(0)
c2<-numeric(0)

p = matrix(0, length(muni), length(muni))
q = matrix(0, length(muni), length(muni))
r = matrix(0, length(muni), length(muni))

#print(r);
#a1 = matrix(0, length(muni), length(muni))
#b1 = matrix(0, length(muni), length(muni))
#a2 = matrix(0, length(muni), length(muni))
#b2 = matrix(0, length(muni), length(muni))
#a3 = matrix(0, length(muni), length(muni))
#b3 = matrix(0, length(muni), length(muni))
#a4 = matrix(0, length(muni), length(muni))
#b4 = matrix(0, length(muni), length(muni))

ann<-matrix('', length(muni), length(muni))
for (i in 1:length(muni))
{
   for (j in 1:length(muni))
   {
       if(j>=i){
       value<-colmap[[paste(muni[i],muni[j],sep="-")]]
       annotation<-annotationmap[[paste(muni[i],muni[j],sep="-")]]
       if(muni[i]==muni[j])
       {
         value<-1
       }
       else if(length(value)==0)
       {
          value<-colmap[[paste(muni[j],muni[i],sep="-")]]
          annotation<-annotationmap[[paste(muni[j],muni[i],sep="-")]]
       } 
       
       if(length(value)==0)
       {         
                 value<-0
       } 
       #push(a2,muni[i])
       #push(b2,muni[j])
       #push(c2,value)
       x<-(as.numeric(muni[i])+as.numeric(muni[j]))/2
       y<-as.numeric(muni[j])-as.numeric(muni[i])
       p[i,j] = p[i,j] + x
       #cat(p[i,j],i,j,"\n");
       q[i,j] = q[i,j] + abs(y)+100; 
      

   
       if(args[4] == "0") { q[i,j] = -q[i,j];}
       r[i,j] = r[i,j] + value;
       #cat(muni[i],muni[j],i,j,r[i,j],"\n");
       ann[i,j]<-paste0(ann[i,j],annotation);
       }
   }   
}

if(file.exists("title.json")){
file.remove("title.json")
}

name<-""
pop<-args[2]
if(pop=="amr")
{ 
   pop<-"American"
} else if(pop=="afr")
{ 
   pop<-"African"
} else if(pop=="eur")
{ 
   pop<-"European"
} else if(pop=="qtr")
{ 
   pop<-"Qatar"
}

if(args[4]=="0")
{
  cat("{","\"text\":","\"LDHeatmap ",pop," vs Qatar Population\"",",","\"value\":","42542352345}" ,file="/home/metabolomics/snipa/web/frontend/js/tmp/title.json","\n")
}

if(args[4]=="0")
{
    if(file.exists(outfile))
    {
        file.remove(outfile)
    }
}

if(args[4]=="0") 
{
  cat("[",append=T,file=outfile)
} else {
   cat(",",append=T,file=outfile)
}
color<-""

for (i in 2:(length(muni)-1))
{
     for (j in 2:(length(muni)-1))
     { 
        if(i<j)
        {
               if(FALSE){if(r[i,j]>=0.8){color<-colarray[1]} 
               else if(r[i,j]>=0.5 && r[i,j]<0.8){color<-colarray[2]} 
               else if(r[i,j]>=0.2 && r[i,j]<0.5){color<-colarray[3]} 
               else if(r[i,j]>=0.0 && r[i,j]<0.2){color<-colarray[5]} 
              }
               if(r[i,j]>=0.8){color<-colarray[1]} 
               else if(r[i,j]>=0.5 && r[i,j]<0.8){color<-colarray[2]} 
               else if(r[i,j]>=0.2 && r[i,j]<0.5){color<-colarray[3]} 
               else if(r[i,j]>=0.05 && r[i,j]<0.2){color<-colarray[6]} 
               else if(r[i,j]>=0.0 && r[i,j]<0.05){color<-colarray[5]} 

               a1<-(p[i,j]+p[i,j+1])/2
               a2<-(p[i,j]+p[i+1,j])/2
               a3<-(p[i,j]+p[i,j-1])/2
               a4<-(p[i,j]+p[i-1,j])/2

               b1<-(q[i,j]+q[i,j+1])/2
               b2<-(q[i,j]+q[i+1,j])/2
               b3<-(q[i,j]+q[i,j-1])/2
               b4<-(q[i,j]+q[i-1,j])/2

               m2<-(b3-b1)/(a3-a1)
               m4<-m2
               m1<-(b4-b2)/(a4-a2)
               m3<-m1

               c1<--m1*a1+b1
               c2<--m2*a2+b2
               c3<--m3*a3+b3
               c4<--m4*a4+b4        



               i1<-intersection(m1,m2,c1,c2);
               i2<-intersection(m2,m3,c2,c3);
               i3<-intersection(m3,m4,c3,c4);
               i4<-intersection(m4,m1,c4,c1);       
 
                   



               if(args[5]=="0"){      
                     
                  cat(paste("{\"showInLegend\": false,\"someText\": \"",paste(chr[1]," ",coordmap[muni[i]],"(",muni[i],")",coordmap[muni[j]],"(",muni[j],"):r2 ",r[i,j]),"\",\"color\": \"",color,"\", \"data\": ", "[","[",p[i,j],", ",q[i,j],"], ","[",p[i,j+1],", ",q[i,j+1],"], ","[",p[i-1,j+1],", ",q[i-1,j+1],"], ","[",p[i-1,j],", ",q[i-1,j],"]","]", " }",sep=""),append=T,file=outfile,"\n") 
              } else {
                 
               cat(paste(i1[1],"\t",i1[2],"\t",i2[1],"\t",i2[2],"\t",i3[1],"\t",i3[2],"\t",i4[1],"\t",i4[2],"\t\"",color,"\"",sep=""),append=T,file=paste0(outfile,".sample"),"\n") 
               cat(paste("{\"showInLegend\": false,\"someText\": \"",paste(coordmap[muni[i]],"(",muni[i],")-",coordmap[muni[j]],"(",muni[j],") :r2 ",r[i,j]),"\", \"color\": \"",color,"\", \"data\": ", "[[",i1[1],", ",i1[2],"],[",i2[1],", ",i2[2],"],[",i3[1],", ",i3[2],"],[",i4[1],", ",i4[2],"]]", " }",sep=""),append=T,file=outfile,"\n")

              }
              if(i!=length(muni)-2) {cat(",",append=T,file=outfile)}
        }
     }
}

if(args[4]=="0")
{
     cat(",{\"name\": \"Variant Position\",\"type\": \"line\",\"lineColor\": \"","#008000","\", \"data\": [",append=T,file=outfile)
     for (i in 1:(length(muni)))
     {
          cat("{\"x\":",muni[i],",\"y\":0,","\"mousevertext\": \"",chr[1]," ",muni[i],"\"}",append=T,file=outfile)
          cat(muni[i],append=T,file=paste0(outfile,".line"),"\n")
          if(i!=length(muni)) {cat(",",append=T,file=outfile)}        
     }
     cat("],\"marker\": {\"enabled\" : true,\"fillColor\" : \"green\",\"radius\" : 3}",append=T,file=outfile)
     cat("}",append=T,file=outfile)
}

if(args[4]!="0")
{
  cat("]",append=T,file=outfile)
}

