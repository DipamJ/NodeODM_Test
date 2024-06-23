import os, sys
import glob
import rs2
import numpy as np
import operator
# Supress/hide the warning
np.seterr(invalid='ignore')
np.seterr(divide='ignore', invalid='ignore')
from osgeo import gdal, gdalnumeric, ogr, osr

# main function is used to debug and test the file (running it from the server)
def main():

	#run function with dummy data if ran from server
	get_msavi(32613, "/var/www/html/uas_data/uploads/products/2021_Corpus_Christi_Cotton_and_Corn/10/28/2021/SHAPE/2021_cc_brewer_plot_boundary_map_maturity_trial/2021_cc_brewer_plot_boundary_map_maturity_trial.shp", "/home/ubuntu/web/uas_data/download/product/2021_Corpus_Christi_Cotton_and_Corn/20210408_cc_p4r_parking_mosaic",  "20220523_cc_p4r_parking_mosaic", "")

#-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Name: get_msavi
# Function: generates msavi attribute and saves the results to its respect to what project/ orthomosiac are selected.
# Parameters: epsg - EPSG value of the selected orthomosiac
#             shp - path to the selected shape/boundary file selected
#             out_dir - path the othomosiac directory where the generated results are stored.

def get_msavi(espg, shp, out_dir, selected_orthomosaic_FileName_noExt, object_handle):
	print ("Generating MSAVI plot")

	selected_orthomosaic_FileName = selected_orthomosaic_FileName_noExt
	selected_boundary = os.path.basename(shp)
	selected_boundary_array = selected_boundary.split(".")
	selected_boundary_FileName_noExt = selected_boundary_array[0]
	gdal.UseExceptions()

	# Coordinate system
	sproj = osr.SpatialReference()
	sproj.ImportFromEPSG(int(espg))

	# input folder including msavi
	in_dir_msavi = os.path.join(out_dir, 'msavi')
	# files_msavi = glob.glob(in_dir_msavi + ('*' + 'msavi.dat'))
	files_msavi = glob.glob(os.path.join(in_dir_msavi, '*.dat'))
	files_msavi.sort()

	print(f'msavi dat file directory is: {in_dir_msavi}')
	print(files_msavi)

	# output folder
	out_dir = os.path.join(out_dir, 'msavi_boundary')
	if not os.path.exists(out_dir):
			os.mkdir(out_dir)

	# output shapefile name
	#out_msavi = os.path.join(out_dir, ('msavi_boundary_' + os.path.basename(shp)))
	#out_msavi = os.path.join(out_dir, ('msavi_boundary_' + selected_orthomosaic_FileName + "_" + selected_boundary_FileName_noExt + "_" + chm_name1 + ".shp"))
	out_msavi = os.path.join(out_dir, ('msavi_boundary_' + selected_orthomosaic_FileName + ".shp"))
	#print "Crop Shape"

	## shapefile open
	driver = ogr.GetDriverByName('ESRI Shapefile') #file type
	shapef = driver.Open(shp, 1)
	lyr = shapef.GetLayer()
	spatialRef = lyr.GetSpatialRef() # Get projection

	## Create the output shapefile
	outDriver = ogr.GetDriverByName('ESRI Shapefile')

	if os.path.exists(out_msavi):
		outDriver.DeleteDataSource(out_msavi)

	outDataSource_cc = outDriver.CreateDataSource(out_msavi)
	outLayer_cc = outDataSource_cc.CopyLayer(lyr, "AgriLife")
	out_fn_prj_cc = os.path.join(out_dir, os.path.splitext(out_msavi)[0] + '.prj')

	#print(out_msavi)
	#print(shp)
	#print(out_fn_prj_cc)

	spatialRef.MorphToESRI()
	file = open(out_fn_prj_cc, 'w')
	file.write(spatialRef.ExportToWkt())
	file.close()

	outDataSource_cc = None
	shapef = None

	# Create an OGR layer from a boundary shapefile
	driver = ogr.GetDriverByName('ESRI Shapefile') #file type
	shapef_out_cc = driver.Open(out_msavi, 1)
	ccLayer = shapef_out_cc.GetLayer()

	msavi_mean_defn = []
	for fn in files_msavi:
		basename = os.path.basename(fn)
		print("basename: ")
		print(basename)
		date_str = basename.split("20",1)[1].split("_",1)[0]
		#msavi_mean_defn.append(ogr.FieldDefn('avEG'+date_str, ogr.OFTReal))
		# msavi_mean_defn.append(ogr.FieldDefn( '20' + date_str, ogr.OFTReal))
		msavi_mean_defn.append(ogr.FieldDefn( 'avMS' + date_str, ogr.OFTReal))
		#msavi_mean_defn.append(ogr.FieldDefn('20'+date_str, ogr.OFTReal))

	#msavi_sd_defn = []
	#for fn in files_msavi:
		#basename = os.path.basename(fn)
		#date_str = basename.split("20",1)[1].split("_",1)[0]
		#msavi_sd_defn.append(ogr.FieldDefn('sdEG'+date_str, ogr.OFTReal))

	for tt in msavi_mean_defn:
		ccLayer.CreateField(tt)

	#for tt in msavi_sd_defn:
		#ccLayer.CreateField(tt)

	for i in range(len(files_msavi)):

		print ("Processing (%d/%d) [%.2f]" % (i+1, len(files_msavi), float(i+1) / len(files_msavi) * 100.0))

		# Create an OGR layer from a boundary shapefile
		driver = ogr.GetDriverByName('ESRI Shapefile') #file type
		shapef_out_cc = driver.Open(out_msavi, 1)
		ccLayer = shapef_out_cc.GetLayer()

		msavi_fn = files_msavi[i]

		basename = os.path.basename(msavi_fn)
		date_str = basename.split("20", 1)[1].split("_", 1)[0]

		print ("Image reading")

		msavi_img = rs2.RSImage(msavi_fn)

		print ("Extracting attribute")
		for crop_poly in ccLayer:

			#geoTrans = msavi_img.geotransform
			clipped_msavi = msavi_img.clip_by_polygon(crop_poly)

			## msavi mean and SD
			filtered_msavi = clipped_msavi[0,:,:]
			filtered_msavi = filtered_msavi[np.nonzero(filtered_msavi)]
			filtered_msavi = filtered_msavi[~np.isnan(filtered_msavi)]
			filtered_msavi = filtered_msavi[~np.isinf(filtered_msavi)]

			msavi_mean = np.mean((filtered_msavi))
			#msavi_sd = np.std((filtered_msavi))

			#crop_poly.SetField('avEG'+date_str, float(msavi_mean))
			# crop_poly.SetField('20' + date_str, float(msavi_mean))
			crop_poly.SetField('avMS' + date_str, float(msavi_mean))
			#crop_poly.SetField('20'+date_str, float(msavi_mean))
			#crop_poly.SetField('sdEG'+date_str, float(msavi_sd))

			ccLayer.SetFeature(crop_poly)

		#cc_img = None
		# chm_img = None
		msavi_img = None
		# msavi_img = None

	gdal.ErrorReset()
	shapef_out_cc = None

if __name__ == "__main__":
    main()
