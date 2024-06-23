import sys
import json
import pandas as pd

DIR = sys.argv[1]

# Create a list of unique elements
def unique(lst):
    u_list = []
    for element in lst:
        if 'BB' not in element and '?' not in element and element != 'B' and element not in u_list:
            u_list.append(element)
    return u_list


# Upload and read csv file
def upload_file(dir):

    global data_file, files, data
    files = []
    data_file = dir
    if not data_file:
        print("err:Error Reading File")
        return

    files.append(data_file)

    data = pd.read_csv(data_file)

    # print(data_file)

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
    print(json.dumps(varieties))


upload_file(DIR)
