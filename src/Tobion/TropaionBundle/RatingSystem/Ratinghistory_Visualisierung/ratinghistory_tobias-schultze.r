setwd("C:/Dokumente und Einstellungen/Tubo/Desktop/")
library(ggplot2)

# Elo
data <- read.csv(file="ratinghistory_tobias-schultze.csv",sep=";",head=TRUE)

qplot(as.Date(created_at), rating, data=data, geom="line", group = discipline, color = discipline) + 
scale_x_date(format = "%b %Y") +
scale_colour_hue("Disziplin", breaks = c("singles","doubles"), labels = c("Einzel","Doppel")) + 
opts(legend.position = c(0.22,0.8), legend.background = theme_rect(col = 0)) +
xlab("") + 
ylab("Rating") + 
opts(title = "Rating-Entwicklung von Tobias Schultze") 

breaks = c("singles","doubles","mixed"), labels = c("Einzel","Doppel","Mixed")



data <- read.csv(file="ratinghistory_kwo.csv",sep=";",head=TRUE)

# Spielervergleich
qplot(as.Date(created_at), rating, data=data, geom="line", color = discipline) + 
facet_grid(. ~ athlete_name, scales = "free") +
# stat_smooth() +
scale_x_date(format = "%y") +
scale_colour_hue("Disziplin", breaks = c("singles","doubles","mixed"), labels = c("Einzel","Doppel","Mixed")) + 
opts(legend.position = c(0.22,0.8), legend.background = theme_rect(col = 0)) +
xlab("") + 
ylab("Rating") + 
opts(title = "Rating-Entwicklung im Vergleich")





# TrueSkill
data <- read.csv(file="trueskillhistory_tobias-schultze.csv",sep=";",head=TRUE)

# Standardabweichung als Liniendicke
qplot(as.Date(created_at), skill_mean, data=data, size=skill_std, geom="line", group = discipline, color = discipline) + 
scale_x_date(format = "%b %Y") +
scale_colour_hue("Disziplin", breaks = c("singles","doubles"), labels = c("Einzel","Doppel")) + 
scale_size_identity() + 
opts(legend.position = c(0.22,0.8), legend.background = theme_rect(col = 0)) +
xlab("") + 
ylab("TrueSkill") + 
opts(title = "TrueSkill-Entwicklung von Tobias Schultze") 


# Mit Band das das Rating im Intervall de 3fachen Standardabweichung
qplot(as.Date(created_at), skill_mean, data=data, geom="line", group = discipline, color = discipline, size = 1.2) + 
scale_x_date(format = "%b %Y") +
scale_colour_hue("Disziplin", breaks = c("singles","doubles"), labels = c("Einzel","Doppel")) + 
geom_ribbon(aes(ymin = skill_mean - 3 * skill_std, ymax = skill_mean + 3 * skill_std, fill = discipline, linetype=3, alpha = 0.2, size = 0.5)) +
scale_size_identity() + 
scale_fill_hue(legend=FALSE) +
scale_alpha(legend=FALSE) +
opts(legend.position = c(0.5,0.2), legend.background = theme_rect(col = 0)) +
xlab("") + 
ylab("TrueSkill") + 
opts(title = "TrueSkill-Entwicklung von Tobias Schultze") 


data <- read.csv(file="trueskillhistory_kwo.csv",sep=";",head=TRUE)

# Spielervergleich
qplot(as.Date(created_at), skill_mean, data=data, geom="line", color = discipline) + 
facet_grid(. ~ athlete_name, scales = "free") +
# stat_smooth() +
scale_x_date(format = "%y") +
scale_colour_hue("Disziplin", breaks = c("singles","doubles","mixed"), labels = c("Einzel","Doppel","Mixed")) + 
opts(legend.position = c(0.22,0.8), legend.background = theme_rect(col = 0)) +
xlab("") + 
ylab("Rating") + 
opts(title = "Rating-Entwicklung im Vergleich")
