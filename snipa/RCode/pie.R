args <- commandArgs(trailingOnly = TRUE)
jobdir<-args[8]
cat("[{\"type\": \"pie\",\"name\": \"SNPs\",\"data\": [[\"Downrstream\",",as.numeric(args[1]),"],[\"Intron\",",as.numeric(args[2]),"],[\"Non Synonymous Coding\",",as.numeric(args[3]),"],[\"Synonymous Coding\",",as.numeric(args[4]),"],[\"Upstream\",",as.numeric(args[5]),"],[\"UTR 3 Prime\",",as.numeric(args[6]),"],[\"UTR 5 Prime\",",as.numeric(args[7]),"]]}]",file=paste0(jobdir,"data.json"),"\n")

#cat("[{\"type\": \"pie\",\"name\": \"SNPs\",\"data\": [[\"Downrstream\",",as.numeric(args[1]),"],[\"Intron\",",as.numeric(args[2]),"],[\"Non Synonymous Coding\",",as.numeric(args[3]),"],[\"Synonymous Coding\",",as.numeric(args[4]),"],[\"Upstream\",",as.numeric(args[5]),"],[\"UTR 3 Prime\",",as.numeric(args[6]),"],[\"UTR 5 Prime\",",as.numeric(args[7]),"]]}]",file=jsonpath,"\n")


