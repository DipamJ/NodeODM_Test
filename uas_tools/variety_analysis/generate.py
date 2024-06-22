# argv[0] = file name
# argv[1] = INPUT csv file name
# argv[2] = DATE of planting
# argv[3] = TASK ['Compare Varieties', 'Generate Tabular Data - Row', 'Generate Tabular Data - Rep', 'Generate Growth Rate']
# argv[4] = Chart TYPE (bar/scatter)
# argv[5] = VARIETY 1
# argv[6] = VARIETY 2
# argv[7] = VARIETY 3
# argv[8] = VARIETY 4
# argv[9] = OUT file name


import os
import sys
import numpy as np
import pandas as pd
from matplotlib.figure import Figure
from datetime import datetime as dt

data_file      = ""
out_dir        = "./results"
var_col        = 'variety_name'
vals_to_plot   = []
vars_to_plot   = []
files          = []
varities_count = 4
widgets_width  = 25
widgets_height = 2
canvas         = None
data           = None
tasks          = ['Compare Varieties', 'Generate Tabular Data - Row', 'Generate Tabular Data - Rep', 'Generate Growth Rate'] # tasks performed using this gui:
                   # generate growth rate, generate tabular data, compare varieties,

fig = None
ax = None

DIR = sys.argv[1]
DAP = (sys.argv[2])
TASK = (sys.argv[3])
CHART = (sys.argv[4])
IN_VARIETIES = []
IN_VARIETIES.append(sys.argv[5])
IN_VARIETIES.append(sys.argv[6])
IN_VARIETIES.append(sys.argv[7])
IN_VARIETIES.append(sys.argv[8])
OUT_FILE = (sys.argv[9])

def unique(lst):
    u_list = []
    for element in lst:
        if 'BB' not in element and '?' not in element and element != 'B' and element not in u_list:
            u_list.append(element)
    return u_list

# Upload and read csv file
def upload_file(dir):
    # print("Will upload the file")

    global data_file, files, data
    files = []
    # data_file = askopenfilename(filetypes=[("Data Files", "*.csv"), ("All Files", "*.*")])
    data_file = dir
    # print(f'################################### {data_file} ###################################')
    if not data_file:
        print("Error reading the data file")
        return

    files.append(data_file)

    data = pd.read_csv(data_file)

    print (sys.argv)

    # cleanup data
    varietes_data = data.drop(['exper_name', 'row_name', 'col', 'plot_num'], axis = 1, inplace = False)
    varietes_data .fillna(0, inplace = True)

    # get the names of the varieties in the file
    varieties  = sorted(unique(varietes_data.variety_name))
    varieties.insert(0, 'variety')

    # for i in range(varities_count):
    #     varieties_drop_menu_var[i].set(varieties[0]) # default value
    #     varieties_drop_menu = tk.OptionMenu(varieties_pannel,
    #                             varieties_drop_menu_var[i],
    #                             *varieties)
    #     varieties_drop_menu.grid(row = i + 1, column = 0, sticky = "ew")
    #     print(f'The menu option is {varieties_drop_menu_var[i].get()}')


    # UI message
    data_filename = os.path.splitext(os.path.basename(data_file))[0][:] # get filename without extension
    # print(varieties)


def get_dap(df, day0):
    planting_date = dt.strptime(day0, "%Y%m%d")
    # timestamp = [dt.strptime('20' + i[2:], "%Y%m%d") for i in data.columns[1:]]
    print(f'********** {planting_date}')
    print(f'********** {data.columns[1]}')
    timestamp = [dt.strptime(i[:], "%Y%m%d") for i in df.columns[1:]]
    print(f'********** {timestamp}')
    days_after_planting = [(ts - planting_date).days  for ts in timestamp]
    return days_after_planting


def compare_varieties(data, var_col, out_dir):
    global canvas, fig, ax, data_file


    fig = Figure(figsize = (11, 7), dpi = 100)
    ax = fig.add_subplot(111)

    varietes_data = data.drop(['exper_name', 'row_name', 'col', 'plot_num'], axis = 1, inplace = False)
    dap = get_dap(varietes_data, DAP)

    print(f'The planting date is {DAP}')

    # generate data for plots
    vals_to_plot = []
    vars_to_plot = []
    for i in range(varities_count):
        if IN_VARIETIES[i] != 'variety':
            v_cc = varietes_data.loc[varietes_data [var_col] == IN_VARIETIES[i]]
            print(f'the menu var is {IN_VARIETIES[i]}')
            v_cc_mean = v_cc.mean()
            vals_to_plot.append(v_cc_mean)
            vars_to_plot.append(IN_VARIETIES[i])

    # figures formatting
    colors = ['b', 'g', 'r', 'y']

    # generating bar plots
    if CHART == 'bar':
        bar_locs = np.arange(len(varietes_data.columns[1:]))
        for i in range(len(vals_to_plot)):
            print('adding vals to ax')
            ax.bar(bar_locs+(0.2*i), vals_to_plot[i], width = 0.2, color = colors[i], align='center')
            ax.set_xticks(bar_locs)

    # generate point plots
    if CHART == 'scatter':
        point_locs = np.arange(len(varietes_data.columns[1:]))
        i = 0
        for v in vals_to_plot:
            print('adding vals to ax')
            ax.plot(dap, vals_to_plot[i], colors[i] + 'o')
            ax.set_xticks(dap)
            i = i + 1

    # set the labels and legend for the figures
    x_labels = [i[4:] for i in varietes_data.columns[1:]]
    x_labels = [x_labels[i] + '\n' + str(dap[i]) for i in range(len(varietes_data.columns[1:]))]
    ax.set_xticklabels(x_labels, rotation = 0, fontsize = 8)
    ax.set_title('Mean Values for Selected Varieties')
    ax.set_xlabel('Date and DAP')
    f = os.path.splitext(os.path.basename(data_file))[0][7:] # get the filename to get the attribute type: 2021_... _cc.csv --> canopy cover
    if "cc" in f:
        print(f'^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Hello ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^')
        ax.set_ylabel('CC Mean Values')
    elif "exg" in f:
        print(f'^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Here ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^')
        ax.set_ylabel('ExG Mean Values')
    elif "ch" in f:
        ax.set_ylabel('CH Mean Values')
    elif "cv" in f:
        ax.set_ylabel('CV Mean Values')
    else:
        ax.set_ylabel('Unknown Attribute Mean Values')
    ax.legend(labels = vars_to_plot, loc = 'upper left') # legend placed at lower right

    all_plotted_means = np.array(vals_to_plot).T
    result_data = pd.DataFrame(all_plotted_means, vals_to_plot[0].index, vars_to_plot)

    result_data.to_csv('results/' + OUT_FILE + '.csv', index = True)
    fig.savefig('results/' + OUT_FILE + '.png', dpi=100)


def generate_growth_data(data, var_col, out_dir):
    global canvas, fig, ax

    # if all(varieties_drop_menu_var[i].get() == 'variety' for i in range(varities_count)):
    #     lbl_msg["text"] = "Select variety(s) ..."
    #     return

    # rd_btn_var.set('x')

    fig = Figure(figsize = (9, 7), dpi = 100)
    ax = fig.add_subplot(111)

    varietes_data = data.drop(['exper_name', 'row_name', 'col', 'plot_num'], axis = 1, inplace = False)
    dap = get_dap(varietes_data, DAP)

    print(f'The planting date is {DAP}')

    # generate data for plots
    vals_to_plot = []
    vars_to_plot = []
    growth_to_plot = []
    for i in range(varities_count):
        if IN_VARIETIES[i] != 'variety':
            v_cc = varietes_data.loc[varietes_data [var_col] == IN_VARIETIES[i]]
            print(f'the menu var is {IN_VARIETIES[i]}')
            v_cc_mean = v_cc.mean()
            # v_cc_mean_diff = [(v_cc_mean[i] - v_cc_mean[i - 1])/(dap[i] - dap[i - 1]) + v_cc_mean[i]  for i in range(1, len(v_cc_mean))] # growth rate = delta(y)/delta(x)
            v_cc_mean_diff = [(v_cc_mean[i] - v_cc_mean[i - 1])/(dap[i] - dap[i - 1])  for i in range(1, len(v_cc_mean))]
            vals_to_plot.append(v_cc_mean)
            vars_to_plot.append(IN_VARIETIES[i])
            growth_to_plot.append(v_cc_mean_diff)

    # figures formatting
    colors = ['b', 'g', 'r', 'y']
    bar_locs = np.arange(len(varietes_data.columns[1:]))

    # generating bar plots
    bar_locs = np.arange(len(varietes_data.columns[1:]))
    for i in range(len(vals_to_plot)):
        print('adding vals to ax')
        ax.plot([dap[i] for i in range(1, len(dap))], growth_to_plot[i], color = colors[i], marker = 'o', markersize = 8)

    # set the labels and legend for the figures
    x_labels = [i[4:] for i in varietes_data.columns[1:]]
    x_labels = [x_labels[i] + '\n' + str(dap[i]) for i in range(len(varietes_data.columns[1:]))]
    ax.set_xticks(dap)
    ax.set_xticklabels(x_labels, rotation = 0, fontsize = 8)
    ax.set_title('Growth Rate for Selected Varieties')
    ax.set_xlabel('Date and DAP')
    f = os.path.splitext(os.path.basename(data_file))[0][7:] # get the filename to get the attribute type: 2021_... _cc.csv --> canopy cover
    if "cc" in f:
        ax.set_ylabel('CC Growth Rate')
    elif "exg" in f:
        print(f'^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Here ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^')
        ax.set_ylabel('ExG Growth Rate')
    elif "ch" in f:
        ax.set_ylabel('CH Growth Rate')
    elif "cv" in f:
        ax.set_ylabel('CV Growth Rate')
    else:
        ax.set_ylabel('Unknown Attribute Growth Rate')

    ax.legend(labels = vars_to_plot, loc = 'upper left') # legend placed at lower right

    # # creating the Tkinter canvas containing the Matplotlib figure
    # canvas = FigureCanvasTkAgg(fig, master = right_pannel)
    # canvas.draw()

    # # placing the canvas on the Tkinter window
    # canvas.get_tk_widget().pack()

    # # creating the Matplotlib toolbar
    # toolbar = NavigationToolbar2Tk(canvas, right_pannel, pack_toolbar = False)
    # toolbar.update()

    all_plotted_means = np.array(vals_to_plot).T
    result_data = pd.DataFrame(all_plotted_means, vals_to_plot[0].index, vars_to_plot)

    result_data.to_csv('results/' + OUT_FILE + '.csv', index = True)
    fig.savefig('results/' + OUT_FILE + '.png', dpi=100)


# this function exports the average value for all the varieties based on the reps
# in a tabular form
def generate_tabular_data(data, var_col, out_dir, level):
    # same variety has 4 plots, one plot has 2 rows

    # if len(files) == 0:
    #     lbl_msg["text"] = "Upload a csv file ..."
    #     return

    # dispaly a message if no plant date is provided
    # if date_var.get() == 'YYYYMMDD' or date_var.get() == '':
    #     lbl_msg["text"] = "Enter flight date ..."
    #     return

    # if rd_btn_var.get() == 'x':
    #     lbl_msg["text"] = "Select chart type ..."
    #     return

    if not all(IN_VARIETIES[i] == 'variety' for i in range(varities_count)):
        all(IN_VARIETIES[i] for i in range(varities_count))

    if level == tasks[1]: # per row data
        # reps_data = data.drop(['row_name', 'col', 'join_key'], axis = 1, inplace = False)
        in_date = DAP
        reps_data = data.drop(['exper_name', 'row_name', 'col'], axis = 1, inplace = False)
        sub = reps_data[[var_col, 'plot_num', in_date]]
        sub_grouped = sub.groupby([var_col, 'plot_num'])
        # sub_grouped_mean = sub.groupby([var_col]).mean()
        # max_reps = 6
        dicts = {}
        for group, values in sub_grouped:
            values = values[in_date].tolist()
            rows_mean = sum(values)/len(values)
            rows_std = np.std(np.array(values), ddof = 0) # TODO: check which std to use!
            rows_cv = rows_mean/rows_std
            # >>> reps_std
            # 0.10265642285659388
            # reps_std = np.std(np.array(reps_vals), ddof = 1)
            # >>> reps_std
            # 0.11853742674059706
            values.append(rows_mean)
            values.append(rows_std)
            values.append(rows_cv)
            values = ["%0.4f" % round(i, 4) for i in values] # round to and show 4 decimal points
            print(group)
            # print(values)
            if group[0] == 'B':
                continue
            # for v in values:
            #     vals_list.append(v)
            #     print(v, end = ',')
            # for i in range(max_reps - len(vals_list)):
            #     vals_list.append('null')
            dicts[group] = values
            print('\n-----------------------------------------------------------')
        # print(type(group))
        # print(type(values))
        col_names = ['Row ' + str(i + 1) for i in range(len(values) - 3)]
        col_names.append('Mean')
        col_names.append('STD')
        col_names.append('Coef. of Variability')

        print(f'TASK: {level}')
        print(dicts)

        table_data = pd.DataFrame.from_dict(dicts, orient = 'index', columns = col_names)

        print(table_data.head())

        table_data.to_csv('results/' + OUT_FILE + '.csv', index = True, index_label = 'Variety and Plot Number')

    if level == tasks[2]: # per_rep_data
        in_date = DAP
        reps = [i[0] for i in data['plot_num']] # get the rep value for the each plot
        data['reps'] = reps # add a column to represent the reps
        reps_data = data.drop(['exper_name', 'row_name', 'col', 'plot_num'], axis = 1, inplace = False) # discard columns not needed
        sub = reps_data[[var_col, 'reps', in_date]] # get the data for the user-specified date
        sub_grouped = sub.groupby([var_col, 'reps']).sum() # get the sum of the plant rows for each variety
        sub_grouped.drop(['B'], inplace = True) # remove boundaries

        # for i in sub_grouped.itertuples():
        #    print(i[0])
        #    print(i[1])
        #
        # indx = sub_grouped.index
        # cols = list(sub_grouped)
        # sub_grouped['20210408'][indx[0]]
        #
        # u_var = unique(sub['var_col'])


        # new_sub = sub.set_index(var_col)
        # i = new_sub.index[0]
        # x = new_sub.loc[i, in_date]
        # dict[i] = x.tolist()

        dict = {}
        v = [t[0] for t in sub_grouped.index]
        unique_varieties = unique(v)
        for variety in unique_varieties:
            print(f'######## {variety} ########')
            reps_vals = sub_grouped.loc[variety, in_date].tolist()
            reps_mean = sum(reps_vals)/len(reps_vals)
            reps_std = np.std(np.array(reps_vals), ddof = 0) # TODO: check which std to use!
            reps_cv = reps_mean/reps_std
            # >>> reps_std
            # 0.10265642285659388
            # reps_std = np.std(np.array(reps_vals), ddof = 1)
            # >>> reps_std
            # 0.11853742674059706
            reps_vals.append(reps_mean)
            reps_vals.append(reps_std)
            reps_vals.append(reps_cv)
            reps_vals = ["%0.4f" % round(i, 4) for i in reps_vals] # round to and show 4 decimal points
            dict[variety] = reps_vals

        col_names = ['Rep ' + str(i + 1) for i in range(len(reps_vals) - 3)]
        col_names.append('Mean')
        col_names.append('STD')
        col_names.append('Coef. of Variability')
        table_data = pd.DataFrame.from_dict(dict, orient = 'index', columns = col_names)

        table_data.to_csv('results/' + OUT_FILE + '.csv', index = True, index_label = 'Variety')



def generate():
    global data, var_col, vals_to_plot, vars_to_plot, canvas, ax, fig

    # print(f'>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>generate: {canvas}>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>')
    # # if canvas != None:
    # #     print('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> CANVAS IS NOT NONE >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>')
    # #     canvas.delete('all')
    # # canvas.delete('all')
    #
    if canvas != None:
        print('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> generate: CANVAS IS NOT NONE >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>')
        # for item in canvas.get_tk_widget().find_all():
        #     canvas.get_tk_widget().delete(item)
        ax.axes.clear()
        ax.axes.figure.canvas.draw()
        canvas.get_tk_widget().destroy()

    # check that a file is uploaded
    if len(files) == 0:
        print("Upload a csv file ...")
        return

    # dispaly a message if no plant date is provided
    if DAP == 'YYYYMMDD' or DAP == '':
        print("Enter planting date ...")
        return

    if TASK == tasks[0]: # comparing varieties
        # if all(varieties_drop_menu_var[i].get() == 'variety' for i in range(varities_count)):
        #     lbl_msg["text"] = "Select variety(s) ..."
        #     return

        # if rd_btn_var.get() == 'x':
        #     lbl_msg["text"] = "Select chart type ..."
        #     return

        compare_varieties(data, var_col, out_dir)

    if TASK == tasks[1]: # generate tabular data at the row level
        print(TASK)
        generate_tabular_data(data, var_col, out_dir, tasks[1])

    if TASK == tasks[2]: # generate tabular data at the rep level
        print(TASK)
        generate_tabular_data(data, var_col, out_dir, tasks[2])

    if TASK == tasks[3]: # generate growth rates
        # print(TASK)
        generate_growth_data(data, var_col, out_dir)


# print(str(sys.argv))
upload_file(DIR)
generate()