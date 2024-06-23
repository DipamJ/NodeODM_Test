#!/usr/bin/env python

import subprocess
import re
from pyproj import Proj, transform
import math

cs2cs = '/usr/bin/cs2cs'

#--------------------------------------------------------------
# Convert longitude and latitude into UTM coordinate
#--------------------------------------------------------------
def ll2utm2(lon, lat, zone):
    args =  ['+proj=latlong', '+ellps=WGS84',
             '+to',
             '+proj=utm', '+north', '+zone='+str(zone), '+units=m', '+ellps=WGS84']

    p = subprocess.Popen([cs2cs, '-f', '"%.15f"'] + args, stdin=subprocess.PIPE, stdout=subprocess.PIPE)

    p.stdin.write('%.5f %.5f\n' % (lon, lat))
    p.stdin.close()

    patt = re.compile(r'^"([\d\.\d]+)"\s+"([\d\.\d]+)"')
    response = patt.match(p.stdout.read())

    if response:
        return float(response.group(1)), float(response.group(2))


def ll2utm(lon, lat, zone):
    p1 = Proj(proj='latlong', ellps='WGS84')
    p2 = Proj(proj='utm', zone=zone, ellps='WGS84')
    x,y = transform(p1, p2, lon, lat)
    return x,y

def ll2xy(lon, lat, target_epsg):
    p1 = Proj(proj='latlong', ellps='WGS84')
    p2 = Proj(init='epsg:'+target_epsg)
    x,y = transform(p1, p2, lon, lat)
    return x,y

def deg2dms(dd):
    """
    Convert decimal degree to (degree, minute, second) format

    Input: decimal degree
    Output: (degree, minute, second)
    """
    is_positive = dd >= 0
    dd = abs(dd)
    minutes,seconds = divmod(dd*3600,60)
    degrees,minutes = divmod(minutes,60)
    degrees = degrees if is_positive else -degrees
    return (degrees,minutes,seconds)

def dms2deg(dd, mm, ss):
    """
    Convert degree, minute, second to decimal degree

    Input: dd -> degree
           mm -> minute
           ss -> second
    Output: decimal degree
    """

    return dd + mm / 60.0 + ss / 3600.0

def Rotate2D(ax,ay,ix,iy, angle):
    """
    Rotate a point around the anchor point
    ax, ay: anchor poinrt coordinates
    ix, iy: point coordinates that we will rotate
    angle: angle in degree (couter clockwise is positive)
    """
    x = ix - ax
    y = iy - ay
    angle_rad = math.radians(angle)
    resultx = (x * math.cos(angle_rad)) - (y * math.sin(angle_rad)) + ax
    resulty = (x * math.sin(angle_rad)) + (y * math.cos(angle_rad)) + ay
    return (resultx,resulty)
