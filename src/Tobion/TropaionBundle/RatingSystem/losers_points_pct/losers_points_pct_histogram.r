setwd("C:/Dokumente und Einstellungen/Tubo/Eigene Dateien/Studium/FHTW B.Sc. Angew. Inf/Bachelorarbeit/BVBB/Statistiken/losers_points_pct/")
data <- read.csv(file="losers_points_pct.csv",sep=";",head=TRUE)
library(ggplot2)

mean(data$losers_original_points_pct)
sd(data$losers_original_points_pct)

hist(data$losers_original_points_pct, breaks=30, cex.main=0.95, main='Histogramm zum Verhältnis der Punkte des Verlierers zu denen des Gewinners', xlab='Quotient aus Verliererpunkte durch Gewinnerpunkte in Prozent', ylab='Häufigkeit')

# Other charts
qqnorm(data$losers_original_points_pct)
plot(data$losers_original_points_pct,data$std_orig_smaller_div_bigger_gamescore_permil)
boxplot(data$losers_original_points_pct,horizontal=TRUE)
stripchart(data$losers_original_points_pct)


# http://had.co.nz/ggplot2/geom_histogram.html
install.packages("ggplot2")
library(ggplot2)

# Basic
qplot(losers_original_points_pct, data=data, xlab='Quotient aus Verliererpunkte durch Gewinnerpunkte in Prozent', ylab='Häufigkeit', main='Histogramm zum Verhältnis der Punkte des Verlierers zu denen des Gewinners', binwidth=5)

# Fill
ggplot(data, aes(x=losers_original_points_pct)) + geom_histogram(binwidth=5, colour="black", aes(fill = ..count..)) + 
scale_fill_gradient("Häufigkeit") + 
xlab("Quotient aus Verliererpunkte durch Gewinnerpunkte in Prozent") + 
ylab("Häufigkeit")

# Density
ggplot(data, aes(x=losers_original_points_pct)) + geom_histogram(binwidth=5, aes(y = ..density..)) + geom_density()

# Facet
ggplot(data, aes(x=losers_original_points_pct)) + geom_histogram(binwidth=5) + facet_grid(~ match_type)
ggplot(data, aes(x=losers_original_points_pct)) + geom_histogram(binwidth=5) + facet_grid(~ games)
 
# stacked histogramm with normal distribution
ggplot(data, aes(x=losers_original_points_pct, fill=factor(games))) +
geom_bar(binwidth=5, colour="black") + # dodge version: position="dodge", aes(y = ..density..)
scale_fill_hue("Sätze", breaks = c("2", "3"), labels = c("2-Satz-Spiel", "3-Satz-Spiel")) +
stat_function(
		fun = function(x, mean, sd, n){
			4250 * sd * sqrt(2 * pi) * dnorm(x = x, mean = mean, sd = sd)
		}, 
		args = with(data, c(mean=mean(losers_original_points_pct), sd=sd(losers_original_points_pct), n=length(losers_original_points_pct))),
		lwd=1.2, # col="darkblue",
		aes(colour="µ = 63.9\nσ = 23.2")
) + 
scale_colour_manual("Normalverteilung", values = c("darkblue")) +
opts(legend.position = c(0.85,0.7), legend.background = theme_rect(col = 0)) +
xlab("Quotient aus Verliererpunkte durch Gewinnerpunkte in Prozent") + 
ylab("Häufigkeit") # + opts(title = "Stacked Histogram with Normal Curve")

#legend("bottomright", "µ = 63.9\nσ = 23.2", inset = -0.04, border="", bty="n", box.lwd=0, lwd=2.5, cex=0.9, col="darkblue", title="Normalverteilung")

# Dodge histogramm
ggplot(data, aes(x=losers_original_points_pct, fill=factor(games))) +
geom_bar(binwidth=5, colour="black",position="dodge") + 
scale_fill_hue("Sätze", breaks = c("2", "3"), labels = c("2-Satz-Spiel", "3-Satz-Spiel")) + 
opts(legend.position = c(0.85,0.7), legend.background = theme_rect(col = 0)) +
xlab("Quotient aus Verliererpunkte durch Gewinnerpunkte in Prozent") + 
ylab("Häufigkeit") 

title(main="Histogramm zum Verhältnis der Punkte des Verlierers zu denen des Gewinners", cex.main = 1)



# Syntax-Hervorhebung für Dokumentation
# http://romainfrancois.blog.free.fr/index.php?post/2009/11/22/new-R-package-:-highlight
install.packages("highlight")
library(highlight)
highlight( "losers_points_pct_histogram.r", renderer = renderer_html(), output = "losers_points_pct_histogram.r.html" )