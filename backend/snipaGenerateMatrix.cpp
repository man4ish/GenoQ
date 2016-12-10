#include <iostream>
#include <fstream>
#include <cstring>
#include <vector>
#include <set>
#include <algorithm>
#include <map>
#include <cmath>
using namespace std;

char colarray[6][25] = {"#ff0000" ,"#ff3333" ,"#ff8080", "#ff9999", "#FAFAFA","#ffb3b3"};

bool myfunction (int i, int j) {
  return (i==j);
}


vector<int> intersection(int m1,int m2,int c1,int c2)
{
   vector<int> vec;
   int x=(c2-c1)/(m1-m2);
   int y=m1*x+c1;
   vec.push_back(x);
   vec.push_back(y);
   return vec;
}

int main(int argc, char* argv[])
{

   char* filename=argv[1];
   string pop=argv[2];
   bool ref = atoi(argv[3]);
   bool phaseflag=atoi(argv[5]);
   char* jobdir = argv[6]; 
   //cout << phaseflag <<endl;        
  /*if(file.exists("title.json")){
    file.remove("title.json")
  }*/


   //string pop="amr";
   if(pop=="amr")
   {
     pop="American";
   } else if(pop=="afr")
   {
      pop="African";
   } else if(pop=="eur")
   {
      pop="European";
   } else if(pop=="qtr")
   {
      pop="Qatar";
   } else if(pop=="sas")
   {
     pop="South East Asian";
   } 
   else if(pop=="eas")
   {
     pop="East Asian";
   }

   

if(ref==0)
{
    ofstream jsontitlefile("/home/metabolomics/snipa/web/frontend/js/tmp/title.json");
    //char* dynamictitlefile; 
    //strcpy(dynamictitlefile,jobdir);
    //ofstream jsontitlefile(strcat(dynamictitlefile,"/title.json")); 
    jsontitlefile<<"{"<<"\"text\":"<<"\"LDHeatmap "<<pop<<" vs Qatar Population\""<<","<<"\"value\":"<<"42542352345}"<<"\n";
    jsontitlefile.close();
    
    //char* statictitlefile;
    //strcpy(statictitlefile,jobdir); 
    //ofstream titlefile(strcat(statictitlefile,"/title.sample"));

    ofstream titlefile("/home/metabolomics/snipa/web/frontend/js/tmp/title.sample");
    titlefile<<"LDHeatmap Qatar vs "<<pop<<" Population\n";
    titlefile.close(); 
}


   ifstream in(filename);
   char line[2000];
   vector <string> chr,rsid;
   vector<string> pos1,pos2;
   vector<float> c;
   map<string,string> coordmap;
   map<string,float> colmap;
   vector <double> maf; 
   string start,stop;
   double r2,mafval; 
   set<int> s;
   while(in)
   {

     	in.getline(line,5000);
        if(in)
        {
     	   char* pch = strtok(line,"\t");
           if(strcmp(pch,"CHR")!=0)
           {
              //cout<<line<<endl;
              /*pch = strtok(NULL,"\t");
              pos1.push_back(pch);
              pch = strtok(NULL,"\t");
              pos2.push_back(pch);

              pch = strtok(NULL,"\t");
              c.push_back(atof(pch));
              */
               pch = strtok(NULL,"\t");
              start=pch;
              //pos1.push_back(pch);
              pch = strtok(NULL,"\t");
              stop=pch;
              //pos2.push_back(pch);

              pch = strtok(NULL,"\t");
              r2=atof(pch);
              //c.push_back(atof(pch));
              pch = strtok(NULL,"\t");
              pch = strtok(NULL,"\t");
              pch = strtok(NULL,"\t");
              pch = strtok(NULL,"\t");
              pch = strtok(NULL,"\t");
              if(phaseflag)
              {
                  pch = strtok(NULL,"\t");
              }
              mafval=atof(pch);
              //cout << start<<"\t"<<stop<<"\t"<<r2<<"\t"<<mafval<<endl;
              if(mafval>0.05)
              { 
                 pos1.push_back(start);
                 pos2.push_back(stop);
                 c.push_back(r2);   
                 maf.push_back(mafval);
              } 
           }
        }
   }


   for (int i=0; i<pos1.size() ; i++)
   {    	
        colmap.insert ( std::pair<string,double>((pos1[i]+"-"+pos2[i]),c[i]));
   }

   std::vector<string>::iterator it;
   it = unique (pos1.begin(), pos1.end());

   pos1.resize( std::distance(pos1.begin(),it) );


   long int p[pos1.size()][pos1.size()];
   long int q[pos1.size()][pos1.size()];
   float r[pos1.size()][pos1.size()];


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

   //char* staticdatafile;
   //strcpy(staticdatafile,jobdir);
   //ofstream outfile(strcat(staticdatafile,"/data.sample"), std::ofstream::app);
   ofstream outfile("/home/metabolomics/snipa/web/frontend/js/tmp/data.sample", std::ofstream::app);
   for (int i=1; i<(pos1.size()-1);i++)
   {
       for (int j=1; j<(pos1.size()-1);j++)
       {
               if(i<j)
               {
                 string color;
                 if(r[i][j]>=0.8){color=colarray[0];}
                 else if(r[i][j]>=0.5 && r[i][j]<0.8){color=colarray[1];}
                 else if(r[i][j]>=0.2 && r[i][j]<0.5){color=colarray[2];}
                 else if(r[i][j]>=0.05 && r[i][j]<0.2){color=colarray[5];}
                 else if(r[i][j]>=0.0 && r[i][j]<0.05){color=colarray[4];}

                 long int a1=(p[i][j]+p[i][j+1])/2;
                 long int a2=(p[i][j]+p[i+1][j])/2;
                 long int a3=(p[i][j]+p[i][j-1])/2;
                 long int a4=(p[i][j]+p[i-1][j])/2;


                 long int b1=(q[i][j]+q[i][j+1])/2;
                 long int b2=(q[i][j]+q[i+1][j])/2;
                 long int  b3=(q[i][j]+q[i][j-1])/2;
                 long int b4=(q[i][j]+q[i-1][j])/2;



                 double m2=(double)(b3-b1)/(double)(a3-a1);
                 if(m2 >  1){m2=2;}
                 else if(m2 <-1){m2=-2;}
                 double m4=m2;
                 double m1=(double)(b4-b2)/(double)(a4-a2);
                 if(m1 >  1){m1=2;}
                 else if(m1 < -1){m1=-2;}
                 double m3=m1;


                 //cout <<a1<<"\t"<<a2<<"\t"<<a3<<"\t"<<a4<<"\t"<<b1<<"\t"<<b2<<"\t"<<b3<<"\t"<<b4<<"\t"<<m1<<"\t"<<m2<<"\n";
                 long int c1=-m1*a1+b1;
                 long int c2=-m2*a2+b2;
                 long int c3=-m3*a3+b3;
                 long int c4=-m4*a4+b4;
                 //cout <<a1<<"\t"<<a2<<"\t"<<a3<<"\t"<<a4<<"\t"<<c1<<"\t"<<c2<<"\t"<<c3<<"\t"<<c4<<"\n";
                 vector <int> i1=intersection(m1,m2,c1,c2);
                 vector <int> i2=intersection(m2,m3,c2,c3);
                 vector <int> i3=intersection(m3,m4,c3,c4);
                 vector <int> i4=intersection(m4,m1,c4,c1);
                 if(i1[0]!=0)
                 {
                   //outfile<<i1[0]<<"\t"<<i1[1]<<"\t"<<i2[0]<<"\t"<<i2[1]<<"\t"<<i3[0]<<"\t"<<i3[1]<<"\t"<<i4[0]<<"\t"<<i4[1]<<"\t\""<<color<<"\""<<endl;
                   outfile<<i1[0]<<"\t"<<i1[1]<<"\t"<<i2[0]<<"\t"<<i2[1]<<"\t"<<i3[0]<<"\t"<<i3[1]<<"\t"<<i4[0]<<"\t"<<i4[1]<<"\t\""<<color<<"\""<<endl;
                 }
                 //outfile<<i1[0]<<"\t"<<i1[1]<<"\t"<<i2[0]<<"\t"<<i2[1]<<"\t"<<i3[0]<<"\t"<<i3[1]<<"\t"<<i4[0]<<"\t"<<i4[1]<<"\t"<<color<<endl;
               }
        }
   }

  outfile.close();

  if(ref==0)
  {

     ofstream out("data.line");
     for (int i =0; i<pos1.size();i++){out<<pos1[i]<<"\n";}
     out.close();

   }
 
   return 0;
}
