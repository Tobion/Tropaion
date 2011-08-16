GaussianScale <- function(x, mean, sd, add) {
	y <- exp(-0.5*((x - mean) / sd)^2)
	ifelse(x > mean, add + y, 2 + add - y)
}

default_kfactor = 24;

# Variante 1
curve(GaussianScale(x, 63.9, 23.2, 0.1) * default_kfactor, -10, 150, n=1000, 
	type="l",lwd=2, col="red", 
	xlab='Quotient aus Verliererpunkte durch Gewinnerpunkte in Prozent', 
	ylab='k-Faktor', 
	main='Dynamik des Elo-Rating-Systems'
)

# Variante 2
x = seq(-10,140,length=1000)
y <- GaussianScale(x, 63.9, 23.2, 0.1) * default_kfactor
plot(x,y,type="l",lwd=2,col="red",xlab='Quotient aus Verliererpunkte durch Gewinnerpunkte in Prozent', ylab='k-Faktor', main='Reaktionsfreudigkeit des Elo-Rating-Systems')

# Variante 3
funcplot <- data.frame(x=x, y=GaussianScale(x,63.9, 23.2, 0.1) * default_kfactor)
ggplot(funcplot, aes(x=x, y=y)) + geom_line()






http://en.wikipedia.org/wiki/Cumulative_distribution_function#Complementary_cumulative_distribution_function
curve(((1 - pnorm(x, mean=63.9, sd=23.2)) * 2 + 0.1) * default_kfactor, -10, 150, n=1000, 
	type="l",lwd=2, col="red", 
	xlab='Quotient aus Verliererpunkte durch Gewinnerpunkte in Prozent', 
	ylab='k-Faktor', 
	main='Dynamik des Elo-Rating-Systems'
)

Statt Normalverteilung könnten auch
http://en.wikipedia.org/wiki/Log-normal_distribution
und
http://en.wikipedia.org/wiki/Gamma_distribution
vorliegen.