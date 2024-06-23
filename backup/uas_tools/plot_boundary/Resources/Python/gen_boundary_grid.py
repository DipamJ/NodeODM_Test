#!/usr/bin/env python

from osgeo import ogr, osr
from proj import Rotate2D, ll2utm
import os
import numpy as np
import sys

# Input 
lat = sys.argv[1]
lon = sys.argv[2]
plot_width = float(sys.argv[3])
plot_height = float(sys.argv[4])

num_var_per_col = int(sys.argv[5])

off_set = [float(numeric_string) for numeric_string in sys.argv[6].split(',')]

vertical_shift = [float(numeric_string) for numeric_string in sys.argv[7].split(',')]


rotation_angle = float(sys.argv[8])


out_fn = sys.argv[9]

name = sys.argv[10]

grid_height = float(sys.argv[11])
num_grid = int(sys.argv[12])

clip_height = (plot_height  - grid_height * num_grid) / 2.0



zone = '14N'

lm,um = ll2utm(lon, lat, zone)


# Coordinate system
sproj = osr.SpatialReference()
sproj.ImportFromEPSG(32614) # UTM 14N WGS84


# Create shp file
driver = ogr.GetDriverByName('ESRI Shapefile')

# Delete file if already exists
if os.path.exists(out_fn):
    driver.DeleteDataSource(out_fn)

ds = driver.CreateDataSource(out_fn)

if ds is None:
    print ("Can not create a file")
    sys.exit(1)

# Layer
layer = ds.CreateLayer('cotton_plot', srs=sproj, geom_type=ogr.wkbPolygon)

# Fields
row_defn = ogr.FieldDefn('Row name', ogr.OFTString)
col_defn = ogr.FieldDefn('Col', ogr.OFTString)
grid_defn = ogr.FieldDefn('Grid', ogr.OFTString)

layer.CreateField(row_defn)
layer.CreateField(col_defn)
layer.CreateField(grid_defn)

for i in range(num_var_per_col):
	
	for j in range(num_grid):
		# Total number of lines before this plot
		total_nol_before_this_plot = int(i)
	
		# Now calculate corners
		x = lm + plot_width * total_nol_before_this_plot + np.sum(off_set[:total_nol_before_this_plot + 1])
		
		left = x
		right = x + plot_width
		#up = um + vertical_shift[i]
		up = um + vertical_shift[i] - clip_height - (grid_height) * j
		down = up - grid_height
		#down = um + vertical_shift[i]

		c0x, c0y = Rotate2D(lm, um, left, up, rotation_angle)
		c1x, c1y = Rotate2D(lm, um, right, up, rotation_angle)
		c2x, c2y = Rotate2D(lm, um, right, down, rotation_angle)
		c3x, c3y = Rotate2D(lm, um, left, down, rotation_angle)

		
		# Now generate polygon
		outring = ogr.Geometry(ogr.wkbLinearRing)
		outring.AddPoint(c0x, c0y)
		outring.AddPoint(c1x, c1y)
		outring.AddPoint(c2x, c2y)
		outring.AddPoint(c3x, c3y)
		outring.AddPoint(c0x, c0y)

		plot = ogr.Geometry(ogr.wkbPolygon)
		plot.AddGeometry(outring)

		featureDefn = layer.GetLayerDefn()
		feature = ogr.Feature(featureDefn)
		feature.SetGeometry(plot)
		feature.SetField('Row name', name)
		feature.SetField('Col', str(i+1))
		feature.SetField('Grid', str(j+1))
		
		layer.CreateFeature(feature)

		#plot.Destroy()
		#feature.Destroy()
 
ds.Destroy()
