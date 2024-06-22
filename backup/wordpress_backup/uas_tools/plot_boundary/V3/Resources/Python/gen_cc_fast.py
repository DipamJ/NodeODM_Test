#!/usr/bin/env python

import sys
import os
import rs
import numpy as np
from osgeo import gdal

###############
# parameters  #
###############
th1 = 0.95
th2 = 0.95
th3 = 20

# Input file name
in_fn = sys.argv[1]

# Open image without loading to memory
in_img = rs.RSImage(in_fn)

# Initialize output array
out_arr = np.zeros((1, in_img.ncol, in_img.nrow), dtype = np.uint8)

# Read bands
red = in_img.img[0,:,:].astype(np.float32)
green = in_img.img[1,:,:].astype(np.float32)
blue = in_img.img[2,:,:].astype(np.float32)

# Calculate index
# print ("Calculating index")
i1 = red / green
i2 = blue / green
i3 = 2*green - blue - red

# print ("Finding canopy only")
cond1 = i1 < th1
cond2 = i2 < th2
cond3 = i3 > th3

cond = cond1 * cond2 * cond3

grid_size = in_img.nrow * in_img.ncol
# grid_size = 0.0
# for row in range(in_img.nrow):
	
	# for col in range(in_img.ncol):
		# alpha = in_img.get_pixel(col, row, band=3)
		# if alpha == 255:
			# grid_size += 1.0

canopy_area = np.count_nonzero(cond) * 1.0

canopy_cover = ( canopy_area / grid_size ) * 100.0

out_dir = os.path.dirname(in_fn)
basename = os.path.basename(in_fn)
canopy_result_file_path = os.path.join(out_dir,os.path.splitext(basename)[0] + '_cc_result.txt')
file = open(canopy_result_file_path, "w")
file.write("%.2f" % canopy_cover)
file.close()

