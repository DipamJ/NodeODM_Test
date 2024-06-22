import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import os
from scipy.optimize import curve_fit
from scipy import asarray as ar,exp
from lmfit.models import SkewedGaussianModel
from sklearn.svm import SVR
import numbers
import ipywidgets as widgets
from ipywidgets import interact_manual
import warnings
warnings.simplefilter('ignore')
bin_size_to_use = 5
percentile_to_use = 50
def gauss(x,a,x0,sigma):
return a*exp(-(x-x0)**2/(2*sigma**2))
def cal_yield_potential(plant_count,
yield_est,
bin_size=5,
bin_min=0,
bin_max=100):
"""
Calculate yield potential from plant_count and yield estimates
"""
# Check if plant_count and yield_est has the same size
if plant_count.shape != yield_est.shape:
print("plant_count and yield_est dimension does not match...")
return None, None
if ((bin_max - bin_min) % bin_size) == 0:
num_bin = int(np.floor((bin_max - bin_min) / bin_size))
else:
num_bin = int(np.floor((bin_max - bin_min) / bin_size)+1)
1
yield_sum = np.zeros(num_bin, dtype=np.float32)
yield_avg = np.zeros(num_bin, dtype=np.float32)
yield_max = np.zeros(num_bin, dtype=np.float32)
yield_min = np.zeros(num_bin, dtype=np.float32)
yield_med = np.zeros(num_bin, dtype=np.float32)
count_index = np.zeros(num_bin, dtype=np.float32)
temp_out = []
for i in range(num_bin):
temp_min = 0 + i*bin_size
temp_max = temp_min + bin_size
count_index[i] = (temp_max + temp_min) / 2.0
cond1 = plant_count >= temp_min
cond2 = plant_count < temp_max
cond = cond1*cond2
temp_out.append(yield_est[cond])
for i in range(num_bin):
if len(temp_out[i]) == 0:
continue
yield_sum[i] = temp_out[i].sum()
yield_avg[i] = temp_out[i].mean()
yield_min[i] = temp_out[i].min()
yield_max[i] = temp_out[i].max()
yield_med[i] = np.median(temp_out[i])
return count_index, yield_sum, yield_avg, yield_med, yield_min, yield_max
def cal_yield_potential_percentile(plant_count,yield_est,percentile,
bin_size=5,bin_min=0,bin_max=100):
"""
Calculate yield potential from plant_count and yield estimates
"""
# Check if plant_count and yield_est has the same size
if plant_count.shape != yield_est.shape:
print("plant_count and yield_est dimension does not match...")
return None, None
if ((bin_max - bin_min) % bin_size) == 0:
num_bin = int(np.floor((bin_max - bin_min) / bin_size))
else:
num_bin = int(np.floor((bin_max - bin_min) / bin_size)+1)
yield_percentile = np.zeros(num_bin, dtype=np.float32)
2
count_index = np.zeros(num_bin, dtype=np.float32)
temp_out = []
for i in range(num_bin):
temp_min = 0 + i*bin_size
temp_max = temp_min + bin_size
count_index[i] = (temp_max + temp_min) / 2.0
cond1 = plant_count >= temp_min
cond2 = plant_count < temp_max
cond = cond1*cond2
temp_out.append(yield_est[cond])
for i in range(num_bin):
if len(temp_out[i]) == 0:
continue
yield_percentile[i] = np.percentile(temp_out[i], percentile)
return count_index, yield_percentile
in_dir = "/home/jinha/garslab_ace/AgriLife-CorpusChristi/2016/05_Cotton_Sorghum/gis_layers/"
# Canopy cover file
cc_basename = "cotton_2016_plot_canopy_cover.xls"
cc_fn = os.path.join(in_dir, cc_basename)
cc = pd.read_excel(cc_fn)
# Open boll count file
ob_basename = "cotton_2016_OCB_Final.xlsx"
ob_fn = os.path.join(in_dir, ob_basename)
ob = pd.read_excel(ob_fn)
# add machine harvest field
ob.insert(14, "yield (lbs/acre)", 0)
# Machine harvest data
field_data_dir = "/home/jinha/garslab_ace/AgriLife-CorpusChristi/2016/05_Cotton_Sorghum/field_data"
mh_fn = os.path.join(field_data_dir, "marchine_harvest-yield_per_row_by_jinha_index.csv")
mh = pd.read_csv(mh_fn)
# Drop last column that has no data
mh = mh.drop(columns=['Unnamed: 14']);
# Calculate machine harvest yield per grid
for index, row in mh.iterrows():
# Find only rows that satisfy the condision
temp_ob_bollsize_sum = ob.loc[(ob['row'] == row['row']) &
(ob['col'] == row['col']) &
3
(ob['line'] == row['line']) &
(ob['irrigation'] == row['irrigation']),
'bollsize'].sum()
# Each grid is 38^2 square inch = 0.9652^2 m^2
# 1 acre = 4046.86 m^2
lb_per_acre_factor = 4046.86 * 0.9652**2
# Now find corresponding record from open boll
ob.loc[(ob['row'] == row['row']) &
(ob['col'] == row['col']) &
(ob['line'] == row['line']) &
(ob['irrigation'] == row['irrigation']),
'yield (lbs/acre)'] = row['Weight per row (lbs)'] * lb_per_acre_factor * ob['# Get unique variety name
cc_uniq_names = cc['name'].unique()
def update_graph(var_name, irr, cc_date_to_use, percentile_to_use, bin_size_to_use=5):
"""
Update graph based on user selection
"""
if irr == "No":
##### Dry land
cc_dry = cc.loc[(cc['name'] == var_name) &
(cc['irrigation'] == "No") &
(cc['breeder'] != "Filler")].fillna(0)
ob_dry = ob.loc[(ob['name'] == var_name) &
(ob['irrigation'] == "No") &
(cc['breeder'] != "Filler")].fillna(0)
ind1,ysum1,yavg1,ymed1,ymin1,ymax1=cal_yield_potential(
cc_dry[cc_date_to_use].values,
ob_dry['yield (lbs/acre)'].values,
bin_size=bin_size_to_use,
bin_min=cc_dry[cc_date_to_use].min(),
bin_max=cc_dry[cc_date_to_use].max())
ind1,ypct1 = cal_yield_potential_percentile(
cc_dry[cc_date_to_use].values,
ob_dry['yield (lbs/acre)'].values,
percentile_to_use,
bin_size = bin_size_to_use,
bin_min = cc_dry[cc_date_to_use].min(),
bin_max = cc_dry[cc_date_to_use].max())
# Only use bins that we have values
4
bin_cond = ypct1 > 0
x=ind1[bin_cond]
y=ypct1[bin_cond]
xx = ar(range(100))
# # Row and Column title
# cols = ['Dry Land', 'Wet Land']
# rows = ['Gaussian', 'Skewed Gaussian', 'SVR']
fig, axes = plt.subplots(3,1,sharex=True,sharey=True,figsize=(7,9),
constrained_layout=True)
# for ax, col in zip(axes[0], cols):
# ax.set_title(col)
# for ax, row in zip(axes[:,0], rows):
# ax.set_ylabel(row, rotation=90, size='large')
# fig.tight_layout()
# Gaussian fitting
popt,pcov = curve_fit(gauss,x,y,p0=[y.max(),60,10])
axes[0].scatter(cc_dry[cc_date_to_use].values, ob_dry['yield (lbs/acre)'].values, axes[0].plot(ind1[bin_cond],ymax1[bin_cond],'r+:', label='max')
axes[0].plot(ind1[bin_cond],ypct1[bin_cond],'m+:', label='%d pct' % percentile_to_use)
axes[0].plot(ind1[bin_cond],ymed1[bin_cond],'g+:', label='med')
axes[0].plot(ind1,ymin1,'b+:', label='min')
axes[0].plot(xx,gauss(xx,*popt), 'c-', label='Gaussian')
axes[0].legend()
axes[0].grid(True)
# Skewed Gaussian fitting
model = SkewedGaussianModel()
params = model.make_params(amplitude=y.max(),
center=50,
sigma=10,
gamma=0)
result = model.fit(y, params, x=x)
axes[1].scatter(cc_dry[cc_date_to_use].values, ob_dry['yield (lbs/acre)'].values, axes[1].plot(ind1[bin_cond],ymax1[bin_cond],'r+:', label='max')
axes[1].plot(ind1[bin_cond],ypct1[bin_cond],'m+:', label='%d pct' % percentile_to_use)
axes[1].plot(ind1[bin_cond],ymed1[bin_cond],'g+:', label='med')
axes[1].plot(ind1,ymin1,'b+:', label='min')
5
axes[1].plot(xx, result.eval(x=xx), 'c-', label='Skwed')
axes[1].legend()
axes[1].grid(True)
# Support Vector Regression fitting
clf = SVR(kernel='rbf',C=1e3, gamma=0.001)
clf.fit(x.reshape(len(x),1), y)
axes[2].scatter(cc_dry[cc_date_to_use].values, ob_dry['yield (lbs/acre)'].values, axes[2].plot(ind1[bin_cond],ymax1[bin_cond],'r+:', label='max')
axes[2].plot(ind1[bin_cond],ypct1[bin_cond],'m+:', label='%d pct' % percentile_to_use)
axes[2].plot(ind1[bin_cond],ymed1[bin_cond],'g+:', label='med')
axes[2].plot(ind1,ymin1,'b+:', label='min')
axes[2].plot(xx,clf.predict(xx.reshape(len(xx),1)), 'c-', label='SVR')
axes[2].legend()
axes[2].grid(True)
if isinstance(var_name, numbers.Number):
out_fig_fn = "Dryland: %d-%d_pct-bin_size_%d" % (var_name, percentile_to_use, else:
out_fig_fn = "Dryland: " + var_name + "-%d_pct-bin_size_%d" % (percentile_to_use, fig.suptitle(out_fig_fn)
if irr == "Yes":
####### Wet land
cc_wet = cc.loc[(cc['name'] == var_name) &
(cc['irrigation'] == "Yes") &
(cc['breeder'] != "Filler")].fillna(0)
ob_wet = ob.loc[(ob['name'] == var_name) &
(ob['irrigation'] == "Yes") &
(cc['breeder'] != "Filler")].fillna(0)
ind2,ysum2,yavg2,ymed2,ymin2,ymax2=cal_yield_potential(
cc_wet[cc_date_to_use].values,
ob_wet['yield (lbs/acre)'].values,
bin_size=bin_size_to_use,
bin_min=0,
bin_max=100)
ind2,ypct2 = cal_yield_potential_percentile(
cc_wet[cc_date_to_use].values,
ob_wet['yield (lbs/acre)'].values,
percentile_to_use,
bin_size = bin_size_to_use,
bin_min = 0,
6
bin_max = 100)
# Only use bins that we have values
bin_cond = ypct2 > 0
x=ind2[bin_cond]
y=ypct2[bin_cond]
xx = ar(range(100))
# # Row and Column title
# cols = ['Dry Land', 'Wet Land']
# rows = ['Gaussian', 'Skewed Gaussian', 'SVR']
fig, axes = plt.subplots(3,1,sharex=True,sharey=True,figsize=(7,9),
constrained_layout=True)
# for ax, col in zip(axes[0], cols):
# ax.set_title(col)
# for ax, row in zip(axes[:,0], rows):
# ax.set_ylabel(row, rotation=90, size='large')
# fig.constrained_layout()
# Gaussian fitting
popt,pcov = curve_fit(gauss,x,y,p0=[y.max(),50,10])
axes[0].scatter(cc_wet[cc_date_to_use].values, ob_wet['yield (lbs/acre)'].values, axes[0].plot(ind2[bin_cond],ymax2[bin_cond],'r+:', label='max')
axes[0].plot(ind2[bin_cond],ypct2[bin_cond],'m+:', label='%d pct' % percentile_to_use)
axes[0].plot(ind2[bin_cond],ymed2[bin_cond],'g+:', label='med')
axes[0].plot(ind2,ymin2,'b+:', label='min')
axes[0].plot(xx,gauss(xx,*popt), 'c-', label='Gaussian')
axes[0].legend()
axes[0].grid(True)
# Skewed Gaussian fitting
model = SkewedGaussianModel()
params = model.make_params(amplitude=y.max(),
center=50,
sigma=10,
gamma=0)
result = model.fit(y, params, x=x)
axes[1].scatter(cc_wet[cc_date_to_use].values, ob_wet['yield (lbs/acre)'].values, axes[1].plot(ind2[bin_cond],ymax2[bin_cond],'r+:', label='max')
axes[1].plot(ind2[bin_cond],ypct2[bin_cond],'m+:', label='%d pct' % percentile_to_use)
7
axes[1].plot(ind2[bin_cond],ymed2[bin_cond],'g+:', label='med')
axes[1].plot(ind2,ymin2,'b+:', label='min')
axes[1].plot(xx, result.eval(x=xx), 'c-', label='Skwed')
axes[1].legend()
axes[1].grid(True)
# Support Vector Regression fitting
clf = SVR(kernel='rbf',C=1e3, gamma=0.001)
clf.fit(x.reshape(len(x),1), y)
axes[2].scatter(cc_wet[cc_date_to_use].values, ob_wet['yield (lbs/acre)'].values, axes[2].plot(ind2[bin_cond],ymax2[bin_cond],'r+:', label='max')
axes[2].plot(ind2[bin_cond],ypct2[bin_cond],'m+:', label='%d pct' % percentile_to_use)
axes[2].plot(ind2[bin_cond],ymed2[bin_cond],'g+:', label='med')
axes[2].plot(ind2,ymin2,'b+:', label='min')
axes[2].plot(xx,clf.predict(xx.reshape(len(xx),1)), 'c-', label='SVR')
axes[2].legend()
axes[2].grid(True)
if isinstance(var_name, numbers.Number):
out_fig_fn = "Wetland: %d-%d_pct-bin_size_%d" % (var_name, percentile_to_use, else:
out_fig_fn = "Wetland: " + var_name + "-%d_pct-bin_size_%d" % (percentile_to_use, fig.suptitle(out_fig_fn)
if irr == "Both":
##### Dry land
cc_dry = cc.loc[(cc['name'] == var_name) &
(cc['irrigation'] == "No") &
(cc['breeder'] != "Filler")].fillna(0)
ob_dry = ob.loc[(ob['name'] == var_name) &
(ob['irrigation'] == "No") &
(cc['breeder'] != "Filler")].fillna(0)
ind1,ysum1,yavg1,ymed1,ymin1,ymax1=cal_yield_potential(
cc_dry[cc_date_to_use].values,
ob_dry['yield (lbs/acre)'].values,
bin_size=bin_size_to_use,
bin_min=0,
bin_max=100)
ind1,ypct1 = cal_yield_potential_percentile(
cc_dry[cc_date_to_use].values,
ob_dry['yield (lbs/acre)'].values,
8
percentile_to_use,
bin_size = bin_size_to_use,
bin_min = 0,
bin_max = 100)
# Only use bins that we have values
bin_cond = ypct1 > 0
x=ind1[bin_cond]
y=ypct1[bin_cond]
xx = ar(range(100))
# Row and Column title
cols = ['Dryland', 'Wetland']
rows = ['Gaussian', 'Skewed Gaussian', 'SVR']
fig, axes = plt.subplots(3,2,sharex=True,sharey=True,figsize=(14,9),
constrained_layout=True)
for ax, col in zip(axes[0], cols):
ax.set_title(col)
for ax, row in zip(axes[:,0], rows):
ax.set_ylabel(row, rotation=90, size='large')
# Gaussian fitting
popt,pcov = curve_fit(gauss,x,y,p0=[y.max(),60,10])
axes[0,0].scatter(cc_dry[cc_date_to_use].values, ob_dry['yield (lbs/acre)'].values, axes[0,0].plot(ind1[bin_cond],ymax1[bin_cond],'r+:', label='max')
axes[0,0].plot(ind1[bin_cond],ypct1[bin_cond],'m+:', label='%d pct' % percentile_to_use)
axes[0,0].plot(ind1[bin_cond],ymed1[bin_cond],'g+:', label='med')
axes[0,0].plot(ind1,ymin1,'b+:', label='min')
axes[0,0].plot(xx,gauss(xx,*popt), 'c-', label='Gaussian')
axes[0,0].legend()
axes[0,0].grid(True)
# Skewed Gaussian fitting
model = SkewedGaussianModel()
params = model.make_params(amplitude=y.max(),
center=50,
sigma=10,
gamma=0)
result = model.fit(y, params, x=x)
axes[1,0].scatter(cc_dry[cc_date_to_use].values, ob_dry['yield (lbs/acre)'].values, axes[1,0].plot(ind1[bin_cond],ymax1[bin_cond],'r+:', label='max')
9
axes[1,0].plot(ind1[bin_cond],ypct1[bin_cond],'m+:', label='%d pct' % percentile_to_use)
axes[1,0].plot(ind1[bin_cond],ymed1[bin_cond],'g+:', label='med')
axes[1,0].plot(ind1,ymin1,'b+:', label='min')
axes[1,0].plot(xx, result.eval(x=xx), 'c-', label='Skwed')
axes[1,0].legend()
axes[1,0].grid(True)
# Support Vector Regression fitting
clf = SVR(kernel='rbf',C=1e3, gamma=0.001)
clf.fit(x.reshape(len(x),1), y)
axes[2,0].scatter(cc_dry[cc_date_to_use].values, ob_dry['yield (lbs/acre)'].values, axes[2,0].plot(ind1[bin_cond],ymax1[bin_cond],'r+:', label='max')
axes[2,0].plot(ind1[bin_cond],ypct1[bin_cond],'m+:', label='%d pct' % percentile_to_use)
axes[2,0].plot(ind1[bin_cond],ymed1[bin_cond],'g+:', label='med')
axes[2,0].plot(ind1,ymin1,'b+:', label='min')
axes[2,0].plot(xx,clf.predict(xx.reshape(len(xx),1)), 'c-', label='SVR')
axes[2,0].legend()
axes[2,0].grid(True)
####### Wet land
cc_wet = cc.loc[(cc['name'] == var_name) &
(cc['irrigation'] == "Yes") &
(cc['breeder'] != "Filler")].fillna(0)
ob_wet = ob.loc[(ob['name'] == var_name) &
(ob['irrigation'] == "Yes") &
(cc['breeder'] != "Filler")].fillna(0)
ind2,ysum2,yavg2,ymed2,ymin2,ymax2=cal_yield_potential(
cc_wet[cc_date_to_use].values,
ob_wet['yield (lbs/acre)'].values,
bin_size=bin_size_to_use,
bin_min=0,
bin_max=100)
ind2,ypct2 = cal_yield_potential_percentile(
cc_wet[cc_date_to_use].values,
ob_wet['yield (lbs/acre)'].values,
percentile_to_use,
bin_size = bin_size_to_use,
bin_min = 0,
bin_max = 100)
# Only use bins that we have values
bin_cond = ypct2 > 0
10
x=ind2[bin_cond]
y=ypct2[bin_cond]
xx = ar(range(100))
# Gaussian fitting
popt,pcov = curve_fit(gauss,x,y,p0=[y.max(),50,10])
axes[0,1].scatter(cc_wet[cc_date_to_use].values, ob_wet['yield (lbs/acre)'].values, axes[0,1].plot(ind2[bin_cond],ymax2[bin_cond],'r+:', label='max')
axes[0,1].plot(ind2[bin_cond],ypct2[bin_cond],'m+:', label='%d pct' % percentile_to_use)
axes[0,1].plot(ind2[bin_cond],ymed2[bin_cond],'g+:', label='med')
axes[0,1].plot(ind2,ymin2,'b+:', label='min')
axes[0,1].plot(xx,gauss(xx,*popt), 'c-', label='Gaussian')
axes[0,1].legend()
axes[0,1].grid(True)
# Skewed Gaussian fitting
model = SkewedGaussianModel()
params = model.make_params(amplitude=y.max(),
center=50,
sigma=10,
gamma=0)
result = model.fit(y, params, x=x)
axes[1,1].scatter(cc_wet[cc_date_to_use].values, ob_wet['yield (lbs/acre)'].values, axes[1,1].plot(ind2[bin_cond],ymax2[bin_cond],'r+:', label='max')
axes[1,1].plot(ind2[bin_cond],ypct2[bin_cond],'m+:', label='%d pct' % percentile_to_use)
axes[1,1].plot(ind2[bin_cond],ymed2[bin_cond],'g+:', label='med')
axes[1,1].plot(ind2,ymin2,'b+:', label='min')
axes[1,1].plot(xx, result.eval(x=xx), 'c-', label='Skwed')
axes[1,1].legend()
axes[1,1].grid(True)
# Support Vector Regression fitting
clf = SVR(kernel='rbf',C=1e3, gamma=0.001)
clf.fit(x.reshape(len(x),1), y)
axes[2,1].scatter(cc_wet[cc_date_to_use].values, ob_wet['yield (lbs/acre)'].values, axes[2,1].plot(ind2[bin_cond],ymax2[bin_cond],'r+:', label='max')
axes[2,1].plot(ind2[bin_cond],ypct2[bin_cond],'m+:', label='%d pct' % percentile_to_use)
axes[2,1].plot(ind2[bin_cond],ymed2[bin_cond],'g+:', label='med')
axes[2,1].plot(ind2,ymin2,'b+:', label='min')
11
axes[2,1].plot(xx,clf.predict(xx.reshape(len(xx),1)), 'c-', label='SVR')
axes[2,1].legend()
axes[2,1].grid(True)
if isinstance(var_name, numbers.Number):
out_fig_fn = "%d-%d_pct-bin_size_%d" % (var_name, percentile_to_use, bin_size_to_use)
else:
out_fig_fn = var_name + "-%d_pct-bin_size_%d" % (percentile_to_use, bin_size_to_use)
fig.suptitle(out_fig_fn)
# if isinstance(var_name, numbers.Number):
# out_fig_fn = "%d-%d_pct-bin_size_%d" % (var_name, percentile_to_use, bin_size_to_use)
# else:
# out_fig_fn = var_name + "-%d_pct-bin_size_%d" % (percentile_to_use, bin_size_to_use)
# fig.suptitle(out_fig_fn)
# fig.savefig(out_fig_fn)
# plt.close()
# print("Done..")
# Create a drop down list
w_var_dd = widgets.Dropdown(
options=cc_uniq_names,
description="Variety name: ",
disabled=False,
)
w_cc_dd = widgets.Dropdown(
options=cc.columns[14:20],
description="Dates: ",
disabled=False,
)
w_irr_dd = widgets.Dropdown(
options=['No', 'Yes', 'Both'],
values='No',
description='Irrigation: ',
disabled=False,
)
w_pct = widgets.IntSlider(
min=0,
max=100,
value=90,
step=1,
12
description='Percentile: ',
disabled=False,
continuous_update=False,
orientation='horizontal',
)
w_bin_size = widgets.BoundedFloatText(
value=5.0,
min=0,
max=20.0,
step=0.1,
description='Bin size to use:',
disabled=False
)
interact_manual(update_graph, var_name=w_var_dd,
cc_date_to_use=w_cc_dd,
irr=w_irr_dd,
percentile_to_use=w_pct);