import growthmodels
import sys
import os
import numpy as np
from datetime import date
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt

import pandas as pd
from scipy.optimize import curve_fit
from scipy import asarray as ar,exp
from sklearn.svm import SVR

dates_column = sys.argv[1].split(',')
ndvi_data = [float(numeric_string) for numeric_string in sys.argv[2].split(',')]
start_date = [int(numeric_string) for numeric_string in sys.argv[3].split('/')]
lday = int(sys.argv[4])
c = float(sys.argv[5])
gamma = float(sys.argv[6])
max = float(sys.argv[7])
mdate = [int(numeric_string) for numeric_string in sys.argv[8].split('/')]

# Make date after emerging
emerging_date = date(start_date[2], start_date[0], start_date[1])
max_date = date(mdate[2], mdate[0], mdate[1])

dap = np.zeros(len(dates_column), dtype=np.int16)
for i in range(len(dates_column)):
    mm = int(dates_column[i][4:6])
    dd = int(dates_column[i][6:8])
    yy = int(dates_column[i][0:4])
    target_date = date(yy,mm,dd)
    delta = target_date - emerging_date
    deltaMed = max_date - emerging_date
    dap[i] = delta.days - deltaMed.days

file = open(os.path.dirname(os.path.abspath(__file__)) + "/dap.txt","w+") 
file.write(str(dap)) 
file.close()
	
ndvi_arr = np.asarray(ndvi_data)
x = ar(range(lday))
weight = ar(range(len(dap),0, -1))

#clf = SVR(kernel='rbf',C=c, gamma=gamma)
clf = SVR(kernel='poly', C=c, degree = 3, gamma = gamma)
#clf.fit(dap.reshape(-1,1), ndvi_arr)
clf.fit(dap.reshape(-1,1), ndvi_arr, weight)

y = clf.predict(x.reshape(-1,1))
#y = clf.predict(x.reshape(len(x),1))

file = open(os.path.dirname(os.path.abspath(__file__)) + "/ndvi_chart.txt","w+") 
file.write(str(y)) 
file.close()
