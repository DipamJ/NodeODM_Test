import growthmodels
import sys
import os
import numpy as np
from datetime import date
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
from scipy import asarray as ar,exp

from sklearn.linear_model import Ridge
from sklearn.preprocessing import PolynomialFeatures
from sklearn.pipeline import make_pipeline

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

def f(x):
    return x * np.sin(x)


# generate points used to plot
x_plot = ar(range(lday))
#x_plot = np.linspace(0, 10, 100)

# generate points and keep a subset of them
x = dap
y = ndvi_arr

#x = np.linspace(0, 10, 100)
#rng = np.random.RandomState(0)
#rng.shuffle(x)
#x = np.sort(x[:20])
#y = f(x)

# create matrix versions of these arrays
X = x[:, np.newaxis]
X_plot = x_plot[:, np.newaxis]

model = make_pipeline(PolynomialFeatures(degree), Ridge())
model.fit(X, y)
y_plot = model.predict(X_plot)

file = open(os.path.dirname(os.path.abspath(__file__)) + "/ndvi_chart.txt","w+") 
file.write(str(y_plot)) 
file.close()