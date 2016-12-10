args <- commandArgs(trailingOnly = TRUE)
filename<-args[1]
data<-read.table(filename,row.names=NULL,header=TRUE);
print(nrow(data));
data <- data[data$MAF > 0.05,]
print(nrow(data));
print(colnames(data));

for (int i=0; i<pos1.size();i++)
   {
      for (int j=0; j<pos1.size();j++)
      {
          if(i<=j){
          double value=0;
          value=colmap[pos1[i]+"-"+pos1[j]];

          if(pos1[i]==pos1[j])
          {
             value=1;
          }
          else if(value==0)
          {
             value=colmap[pos1[j]+"-"+pos1[i]];
          }
          //cout << pos1[i] <<"\t"<<pos1[j]<<"\t"<<value<<endl;
          long int x=(atoi(pos1[i].c_str())+atoi(pos1[j].c_str()))/2;
          //double x=(atol(pos1[i].c_str())+atol(pos1[j].c_str()))/2;
          long int y=atoi(pos1[j].c_str())-atoi(pos1[i].c_str());
          p[i][j] = x;
          q[i][j] = abs(y)+1000;
          if(ref==0){q[i][j]=-q[i][j];}
          r[i][j] = value;
          //cout << x <<"\t"<<y<<"\t"<<value<<endl;
          }
      }
}
