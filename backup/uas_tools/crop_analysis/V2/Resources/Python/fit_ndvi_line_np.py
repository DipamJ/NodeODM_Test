import growthmodels
import sys
import os
import numpy as np
from datetime import date
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import pylab
Polynomial = np.polynomial.Polynomial

dates_column = sys.argv[1].split(',')
ndvi_data = [float(numeric_string) for numeric_string in sys.argv[2].split(',')]
start_date = [int(numeric_string) for numeric_string in sys.argv[3].split('/')]
lday = int(sys.argv[4])
name = sys.argv[5]


# Make date after emerging
emerging_date = date(start_date[2], start_date[0], start_date[1])
dap = np.zeros(len(dates_column), dtype=np.int16)
for i in range(len(dates_column)):
    mm = int(dates_column[i][4:6])
    dd = int(dates_column[i][6:8])
    yy = int(dates_column[i][0:4])
    target_date = date(yy,mm,dd)
    delta = target_date - emerging_date
    dap[i] = delta.days

ndvi_arr = np.asarray(ndvi_data)

cmin, cmax = min(dap), max(dap)
pfit, stats = Polynomial.fit(dap, ndvi_arr , 1, full=True, window=(cmin, cmax), domain=(cmin, cmax))

file = open(os.path.dirname(os.path.abspath(__file__)) + "/" + name,"w+") 
file.write(str(pfit(dap))) 
file.close()