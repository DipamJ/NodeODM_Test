import numpy as np
from scipy.optimize import curve_fit

def richard5(t, beta, li, tm, k, T):
    """
    5 parameter richard function

    Reference: http://www.pisces-conservation.com/growthhelp/index.html?richards_curve.htm

    Input
    =====
    beta: lower asymptote
    li: upper asymptote
    tm: time of maximum growth
    k: growth rate
    T: variable which fixed the point of inflection
    """
    y = beta + (li) / ( (1 + T * np.exp(-k * (t - tm)))**(1/T) )
    return y

    
def fit_richard5(dap, measurements, init_param = [0,100,60,3,1], last_day = 120):
    """
    Fitting 5 parameter richard function function

    """
    # Check dimension first
    if not (dap.shape == measurements.shape):
        print ("Input arrray dimension does not match.")
        return None

    # Now fit sigmoid curve
    try:    
        popt, pcov = curve_fit(richard5, dap, measurements, p0=init_param)
    except RuntimeError:
        print ("Skipping since it fails to fit sigmoid curve")
        return None, None, None, None

    # Now generate curve
    x = np.linspace(0, last_day, last_day + 1)
    y = richard5(x, *popt)
    
    return x,y, popt, pcov
    

def richard4(t, li, k, gamma, delta):
    """
    4 parameter richard function

    Reference: http://www.pisces-conservation.com/growthhelp/index.html?richards_curve.htm

    Input
    =====
    li: upper asymptote
    k: growth rate
    gamma: point of inflection on the x axis
    delta: parameter that in part determines the point of inflection on the y axis

    """
    y = li * (1 + (delta-1) * np.exp(-k*(t-gamma)))**(1/(1-delta))
    return y

def fit_richard4(dap, measurements, init_param = [100,3,60,1], last_day = 120):
    """
    Fitting 4 parameter richard function function
    """
    # Check dimension first
    if not (dap.shape == measurements.shape):
        print ("Input arrray dimension does not match.")
        return None

    # Now fit sigmoid curve
    try:    
        popt, pcov = curve_fit(richard4, dap, measurements, p0=init_param)
    except RuntimeError:
        print ("Skipping since it fails to fit sigmoid curve")
        return None, None, None, None

    # Now generate curve
    x = np.linspace(0, last_day, last_day + 1)
    y = richard4(x, *popt)
    
    return x,y, popt, pcov
    

def logistic(x, v, tau, mu, sigma, lho):
    """
    Assymetric logistic function
    """
    y = v + tau * (1 + np.exp((x-mu)/sigma))**(-lho)
    return y

def sigmoid(x, a, b, c):
    """
    Sigmoid function that is often used to model plant growth
    """
    y = a / (1 + np.exp(-(x-c)/b))
    return y

def fit_logistic(dap, measurements, init_param = [0,100,30,3,1], last_day = 120):
    """
    Fitting a logistic function
    """
    # Check dimension first
    if not (dap.shape == measurements.shape):
        print ("Input arrray dimension does not match.")
        return None

    # Now fit sigmoid curve
    try:    
        popt, pcov = curve_fit(logistic, dap, measurements, p0=init_param)
    except RuntimeError:
        print ("Skipping since it fails to fit sigmoid curve")
        return None, None, None, None

    # Now generate curve
    x = np.linspace(0, last_day, last_day + 1)
    y = logistic(x, *popt)
    
    return x,y, popt, pcov
    
def fit_sigmod(dap, measurements, init_param = [1, 3, 25], last_day = 120):
    """
    Fitting a sigmod curve to the measurements over the growing season
    
    Input
    =====
    dap: date after planing, this should be a numpy array.
    measurements: any measurements you would like to fit, this should be a numpy array as well.
    Note: Dimension of dap and measurements should be identical.
    """

    # Check dimension first
    if not (dap.shape == measurements.shape):
        print ("Input arrray dimension does not match.")
        return None

    # Now fit sigmoid curve
    try:    
        popt, pcov = curve_fit(sigmoid, dap, measurements, p0=init_param)
    except RuntimeError:
        print ("Skipping since it fails to fit sigmoid curve")
        return None, None, None, None

    # Now generate curve
    x = np.linspace(0, last_day, last_day + 1)
    y = sigmoid(x, *popt)
    
    return x,y, popt, pcov

def find_half_max_location(in_arr):
    """
    Find half maximum value and location after interpolating inbetween
    """
    half_max = in_arr.max() / 2.0

    # Find first point whose value is greater than half max
    for i in range(in_arr.shape[0]):
        if in_arr[i] > half_max:
            break

    if i == in_arr.shape[0] - 1:
        print ("Failed to find half max location")
        return None
        
    # Now find half max location
    m = in_arr[i] - in_arr[i-1]

    y_off = half_max - in_arr[i]
    x_off = y_off / m

    return i + x_off

    
def find_half_max_location_backward(in_arr):
    """
    Find half maximum value and location after interpolating inbetween
    but staring from the end of the array
    """
    half_max = in_arr.max() / 2.0

    # Find first point whose value is greater than half max
    for i in range(in_arr.shape[0]-1, 0, -1):
        if in_arr[i] > half_max:
            break

    if (i == 0) or (i == in_arr.shape[0] - 1):
        print ("Failed to find half max location")
        return None
    # Now find half max location
    m = in_arr[i] - in_arr[i+1]

    y_off = in_arr[i] - half_max
    x_off = y_off / m

    return i + x_off

def numerical_first_derivative(x,y):
    """
    Calculate numerical first derivative given x,y values
    Assuming that the x array is uniform, meaning that 1 day apart for all values.

    For example, x = [0,1,2,3,4, ... ,120]
    """
    # Initialize output array
    yy = np.zeros(y.shape[0], dtype=y.dtype)

    # Do first and last array
    yy[0] = (y[1] - y[0]) / (x[1] - x[0])
    yy[-1] = (y[-1] - y[-2]) / (x[-1] - x[-2])

    for i in range(1,y.shape[0]-1):
        # value before
        val1 = y[i-1] + (y[i] - y[i-1]) / 2.0
        val2 = y[i] + (y[i+1] - y[i]) / 2.0
        yy[i] = (val2 - val1)

    return yy
    