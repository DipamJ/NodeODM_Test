#!/usr/bin/env python3
# -*- coding: utf-8 -*-
# =============================================================================
# Created By  : Jose Luis Landivar
# Created Date: Wed October 6 18:00:00 CT 2021
# =============================================================================
"""The module has been build to create GIS objects (rectangles) and check intersection. Results are written into a text file (test.txt)"""
# =============================================================================
# Imports
# =============================================================================
import random
import sys

location = sys.argv[1]

# Open file
f = open('test.txt', 'w')

## 1. Declare the Point class
class Point:
#pass ## Implement me
    def __init__(self, x=0.0, y=0.0):
        self.x,self.y = x, y

    # def join(self):
    #     return(self.x, self.y)

## 2. Declare the Rectangle class
class Rectangle:
#pass ## Implement me
    def __init__(self, minX, minY, maxX, maxY):
        # Initialize rectangles
        self.minX, self.minY, self.maxX, self.maxY = minX, minY, maxX, maxY

f.write('\n')

f.write(location)
f.write('TEST')
f.write('\n')

# Writting message to text file
f.write("\nRandom points created below: ")
f.write('\n')

## 3. Generate four points
#print("\nRandom points created below: ")
# Creating a list
pointList = []
for i in range(4):
    x, y = random.random(), random.random()
    # Class instantiation
    point = Point(x,y)
    # Appending values from 'point' to the list
    pointList.append(point)
    # #printing all appended values
    #print(point.x , ',' , point.y)

    # Writting message to text file
    f.write('(')
    f.write(str(round(point.x, 2)))
    f.write(',')
    f.write(str(round(point.y, 2)))
    f.write(')\n')

# Writting message to text file
f.write("\nRandom rectangles created below: ")
f.write('\n')

#print("\nRandom rectangles created below: ")

## 4. Generate four rectangles
# Creating a list
rectangleList = []
for i in range(4):
     x1, y1, x2, y2 = random.random(), random.random(), random.random(), random.random()
     # Points that failed to create a rectangle. Generate their values again.
     while (x1 == x2 or y1 == y2):
         x1, y1, x2, y2 = random.random(), random.random(), random.random(), random.random()
     # Class instantiation
     rectangle = Rectangle(x1, y1, x2, y2)
     # Appending values from 'point' to the list
     rectangleList.append(rectangle)
     # #printing all appended values
     #print('Rectangle:',rectangle.minX, rectangle.minY, rectangle.maxX, rectangle.maxY)

     # Writting message to text file
     f.write('(')
     f.write(str(round(rectangle.minX,2)))
     f.write(',')
     f.write(str(round(rectangle.minY,2)))
     f.write(',')
     f.write(str(round(rectangle.maxX,2)))
     f.write(',')
     f.write(str(round(rectangle.maxY,2)))
     f.write(')\n')

f.write('\n')
for point in pointList:
     x, y = point.x, point.y
     #print('Point:', '(', x, ',', y, ')')

     # Writting message to text file
     f.write('Point: ')
     f.write('(')
     f.write(str(round(x,2)))
     f.write(',')
     f.write(str(round(y,2)))
     f.write(')')
     f.write('\n')

     # 5. Check which point is in which rectangle and record the result
     # #print if the (x, y) coordinates are inside the rectangle (true), or on or outside it (false)
     result = (((x2 > x > x1) or (x1 > x > x2)) and ((y2 > y > y1) or (y1 > y > y2)))
     #print(result,'\n')

     # 6. Write the results to file
     # Writting message to text file
     if not result:
         f.write('Point is NOT inside one of the rectangles.')
     elif result:
         f.write('Point is inside one of the rectangles.')

     f.write('\n\n')

f.close()
print('Please see test.txt to see the results.')
