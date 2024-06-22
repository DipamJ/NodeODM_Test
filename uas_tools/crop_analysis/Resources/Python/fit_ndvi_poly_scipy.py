import growthmodels
import sys
import os
import numpy as np
from datetime import date
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt

from sklearn.linear_model import Ridge
from sklearn.preprocessing import PolynomialFeatures
from sklearn.pipeline import make_pipeline

from scipy import optimize


dates_column = sys.argv[1].split(',')
ndvi_data = [float(numeric_string) for numeric_string in sys.argv[2].split(',')]
start_date = [int(numeric_string) for numeric_string in sys.argv[3].split('/')]
lday = int(sys.argv[4])
degree = int(sys.argv[5])
gamma = float(sys.argv[6])

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
#y = f(x)
def test_func(x, a, b,c):
     return a*x**3 + b*x**2 + c*x

params, params_covariance = optimize.curve_fit(test_func, dap, ndvi_arr)

file = open(os.path.dirname(os.path.abspath(__file__)) + "/ndvi_chart.txt","w+") 
file.write(str(params)) 
file.close()