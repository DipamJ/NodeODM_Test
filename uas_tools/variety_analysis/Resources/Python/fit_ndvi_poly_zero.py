import growthmodels
import sys
import os
import numpy as np
from datetime import date
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
from scipy import asarray as ar,exp

def fit_poly_through_origin(x, y, n=1):
    a = x[:, np.newaxis] ** np.arange(1, n+1)
    coeff = np.linalg.lstsq(a, y)[0]
    return np.concatenate(([0], coeff))


dates_column = sys.argv[1].split(',')
ndvi_data = [float(numeric_string) for numeric_string in sys.argv[2].split(',')]
start_date = [int(numeric_string) for numeric_string in sys.argv[3].split('/')]
lday = int(sys.argv[4])
degree = int(sys.argv[5])

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

# calculate polynomial
#fit = np.polyfit(dap, ndvi_arr, degree)
#f = np.poly1d(fit)

#x = np.linspace(dap[0], dap[-1], lday)
#x = ar(range(lday))
#y = f(x)

c1 = fit_poly_through_origin(dap, ndvi_arr, degree)
p1 = np.polynomial.Polynomial(c1)
x = ar(range(lday))
y = p1(x)

file = open(os.path.dirname(os.path.abspath(__file__)) + "/ndvi_chart.txt","w+") 
file.write(str(y)) 
file.close()