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
	get_ndvi(32613, "/var/www/html/uas_data/uploads/products/2021_Corpus_Christi_Cotton_and_Corn/10/28/2021/SHAPE/2021_cc_brewer_plot_boundary_map_maturity_trial/2021_cc_brewer_plot_boundary_map_maturity_trial.shp", "/home/ubuntu/web/uas_data/download/product/2021_Corpus_Christi_Cotton_and_Corn/20210408_cc_p4r_parking_mosaic",  "20220523_cc_p4r_parking_mosaic", "")

#-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Name: get_ndvi
# Function: generates ndvi attribute and saves the results to its respect to what project/ orthomosiac are selected.
# Parameters: epsg - EPSG value of the selected orthomosiac
#             shp - path to the selected shape/boundary file selected
#             out_dir - path the othomosiac directory where the generated results are stored.

def get_ndvi(espg, shp, out_dir, selected_orthomosaic_FileName_noExt, object_handle):
	print ("Generating NDVI plot")

	selected_orthomosaic_FileName = selected_orthomosaic_FileName_noExt
	selected_boundary = os.path.basename(shp)
	selected_boundary_array = selected_boundary.split(".")
	selected_boundary_FileName_noExt = selected_boundary_array[0]
	gdal.UseExceptions()

	# Coordinate system
	sproj = osr.SpatialReference()
	sproj.ImportFromEPSG(int(espg))

	# input folder including ndvi
	in_dir_ndvi = os.path.join(out_dir, 'ndvi')
	# files_ndvi = glob.glob(in_dir_ndvi + ('*' + 'ndvi.dat'))
	files_ndvi = glob.glob(os.path.join(in_dir_ndvi, '*.dat'))
	files_ndvi.sort()

	print(f'ndvi dat file directory is: {in_dir_ndvi}')
	print(files_ndvi)

	# output folder
	out_dir = os.path.join(out_dir, 'ndvi_boundary')
	if not os.path.exists(out_dir):
			os.mkdir(out_dir)

	# output shapefile name
	#out_ndvi = os.path.join(out_dir, ('ndvi_boundary_' + os.path.basename(shp)))
	#out_ndvi = os.path.join(out_dir, ('ndvi_boundary_' + selected_orthomosaic_FileName + "_" + selected_boundary_FileName_noExt + "_" + chm_name1 + ".shp"))
	out_ndvi = os.path.join(out_dir, ('ndvi_boundary_' + selected_orthomosaic_FileName + ".shp"))
	#print "Crop Shape"

	## shapefile open
	driver = ogr.GetDriverByName('ESRI Shapefile') #file type
	shapef = driver.Open(shp, 1)
	lyr = shapef.GetLayer()
	spatialRef = lyr.GetSpatialRef() # Get projection

	## Create the output shapefile
	outDriver = ogr.GetDriverByName('ESRI Shapefile')

	if os.path.exists(out_ndvi):
		outDriver.DeleteDataSource(out_ndvi)

	outDataSource_cc = outDriver.CreateDataSource(out_ndvi)
	outLayer_cc = outDataSource_cc.CopyLayer(lyr, "AgriLife")
	out_fn_prj_cc = os.path.join(out_dir, os.path.splitext(out_ndvi)[0] + '.prj')

	#print(out_ndvi)
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
	shapef_out_cc = driver.Open(out_ndvi, 1)
	ccLayer = shapef_out_cc.GetLayer()

	ndvi_mean_defn = []
	for fn in files_ndvi:
		basename = os.path.basename(fn)
		print("basename: ")
		print(basename)
		date_str = basename.split("20",1)[1].split("_",1)[0]
		#ndvi_mean_defn.append(ogr.FieldDefn('avEG'+date_str, ogr.OFTReal))
		# ndvi_mean_defn.append(ogr.FieldDefn( '20' + date_str, ogr.OFTReal))
		ndvi_mean_defn.append(ogr.FieldDefn( 'avND' + date_str, ogr.OFTReal))
		#ndvi_mean_defn.append(ogr.FieldDefn('20'+date_str, ogr.OFTReal))

	#ndvi_sd_defn = []
	#for fn in files_ndvi:
		#basename = os.path.basename(fn)
		#date_str = basename.split("20",1)[1].split("_",1)[0]
		#ndvi_sd_defn.append(ogr.FieldDefn('sdEG'+date_str, ogr.OFTReal))

	for tt in ndvi_mean_defn:
		ccLayer.CreateField(tt)

	#for tt in ndvi_sd_defn:
		#ccLayer.CreateField(tt)

	for i in range(len(files_ndvi)):

		print ("Processing (%d/%d) [%.2f]" % (i+1, len(files_ndvi), float(i+1) / len(files_ndvi) * 100.0))

		# Create an OGR layer from a boundary shapefile
		driver = ogr.GetDriverByName('ESRI Shapefile') #file type
		shapef_out_cc = driver.Open(out_ndvi, 1)
		ccLayer = shapef_out_cc.GetLayer()

		ndvi_fn = files_ndvi[i]

		basename = os.path.basename(ndvi_fn)
		date_str = basename.split("20", 1)[1].split("_", 1)[0]

		print ("Image reading")

		ndvi_img = rs2.RSImage(ndvi_fn)

		print ("Extracting attribute")
		for crop_poly in ccLayer:

			#geoTrans = ndvi_img.geotransform
			clipped_ndvi = ndvi_img.clip_by_polygon(crop_poly)

			## ndvi mean and SD
			filtered_ndvi = clipped_ndvi[0,:,:]
			filtered_ndvi = filtered_ndvi[np.nonzero(filtered_ndvi)]
			filtered_ndvi = filtered_ndvi[~np.isnan(filtered_ndvi)]
			filtered_ndvi = filtered_ndvi[~np.isinf(filtered_ndvi)]

			ndvi_mean = np.mean((filtered_ndvi))
			#ndvi_sd = np.std((filtered_ndvi))

			#crop_poly.SetField('avEG'+date_str, float(ndvi_mean))
			# crop_poly.SetField('20' + date_str, float(ndvi_mean))
			crop_poly.SetField('avND' + date_str, float(ndvi_mean))
			#crop_poly.SetField('20'+date_str, float(ndvi_mean))
			#crop_poly.SetField('sdEG'+date_str, float(ndvi_sd))

			ccLayer.SetFeature(crop_poly)

		#cc_img = None
		# chm_img = None
		ndvi_img = None
		# ndvi_img = None

	gdal.ErrorReset()
	shapef_out_cc = None

if __name__ == "__main__":
    main()
