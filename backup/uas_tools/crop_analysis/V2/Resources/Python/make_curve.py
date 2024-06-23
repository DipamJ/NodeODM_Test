import growthmodels
import sys
import os
import numpy as np
from datetime import date
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt


# These are variables. These variables are set from the command below
# $command = "python3 '../Python/make_curve.py' " . $dates . " " . $values . " " . $type . " " . $parameters . " " . $startDate . " " . $lastDay . " 2>&1";
dates_column = sys.argv[1].split(',') #$dates
cc_data = [float(numeric_string) for numeric_string in sys.argv[2].split(',')] #$values
type = sys.argv[3] #$type
parameters = [float(numeric_string) for numeric_string in sys.argv[4].split(',')] #$parameters
start_date = [int(numeric_string) for numeric_string in sys.argv[5].split('/')] #$startDate
lday = int(sys.argv[6]) #$lastDay

# Make date after emerging
emerging_date = date(start_date[2], start_date[0], start_date[1])
dae = np.zeros(len(dates_column), dtype=np.int16)
for i in range(len(dates_column)):
    mm = int(dates_column[i][4:6])
    dd = int(dates_column[i][6:8])
    yy = int(dates_column[i][0:4])
    target_date = date(yy,mm,dd)
    delta = target_date - emerging_date
    dae[i] = delta.days

cc_arr = np.asarray(cc_data)

if type == "sigmoid":
	x, y, popt, pcov = growthmodels.fit_sigmod(dae, cc_arr, last_day = lday, init_param = np.asarray(parameters))

elif type == "logistic":
	x, y, popt, pcov = growthmodels.fit_logistic(dae, cc_arr, last_day = lday, init_param = np.asarray(parameters))

elif type == "richard4":
	x, y, popt, pcov = growthmodels.fit_richard4(dae, cc_arr, last_day = lday, init_param = np.asarray(parameters))

# Added
elif type == "heatmap":
	x, y, popt, pcov = growthmodels.fit_heatmap(dae, cc_arr, last_day = lday, init_param = np.asarray(parameters)) # Modify this function to be used for heatmap
# Added

else:
	x, y, popt, pcov = growthmodels.fit_richard5(dae, cc_arr, last_day = lday, init_param = np.asarray(parameters))

dy = growthmodels.numerical_first_derivative(x,y)

ymax = y.max()
dymax = dy.max()
index_max = np.where(dy == dymax)[0][0]
xmax = x[index_max]
hm1 = growthmodels.find_half_max_location(dy)
hm2 = growthmodels.find_half_max_location_backward(dy)
delta = hm2 - hm1

file = open(os.path.dirname(os.path.abspath(__file__)) + "/chart.txt","w+")
file.write(str(y))
file.close()

file = open(os.path.dirname(os.path.abspath(__file__)) + "/gr_chart.txt","w+")
file.write(str(dy))
file.close()

file = open(os.path.dirname(os.path.abspath(__file__)) + "/gr_chart_features.txt","w+")
file.write(str(xmax) + "," + str(dymax) + "," + str(delta) + "," + str(hm1) + "," + str(hm2))
file.close()

file = open(os.path.dirname(os.path.abspath(__file__)) + "/popt.txt","w+")
file.write(str(popt))
file.close()

file = open(os.path.dirname(os.path.abspath(__file__)) + "/pcov.txt","w+")
file.write(popt)
file.close()
