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
	get_ndre(32613, "/var/www/html/uas_data/uploads/products/2021_Corpus_Christi_Cotton_and_Corn/10/28/2021/SHAPE/2021_cc_brewer_plot_boundary_map_maturity_trial/2021_cc_brewer_plot_boundary_map_maturity_trial.shp", "/home/ubuntu/web/uas_data/download/product/2021_Corpus_Christi_Cotton_and_Corn/20210408_cc_p4r_parking_mosaic",  "20220523_cc_p4r_parking_mosaic", "")

#-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Name: get_ndre
# Function: generates ndre attribute and saves the results to its respect to what project/ orthomosiac are selected.
# Parameters: epsg - EPSG value of the selected orthomosiac
#             shp - path to the selected shape/boundary file selected
#             out_dir - path the othomosiac directory where the generated results are stored.

def get_ndre(espg, shp, out_dir, selected_orthomosaic_FileName_noExt, object_handle):
	print ("Generating NDRE plot")

	selected_orthomosaic_FileName = selected_orthomosaic_FileName_noExt
	selected_boundary = os.path.basename(shp)
	selected_boundary_array = selected_boundary.split(".")
	selected_boundary_FileName_noExt = selected_boundary_array[0]
	gdal.UseExceptions()

	# Coordinate system
	sproj = osr.SpatialReference()
	sproj.ImportFromEPSG(int(espg))

	# input folder including ndre
	in_dir_ndre = os.path.join(out_dir, 'ndre')
	# files_ndre = glob.glob(in_dir_ndre + ('*' + 'ndre.dat'))
	files_ndre = glob.glob(os.path.join(in_dir_ndre, '*.dat'))
	files_ndre.sort()

	print(f'ndre dat file directory is: {in_dir_ndre}')
	print(files_ndre)

	# output folder
	out_dir = os.path.join(out_dir, 'ndre_boundary')
	if not os.path.exists(out_dir):
			os.mkdir(out_dir)

	# output shapefile name
	#out_ndre = os.path.join(out_dir, ('ndre_boundary_' + os.path.basename(shp)))
	#out_ndre = os.path.join(out_dir, ('ndre_boundary_' + selected_orthomosaic_FileName + "_" + selected_boundary_FileName_noExt + "_" + chm_name1 + ".shp"))
	out_ndre = os.path.join(out_dir, ('ndre_boundary_' + selected_orthomosaic_FileName + ".shp"))
	#print "Crop Shape"

	## shapefile open
	driver = ogr.GetDriverByName('ESRI Shapefile') #file type
	shapef = driver.Open(shp, 1)
	lyr = shapef.GetLayer()
	spatialRef = lyr.GetSpatialRef() # Get projection

	## Create the output shapefile
	outDriver = ogr.GetDriverByName('ESRI Shapefile')

	if os.path.exists(out_ndre):
		outDriver.DeleteDataSource(out_ndre)

	outDataSource_cc = outDriver.CreateDataSource(out_ndre)
	outLayer_cc = outDataSource_cc.CopyLayer(lyr, "AgriLife")
	out_fn_prj_cc = os.path.join(out_dir, os.path.splitext(out_ndre)[0] + '.prj')

	#print(out_ndre)
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
	shapef_out_cc = driver.Open(out_ndre, 1)
	ccLayer = shapef_out_cc.GetLayer()

	ndre_mean_defn = []
	for fn in files_ndre:
		basename = os.path.basename(fn)
		print("basename: ")
		print(basename)
		date_str = basename.split("20",1)[1].split("_",1)[0]
		#ndre_mean_defn.append(ogr.FieldDefn('avEG'+date_str, ogr.OFTReal))
		# ndre_mean_defn.append(ogr.FieldDefn( '20' + date_str, ogr.OFTReal))
		ndre_mean_defn.append(ogr.FieldDefn( 'avRE' + date_str, ogr.OFTReal))
		#ndre_mean_defn.append(ogr.FieldDefn('20'+date_str, ogr.OFTReal))

	#ndre_sd_defn = []
	#for fn in files_ndre:
		#basename = os.path.basename(fn)
		#date_str = basename.split("20",1)[1].split("_",1)[0]
		#ndre_sd_defn.append(ogr.FieldDefn('sdEG'+date_str, ogr.OFTReal))

	for tt in ndre_mean_defn:
		ccLayer.CreateField(tt)

	#for tt in ndre_sd_defn:
		#ccLayer.CreateField(tt)

	for i in range(len(files_ndre)):

		print ("Processing (%d/%d) [%.2f]" % (i+1, len(files_ndre), float(i+1) / len(files_ndre) * 100.0))

		# Create an OGR layer from a boundary shapefile
		driver = ogr.GetDriverByName('ESRI Shapefile') #file type
		shapef_out_cc = driver.Open(out_ndre, 1)
		ccLayer = shapef_out_cc.GetLayer()

		ndre_fn = files_ndre[i]

		basename = os.path.basename(ndre_fn)
		date_str = basename.split("20", 1)[1].split("_", 1)[0]

		print ("Image reading")

		ndre_img = rs2.RSImage(ndre_fn)

		print ("Extracting attribute")
		for crop_poly in ccLayer:

			#geoTrans = ndre_img.geotransform
			clipped_ndre = ndre_img.clip_by_polygon(crop_poly)

			## ndre mean and SD
			filtered_ndre = clipped_ndre[0,:,:]
			filtered_ndre = filtered_ndre[np.nonzero(filtered_ndre)]
			filtered_ndre = filtered_ndre[~np.isnan(filtered_ndre)]
			filtered_ndre = filtered_ndre[~np.isinf(filtered_ndre)]

			ndre_mean = np.mean((filtered_ndre))
			#ndre_sd = np.std((filtered_ndre))

			#crop_poly.SetField('avEG'+date_str, float(ndre_mean))
			# crop_poly.SetField('20' + date_str, float(ndre_mean))
			crop_poly.SetField('avRE' + date_str, float(ndre_mean))
			#crop_poly.SetField('20'+date_str, float(ndre_mean))
			#crop_poly.SetField('sdEG'+date_str, float(ndre_sd))

			ccLayer.SetFeature(crop_poly)

		#cc_img = None
		# chm_img = None
		ndre_img = None
		# ndre_img = None

	gdal.ErrorReset()
	shapef_out_cc = None

if __name__ == "__main__":
    main()
