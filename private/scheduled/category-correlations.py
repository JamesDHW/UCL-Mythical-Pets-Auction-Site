import mysql.connector
import numpy as np


def compute_correlations():
    try:
        con = mysql.connector.connect(user='database_admin', password='mythPetsAdmin', host='localhost',
                                      database='mythical_pets', unix_socket='/Applications/MAMP/tmp/mysql/mysql.sock')
    except mysql.connector.Error as er:
        print(er)
        return

    # get category combinations
    cur = con.cursor()
    query = 'SELECT mythologyID, animalClassID FROM mythologies JOIN animalClasses ' \
            'ORDER BY mythologyID, animalClassID ASC'
    cur.execute(query)
    categories = cur.fetchall()
    cur.close()

    # get all users that bid on at least one item
    # save the userID with its index in a dict for constant lookup time
    users = dict()
    cur = con.cursor()
    query = 'SELECT DISTINCT userID from bids ORDER BY userID ASC'
    cur.execute(query)
    for i, user_id in enumerate(cur):
        users[user_id[0]] = i
    cur.close()

    # for each category get the users that bid on an item of that category
    users_per_category = []
    query = 'SELECT DISTINCT b.userID FROM bids b INNER JOIN (SELECT itemID FROM items ' \
            'WHERE mythology = %s AND animalClass = %s) i ON b.itemID = i.itemID'
    for i, category in enumerate(categories):
        users_per_category.append([])
        cur = con.cursor()
        cur.execute(query, category)
        for user_id in cur:
            users_per_category[i].append(user_id[0])

    # initialize the user-category matrix
    user_len = len(users)
    categories_len = len(categories)
    user_cat = np.zeros((user_len, categories_len), dtype=np.int8)

    # fill the user-cat-matrix
    for i, category_users in enumerate(users_per_category):
        for user in category_users:
            user_cat[users[user], i] = 1

    # compute the category-category correlations
    cat_cat = np.identity(categories_len, dtype=np.float)
    for i in range(categories_len):
        for j in range(i + 1, categories_len):
            vec_a = user_cat[:, i]
            vec_b = user_cat[:, j]
            vector_dot = np.dot(vec_a, vec_b)
            vector_mag_prod = np.linalg.norm(vec_a) * np.linalg.norm(vec_b)
            if vector_mag_prod == 0:
                cat_cat[i, j] = 0
                cat_cat[j, i] = 0
            else:
                cosine = vector_dot/vector_mag_prod
                cat_cat[i, j] = cosine
                cat_cat[j, i] = cosine

    # store the result in the database
    cur = con.cursor()
    query = 'DELETE FROM catCatCorrelations'
    cur.execute(query)
    cur.close()

    query = 'INSERT INTO catCatCorrelations(mythologyIDRow, animalClassIDRow, ' \
            'mythologyIDCol, animalClassIDCol, correlation) VALUES (%s, %s, %s, %s, %s)'
    for i in range(categories_len):
        row_myth = categories[i][0]
        row_class = categories[i][1]
        for j in range(categories_len):
            col_myth = categories[j][0]
            col_class = categories[j][1]
            cur = con.cursor()
            cur.execute(query, (row_myth, row_class, col_myth, col_class, cat_cat.item((i, j))))
            cur.close()

    con.commit()
    con.close()


if __name__ == '__main__':
    compute_correlations()
