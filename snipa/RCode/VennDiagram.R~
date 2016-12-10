library(VennDiagram)
args <- commandArgs(trailingOnly = TRUE)
print(args[1])
jobdir<-args[8];
venn.plot <- draw.triple.venn(
area1 = as.numeric(args[1]),
area2 = as.numeric(args[2]),
area3 = as.numeric(args[3]),
n12 = as.numeric(args[4]),
n23 = as.numeric(args[5]),
n13 = as.numeric(args[6]),
n123 = as.numeric(args[7]),
category = c("GenoQ", "1000 Genome", "dbSNP"),
fill = c("blue", "red", "green"),
lty = "blank",
cex = 8,
cat.cex = 8,
cat.col = c("blue", "red", "green")
);
plot.new();
png(paste0(jobdir,"/venndiagram.png"),width=3000,height=3000)
grid.draw(venn.plot);
dev.off();

